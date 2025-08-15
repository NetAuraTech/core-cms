<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Support\Facades\Artisan;
use Netauratech\CoreCms\Contracts\BackupProviderInterface;

class BackupProvider implements BackupProviderInterface
{
    /**
     * Executes the backup process.
     *
     * This method should perform the complete backup workflow,
     * including creating the backup files and optionally
     * performing cleanup of existing backups.
     *
     * @param array $optionsBackup  Array of options related to backup creation.
     * @param array $optionsCleanup Array of options related to backup cleanup.
     *
     * @return void
     */
    public function run(array $optionsBackup, array$optionsCleanup): void
    {
        Artisan::call('core-cms:backup-run', $optionsBackup);
        Artisan::call('core-cms:backup-clean', $optionsCleanup);
    }
}