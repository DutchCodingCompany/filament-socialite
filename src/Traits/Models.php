<?php

namespace DutchCodingCompany\FilamentSocialite\Traits;

use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;

trait Models
{
    /**
     * @var class-string<\Illuminate\Contracts\Auth\Authenticatable>
     */
    protected string $userModelClass = \App\Models\User::class;

    /**
     * @var class-string<\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser>
     */
    protected string $socialiteUserModelClass = SocialiteUser::class;

    /**
     * @param class-string<\Illuminate\Contracts\Auth\Authenticatable> $value
     */
    public function userModelClass(string $value): static
    {
        $this->userModelClass = $value;

        return $this;
    }

    /**
     * @return class-string<\Illuminate\Contracts\Auth\Authenticatable>
     */
    public function getUserModelClass(): string
    {
        return $this->userModelClass;
    }

    /**
     * @param class-string<\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser> $value
     */
    public function socialiteUserModelClass(string $value): static
    {
        $this->socialiteUserModelClass = $value;

        return $this;
    }

    /**
     * @return class-string<\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser>
     */
    public function getSocialiteUserModelClass(): string
    {
        return $this->socialiteUserModelClass;
    }

    public function getSocialiteUserModel(): FilamentSocialiteUserContract
    {
        return new ($this->getSocialiteUserModelClass());
    }
}
