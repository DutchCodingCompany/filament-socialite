<?php

namespace DutchCodingCompany\FilamentSocialite\Traits;

trait Routes
{
    protected ?string $loginRouteName = null;

    protected ?string $dashboardRouteName = null;

    public function getRoute(): string
    {
        return "socialite.{$this->getPanel()->generateRouteName('oauth.redirect')}";
    }

    public function loginRouteName(string $value): static
    {
        $this->loginRouteName = $value;

        return $this;
    }

    public function getLoginRouteName(): string
    {
        return $this->loginRouteName ?? $this->getPanel()->generateRouteName('auth.login');
    }

    public function dashboardRouteName(string $value): static
    {
        $this->dashboardRouteName = $value;

        return $this;
    }

    public function getDashboardRouteName(): string
    {
        return $this->dashboardRouteName ?? $this->getPanel()->generateRouteName('pages.dashboard');
    }
}
