<?php

namespace DutchCodingCompany\FilamentSocialite\Traits;

use App\Models\User;
use DutchCodingCompany\FilamentSocialite\Exceptions\ImplementationException;
use DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser as FilamentSocialiteUserContract;
use DutchCodingCompany\FilamentSocialite\Models\SocialiteUser;
use Illuminate\Contracts\Auth\Authenticatable;

trait Models
{
    /**
     * @var class-string<\Illuminate\Contracts\Auth\Authenticatable>
     */
    protected string $userModelClass = User::class;

    /**
     * @var class-string<\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser>
     */
    protected string $socialiteUserModelClass = SocialiteUser::class;

    /**
     * @param class-string<\Illuminate\Contracts\Auth\Authenticatable> $value
     * @throws ImplementationException
     */
    public function userModelClass(string $value): static
    {
        if (! is_a($value, Authenticatable::class, true)) {
            throw new ImplementationException('The user model class must implement the "\Illuminate\Contracts\Auth\Authenticatable" interface.');
        }

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
        if (! is_a($value, FilamentSocialiteUserContract::class, true)) {
            throw new ImplementationException('The socialite user model class must implement the "\DutchCodingCompany\FilamentSocialite\Models\Contracts\FilamentSocialiteUser" interface.');
        }

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
