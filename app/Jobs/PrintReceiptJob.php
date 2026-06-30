<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Services\PrintService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PrintReceiptJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, Dispatchable, SerializesModels;

    public $transaction;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle(PrintService $printService): void
    {
        try {
            $printService->printReceipt($this->transaction);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('PrintReceiptJob failed: ' . $e->getMessage());
        }
    }
}
