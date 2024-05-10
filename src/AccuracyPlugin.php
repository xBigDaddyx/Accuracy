<?php

namespace Xbigdaddyx\Accuracy;

use Xbigdaddyx\HarmonyFlow\Filament\Resources\AccuracyResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\BuyerResource;
use Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\CartonBoxResource;
use Xbigdaddyx\Accuracy\Filament\Accuracy\Resources\PackingListResource;

class AccuracyPlugin implements Plugin
{

    protected bool $hasBuyerResource = true;
    protected bool $hasCartonBoxResource = true;
    protected bool $hasPackingListResource = true;

    public function hasBuyerResource(bool $condition = true): static
    {
        $this->hasBuyerResource = $condition;

        return $this;
    }
    public function hasCartonBoxResource(bool $condition = true): static
    {
        $this->hasCartonBoxResource = $condition;

        return $this;
    }
    public function hasPackingListResource(bool $condition = true): static
    {
        $this->hasPackingListResource = $condition;

        return $this;
    }

    public function getId(): string
    {
        return 'accuracy';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasBuyerResource() && $this->hasCartonBoxResource() && $this->hasPackingListResource()) {
            $panel
                ->resources([
                    BuyerResource::class,
                    CartonBoxResource::class,
                    PackingListResource::class
                ]);
        }
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
