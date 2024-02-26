<?php

namespace DutchCodingCompany\FilamentSocialite\Tests;

use DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestSocialiteUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Mockery;

class SocialiteLoginTest extends TestCase
{
    use RefreshDatabase;

    public function testLogin(): void
    {

        $response = $this
            ->getJson("/$this->panelName/oauth/github")
            ->assertStatus(302);

        $state = session()->get('state');

        $location = $response->headers->get('location');

        parse_str($location, $urlQuery);

        // Test if the correct state is sent to the endpoint in the "Location" header.
        $this->assertEquals($state, $urlQuery['state']);

        // Assert decrypting of the state gives the correct panel name.
        $this->assertEquals($this->panelName, Crypt::decrypt($state));

        Socialite::shouldReceive('driver')
            ->with('github')
            ->andReturn(
                Mockery::mock(Provider::class)
                    ->shouldReceive('user')
                    ->andReturn(new TestSocialiteUser())
                    ->getMock()
            );

        // Fake oauth response.
        $response = $this
            ->getJson("/oauth/callback/github?state=$state")
            ->assertStatus(302);

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
