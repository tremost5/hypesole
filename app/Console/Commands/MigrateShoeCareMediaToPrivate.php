<?php

namespace App\Console\Commands;

use App\Models\ShoeCareOrderItem;
use App\Models\ShoeCareShipment;
use App\Models\ShoeCareShipmentEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateShoeCareMediaToPrivate extends Command
{
    protected $signature = 'HYPESOLE:media-to-private {--dry-run : Only report files without copying}';
    protected $description = 'Copy legacy Shoe Care photos from public storage to private storage without deleting originals';

    public function handle(): int
    {
        $paths = [];
        ShoeCareOrderItem::query()->select(['id','before_photos','after_photos'])->chunkById(100, function ($items) use (&$paths) {
            foreach ($items as $item) {
                foreach (array_merge($item->before_photos ?? [], $item->after_photos ?? []) as $path) $paths[] = $path;
            }
        });
        ShoeCareShipment::query()->whereNotNull('proof_photo_path')->pluck('proof_photo_path')->each(fn ($p) => $paths[] = $p);
        ShoeCareShipmentEvent::query()->whereNotNull('proof_photo_path')->pluck('proof_photo_path')->each(fn ($p) => $paths[] = $p);
        $paths = array_values(array_unique(array_filter($paths)));
        $copied = 0; $missing = 0; $already = 0;
        foreach ($paths as $path) {
            if (Storage::disk('private')->exists($path)) { $already++; continue; }
            if (! Storage::disk('public')->exists($path)) { $missing++; $this->warn("Missing: {$path}"); continue; }
            if ($this->option('dry-run')) { $this->line("Would copy: {$path}"); continue; }
            $stream = Storage::disk('public')->readStream($path);
            if (! is_resource($stream)) { $missing++; continue; }
            try {
                Storage::disk('private')->writeStream($path, $stream);
                $copied++;
            } finally {
                if (is_resource($stream)) fclose($stream);
            }
        }
        $this->info('Referenced paths: '.count($paths)."; copied: {$copied}; already private: {$already}; missing: {$missing}");
        if ($this->option('dry-run')) $this->comment('Dry run only. No files were copied.');
        else $this->comment('Original public files were deliberately retained. Remove public exposure only after verifying every copied file and server storage configuration.');
        return $missing ? self::FAILURE : self::SUCCESS;
    }
}
