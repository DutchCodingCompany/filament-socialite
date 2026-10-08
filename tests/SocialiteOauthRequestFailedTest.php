<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use DutchCodingCompany\FilamentSocialite\Events\OauthRequestFailed;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as PsrRequest;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

class SocialiteOauthRequestFailedTest extends TestCase
{
    #[DataProvider('failedRequestProvider')]
    public function testFailedProviderRequestRedirectsToLogin(Throwable $exception): void
    {
        Event::fake();

        $this
            ->getJson(route("socialite.filament.{$this::getPanelName()}.oauth.redirect", ['provider' => 'github']))
            ->assertStatus(302);

        $state = session()->get('state');

        $provider = \Mockery::mock(
            AbstractProvider::class,
            [request()->merge(['state' => $state]), 'test-client-id', 'test-client-secret', 'test-redirect-url']
        )
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $provider->shouldReceive('getAccessTokenResponse')->andThrow($exception);

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn($provider);

        $this
            ->get(route("socialite.filament.{$this::getPanelName()}.oauth.callback", ['provider' => 'github', 'state' => $state]))
            ->assertRedirect(route("filament.{$this::getPanelName()}.auth.login"));

        Event::assertDispatched(
            OauthRequestFailed::class,
            fn (OauthRequestFailed $event) => $event->provider === 'github' && $event->exception === $exception,
        );

        $this->assertDatabaseCount('socialite_users', 0);
    }

    /**
     * @return array<string, array{0: Throwable}>
     */
    public static function failedRequestProvider(): array
    {
        return [
            'User denied access (Guzzle client error)' => [
                new ClientException(
                    'invalid_request',
                    new PsrRequest('POST', 'https://github.com/login/oauth/access_token'),
                    new PsrResponse(400, [], '{"error":"invalid_request"}'),
                ),
            ],
            'Provider refuses the user (Laravel HTTP client error)' => [
                new RequestException(new Response(new PsrResponse(403, [], '{"message":"Forbidden"}'))),
            ],
            'Provider is unreachable' => [
                new ConnectionException('Could not resolve host'),
            ],
        ];
    }
}
