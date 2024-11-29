<?php

namespace DutchCodingCompany\FilamentSocialite;

use Closure;
use DutchCodingCompany\FilamentSocialite\Traits\CanBeHidden;
use Filament\Support\Colors\Color;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Str;

class Provider
{
    use EvaluatesClosures;
    use CanBeHidden;

    protected string $name;

    protected string $label;

    protected string | null $icon = null;

    /**
     * @var string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    protected string | array | null $color = Color::Gray;

    protected bool $outlined = true;

    /**
     * @var \Closure(): array<mixed> | array<mixed>
     */
    protected Closure | array $scopes = [];

    /**
     * @var \Closure(): array<mixed> | array<mixed>
     */
    protected Closure | array $with = [];

    protected bool $stateless = false;

    public function __construct(string $name)
    {
        $this->name($name);
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function fill(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->{$key}($value);
        }

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label ?? Str::title($this->getName());
    }

    public function icon(string | null $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): string | null
    {
        return $this->icon;
    }

    /**
     * @param string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null $color
     */
    public function color(string | array | null $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return string | array{50: string, 100: string, 200: string, 300: string, 400: string, 500: string, 600: string, 700: string, 800: string, 900: string, 950: string} | null
     */
    public function getColor(): string | array | null
    {
        return $this->color;
    }

    public function outlined(bool $outlined = true): static
    {
        $this->outlined = $outlined;

        return $this;
    }

    public function getOutlined(): bool
    {
        return $this->outlined;
    }

    /**
     * @param Closure(): array<mixed> | array<mixed> $scopes
     */
    public function scopes(Closure | array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getScopes(): array
    {
        return $this->evaluate($this->scopes, ['provider' => $this]);
    }

    /**
     * @param Closure(): array<mixed> | array<mixed> $with
     */
    public function with(Closure | array $with): static
    {
        $this->with = $with;

        return $this;
    }

    /**
     * @return array<mixed>
     */
    public function getWith(): array
    {
        return $this->evaluate($this->with, ['provider' => $this]);
    }

    public function stateless(bool $stateless = true): static
    {
        $this->stateless = $stateless;

        return $this;
    }

    public function getStateless(): bool
    {
        return $this->stateless;
    }
}
