<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\PageInvite;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('page-invites:update-handles', function () {
    $this->info('Starting PageInvite handle update...');
    
    $pageInvites = PageInvite::all();
    $totalCount = $pageInvites->count();
    
    if ($totalCount === 0) {
        $this->info('No PageInvite records found.');
        return;
    }
    
    $this->info("Found {$totalCount} PageInvite records to update.");
    
    $bar = $this->output->createProgressBar($totalCount);
    $bar->start();
    
    $updatedCount = 0;
    $errors = [];
    
    foreach ($pageInvites as $pageInvite) {
        try {
            $oldHandle = $pageInvite->handle;
            
            // Generate new unique handle for this page
            do {
                $newHandle = Str::random(8);
            } while (PageInvite::where('page_id', $pageInvite->page_id)
                ->where('handle', $newHandle)
                ->where('id', '!=', $pageInvite->id)
                ->exists());
            
            $pageInvite->update(['handle' => $newHandle]);
            $this->line("\nUpdated: Page {$pageInvite->page_id} - User {$pageInvite->user_id} - Handle: {$oldHandle} → {$newHandle}");
            
            $updatedCount++;
            
        } catch (\Exception $e) {
            $errors[] = "Error updating PageInvite ID {$pageInvite->id}: " . $e->getMessage();
        }
        
        $bar->advance();
    }
    
    $bar->finish();
    $this->newLine(2);
    
    $this->info("Successfully updated {$updatedCount} PageInvite handles");
    
    if (!empty($errors)) {
        $this->error('Errors encountered:');
        foreach ($errors as $error) {
            $this->error("- {$error}");
        }
    }
    
    // Verify uniqueness
    $this->info('Verifying handle uniqueness...');
    $duplicates = PageInvite::select('page_id', 'handle')
        ->groupBy('page_id', 'handle')
        ->havingRaw('COUNT(*) > 1')
        ->get();
    
    if ($duplicates->isEmpty()) {
        $this->info('✅ All handles are unique within their pages');
    } else {
        $this->error('❌ Found duplicate handles:');
        foreach ($duplicates as $duplicate) {
            $this->error("- Page {$duplicate->page_id}: {$duplicate->handle}");
        }
    }
    
    // Show some examples of the new handles
    $this->info('Sample of updated handles:');
    PageInvite::inRandomOrder()->limit(5)->get(['page_id', 'user_id', 'handle'])->each(function ($invite) {
        $this->line("- Page {$invite->page_id} - User {$invite->user_id}: {$invite->handle}");
    });
    
})->purpose('Update all existing PageInvite handles to unique random strings');
