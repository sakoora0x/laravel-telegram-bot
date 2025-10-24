<?php

namespace sakoora0x\Telegram;

use sakoora0x\Telegram\Commands\InitCommand;
use sakoora0x\Telegram\Commands\LiveCommand;
use sakoora0x\Telegram\Commands\NewBotCommand;
use sakoora0x\Telegram\Commands\PoolingCommand;
use sakoora0x\Telegram\Commands\SetWebhookCommand;
use sakoora0x\Telegram\Commands\TruncateCommand;
use sakoora0x\Telegram\Commands\UnsetWebhookCommand;
use sakoora0x\Telegram\Components\EditForm;
use sakoora0x\Telegram\Providers\AuthServiceProvider;
use sakoora0x\Telegram\Providers\RouteServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TelegramServiceProvider extends PackageServiceProvider
{
    public function boot(): static
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);

        return parent::boot();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('telegram')
            ->hasConfigFile('telegram')
            ->hasViews('telegram')
            ->hasRoutes('api')
            ->hasMigrations([
                'create_telegram_bots_table',
                'create_telegram_chats_table',
                'create_telegram_users_table',
                'create_telegram_attachments_table',
            ])
            ->hasCommands([
                NewBotCommand::class,
                InitCommand::class,
                SetWebhookCommand::class,
                UnsetWebhookCommand::class,
                PoolingCommand::class,
                TruncateCommand::class,
                LiveCommand::class,
            ])
            ->hasViewComponent('telegram', EditForm::class)
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->publish('routes');
            });

        $this->publishes([
            $this->package->basePath("../stubs/routes/telegram.php.stub") => base_path('routes/telegram.php'),
        ], "{$this->package->shortName()}-routes");

        $this->loadViewsFrom(resource_path('views/telegram'), 'telegram');

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
    }
}
