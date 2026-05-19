<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupOldExports extends Command
{
    protected $signature = 'ml:cleanup-old-exports {--days=30 : Keep files newer than this many days}';
    protected $description = 'Clean up old training data export files';

    public function handle()
    {
        $days = (int) $this->option('days');
        $cutoffDate = now()->subDays($days);
        $deletedCount = 0;

        $mlPath = storage_path('ml');

        if (!File::exists($mlPath)) {
            $this->info('No ML directory found.');
            return Command::SUCCESS;
        }

        $files = File::files($mlPath);

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoffDate->timestamp) {
                File::delete($file);
                $deletedCount++;
            }
        }

        $this->info("✅ Deleted {$deletedCount} old export files older than {$days} days");

        return Command::SUCCESS;
    }
}