<?php

namespace Netauratech\CoreCms\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Netauratech\CoreCms\Contracts\BackupProviderInterface;

class BackupCmsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core-cms:backup {--only-db} {--disable-notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database and files';

    protected BackupProviderInterface $backupProvider;

    public function __construct(BackupProviderInterface $backupProvider)
    {
        parent::__construct();
        $this->backupProvider = $backupProvider;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $optionsBackup = [];
        $optionsCleanup = [];
        $prefix = "full_";

        if ($this->option('only-db')) {
            $optionsBackup['--only-db'] = true;
            $prefix = "db_";
        }

        if ($this->option('disable-notifications')) {
            $optionsBackup['--disable-notifications'] = true;
            $optionsCleanup['--disable-notifications'] = true;
        }

        config(['backup.backup.destination.filename_prefix' => $prefix]);

        try {
            $this->backupProvider->run($optionsBackup, $optionsCleanup);
        } finally {
            $tempPath = config('backup.backup.temporary_directory');
            if (is_dir($tempPath)) {
                File::deleteDirectory($tempPath);
            }
        }
    }
}
