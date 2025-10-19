<div
        x-data="{}"
        x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('filament-socialite-styles', package: 'filament-socialite'))]"
>
    <div class="flex flex-col gap-y-6">
        @if ($messageBag->isNotEmpty())
            @foreach($messageBag->all() as $value)
                <p class="fi-fo-field-wrp-error-message text-danger-600 dark:text-danger-400">{{ __($value) }}</p>
            @endforeach
        @endif

        @if (count($visibleProviders))
            @if($showDivider && !$showButtonsBeforeLogin)
                @include('filament-socialite::components.divider')
            @endif

            <div class="grid @if(count($visibleProviders) > 1) grid-cols-2 @endif gap-4">
                @foreach($visibleProviders as $key => $provider)
                    <x-filament::button
                            :color="$provider->getColor()"
                            :outlined="$provider->getOutlined()"
                            :icon="$provider->getIcon()"
                            tag="a"
                            :href="route($socialiteRoute, $key)"
                            :spa-mode="false"
                    >
                        {{ $provider->getLabel() }}
                    </x-filament::button>
                @endforeach
            </div>
            @if($showDivider && $showButtonsBeforeLogin)
                @include('filament-socialite::components.divider')
            @endif
        @else
            <span></span>
        @endif
    </div>
</div>
