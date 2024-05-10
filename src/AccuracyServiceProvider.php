<?php

namespace Xbigdaddyx\Accuracy;

use App\Application;
use Illuminate\Support\ServiceProvider;
use Xbigdaddyx\Accuracy\Observers\ApprovalObserver;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Xbigdaddyx\Accuracy\Commands\ApprovalCommand;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;
use Xbigdaddyx\Accuracy\Livewire\Comment;
use Xbigdaddyx\Accuracy\Livewire\Status;
use Illuminate\Support\Facades\Gate;
use RingleSoft\LaravelProcessApproval\Events\ProcessApprovedEvent;
use Xbigdaddyx\Accuracy\Events\CartonBoxStatusUpdated;
use Xbigdaddyx\Accuracy\Listeners\ApprovalApproved;
use Xbigdaddyx\Accuracy\Listeners\CartonBoxStatusListener;
use Xbigdaddyx\Accuracy\Livewire\components\AccuracyPolybagAttributes;
use Xbigdaddyx\Accuracy\Livewire\components\AccuracyPolybagStats;
use Xbigdaddyx\Accuracy\Livewire\components\AccuracyPolybagTable;
use Xbigdaddyx\Accuracy\Livewire\RevisionsPaginator;
use Xbigdaddyx\Accuracy\Livewire\SearchCarton;
use Xbigdaddyx\Accuracy\Livewire\VerificationCarton;
use Xbigdaddyx\Accuracy\Livewire\Version;


class AccuracyServiceProvider extends PackageServiceProvider
{
    public static string $name = 'Accuracy';

    public static string $viewNamespace = 'accuracy';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)

            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('xbigdaddyx/Accuracy');
            })
            ->hasViews(static::$viewNamespace);

        $configFileName = $package->shortName();
        if (file_exists($package->basePath("/../routes/web.php"))) {
            $package->hasRoutes("web");
        }
        if (file_exists($package->basePath("/../routes/api.php"))) {
            $package->hasRoutes("api");
        }

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../database/migrations'))) {
            $package->hasMigrations($this->getMigrations());
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }
    protected function getCommands(): array
    {
        return [
            //
        ];
    }
    protected function getMigrations(): array
    {
        return [
            // '20230109_1032_create_Accuracy_types_table',
            // '20230109_1033_create_Accuracy_documents_table'
        ];
    }
    public function packageRegistered(): void
    {
        $this->app->bind('VerificationRepository', \Xbigdaddyx\Accuracy\Repositories\VerificationRepository::class);
        $this->app->bind('SearchRepository', \Xbigdaddyx\Accuracy\Repositories\SearchRepository::class);
        // $this->app->register(AccuracyEventServiceProvider::class);
    }

    public function packageBooted(): void
    {
        Event::listen(CartonBoxStatusUpdated::class, CartonBoxStatusListener::class);
        $this->callAfterResolving(BladeCompiler::class, function () {

            if (class_exists(Livewire::class)) {
                Livewire::component('search-carton', SearchCarton::class);
                Livewire::component('verification-carton', VerificationCarton::class);
                Livewire::component('accuracy-polybag-attributes', AccuracyPolybagAttributes::class);
                Livewire::component('accuracy-polybag-stats', AccuracyPolybagStats::class);
                Livewire::component('accuracy-polybag-table', AccuracyPolybagTable::class);
                // Livewire::component('status', Status::class);
                // Livewire::component('paginator', RevisionsPaginator::class);
                // Livewire::component('version', Version::class);
            }
        });
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName(),

        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Handle Stubs
        // if (app()->runningInConsole()) {
        //     foreach (app(Filesystem::class)->files(__DIR__ . '/../stubs/') as $file) {
        //         $this->publishes([
        //             $file->getRealPath() => base_path("stubs/Accuracy/{$file->getFilename()}"),
        //         ], 'Accuracy-stubs');
        //     }
        // }

        // Testing
        // Testable::mixin(new TestsApproval());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'xbigdaddyx/Accuracy';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // Css::make('Accuracy-paginator-styles', __DIR__ . '/../resources/dist/paginator.css'),
            // // AlpineComponent::make('filament-approvals', __DIR__ . '/../resources/dist/components/filament-approvals.js'),
            // Css::make('Accuracy-styles', __DIR__ . '/../resources/dist/Accuracy.css'),
            // Js::make('Accuracy-scripts', __DIR__ . '/../resources/dist/Accuracy.js'),
        ];
    }

    protected function getIcons(): array
    {
        return [];
    }


    protected function getRoutes(): array
    {
        return [];
    }


    protected function getScriptData(): array
    {
        return [];
    }
}
