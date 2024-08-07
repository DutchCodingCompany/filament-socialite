<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestSocialiteUser;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestTeam;
use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestTenantUser;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use LogicException;
use Mockery;

class SocialiteTenantLoginTest extends TestCase
{
    protected string $userModelClass = TestTenantUser::class;

    protected array $tenantArguments = [
        TestTeam::class,
    ];

    public function testTenantLogin(): void
    {
        FilamentSocialitePlugin::current()->redirectAfterLoginUsing(function (string $provider, FilamentSocialiteUserContract $socialiteUser, FilamentSocialitePlugin $plugin) {
            assert($socialiteUser instanceof SocialiteUser);

            $this->assertEquals($this::getPanelName(), $plugin->getPanel()->getId());
            $this->assertEquals('github', $provider);
            $this->assertEquals('github', $socialiteUser->provider);
            $this->assertEquals('test-socialite-user-id', $socialiteUser->provider_id);

            return redirect()->to('/some-tenant-url');
        });

        $response = $this
            ->getJson("/{$this::getPanelName()}/oauth/github")
            ->assertStatus(302);

        $state = session()->get('state');

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn(static::makeOAuthProviderMock(
                request()->merge(['state' => $state]),
                new TestSocialiteUser()
            ));

        // Fake oauth response.
        $response = $this
            ->getJson("/oauth/callback/github?state=$state")
            ->assertStatus(302);

        $this->assertStringContainsString('/some-tenant-url', $response->headers->get('Location') ?? throw new LogicException('Location header not set.'));

        $this->assertDatabaseHas('socialite_users', [
            'provider' => 'github',
            'provider_id' => 'test-socialite-user-id',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test-socialite-user-name',
            'email' => 'test@example.com',
        ]);
    }
}
