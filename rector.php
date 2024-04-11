<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Utils\Rector\Rector\RenameMethods;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        'app/Providers/Filament/',
    ]);

    $rectorConfig->ruleWithConfiguration(
        \Rector\Renaming\Rector\MethodCall\RenameMethodRector::class,
        [
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setProviders",
                "providers",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setRegistrationEnabled",
                "registration",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setSlug",
                "slug",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setLoginRouteName",
                "loginRouteName",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setDashboardRouteName",
                "dashboardRouteName",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setRememberLogin",
                "rememberLogin",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setSocialiteUserModelClass",
                "socialiteUserModelClass",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setDomainAllowList",
                "domainAllowList",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setUserModelClass",
                "userModelClass",
            ),
            new \Rector\Renaming\ValueObject\MethodCallRename(
                "DutchCodingCompany\\FilamentSocialite\\FilamentSocialitePlugin",
                "setShowDivider",
                "showDivider",
            ),
        ]
    );
};
