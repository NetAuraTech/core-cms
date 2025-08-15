<?php

namespace Netauratech\CoreCms\Contracts;

interface BackupProviderInterface
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
    public function run(array $optionsBackup, array $optionsCleanup): void;
}