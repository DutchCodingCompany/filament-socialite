<?php

namespace DutchCodingCompany\FilamentSocialite\Tests\Fixtures;

use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @property Collection<\DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestTeam> $teams
 */
class TestTenantUser extends TestUser implements HasTenants
{
    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams->contains($tenant);
    }

    /**
     * @return \Illuminate\Support\Collection<int, \DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestTeam>
     */
    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<\DutchCodingCompany\FilamentSocialite\Tests\Fixtures\TestTeam, $this>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(TestTeam::class, 'team_user', 'user_id', 'team_id');
    }
}
