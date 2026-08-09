<?php

namespace App\Console\Commands;

use App\Models\FeedEvent;
use Illuminate\Console\Command;

class PruneOldFeedEvents extends Command
{
    protected $signature   = 'pulse:prune';
    protected $description = 'Delete expired feed events';

    public function handle(): void
    {
        $deleted = FeedEvent::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Pruned {$deleted} expired feed events.");
    }
}