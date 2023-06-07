<?php

namespace UisIts\Oidc\Console;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class ShibbolethInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shibboleth:install
    {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Shibboleth OIDC/SAML components.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Install Spatie-Permissions package
        $this->requireComposerPackages('spatie/laravel-permission');

        // Publish Spatie-Permission ServiceProvider
        $this->callSilent('vendor:publish', ['--tag' => 'permission-config', '--force' => true]);
        $this->callSilent('vendor:publish', ['--tag' => 'permission-migrations', '--force' => true]);

        // publish config file
        $this->callSilent('vendor:publish', ['--tag' => 'shib-config', '--force' => true]);
        $this->info('Successfully published shibboleth configuration to: '.config_path('shibboleth.php'));
        $this->newLine();

        // publish migration file
        $this->callSilent('vendor:publish', ['--tag' => 'shib-migrations', '--force' => true]);
        $this->info('Successfully published shibboleth migrations to: '.database_path());
        $this->newLine();

        $this->info('Please run your migrations using:');
        $this->warn('php artisan migrate');
    }

    protected function requireComposerPackages($packages)
    {
        $composer = $this->option('composer');

        if ($composer !== 'global') {
            $command = [$this->phpBinary(), $composer, 'require'];
        }

        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : func_get_args()
        );

        return ! (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(function ($type, $output) {
                $this->output->write($output);
            });
    }
}
