<?php

namespace NetAuraTech\CoreCms\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'cms:install';
    protected $description = 'Installe le CMS pour la première fois.';

    public function handle()
    {
        $this->info('Installation du CMS en cours...');

        // Exécuter les migrations de tous les packages
        $this->call('migrate', ['--force' => true]);

        // Exécuter les seeders de base
        $this->call('db:seed', ['--force' => true]);

        // Publier les assets des packages
        $this->call('vendor:publish', ['--force' => true, '--tag' => 'core-cms-assets']);

        $this->info('Installation terminée !');
    }
}