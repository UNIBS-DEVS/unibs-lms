<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Batch;

class BatchCreatedMail extends TenantMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $batchId;
    public string $createdBy;

    public function __construct(int $batchId, string $createdBy, ?string $clientCode)
    {
        if (!$clientCode) {
            throw new \Exception('Client code missing while dispatching mail.');
        }

        parent::__construct($clientCode);

        $this->batchId = $batchId;
        $this->createdBy = $createdBy;
    }

    public function build()
    {
        $this->configureTenantMail();

        $batch = Batch::with('customer')->findOrFail($this->batchId);

        return $this->subject('New Batch Created: ' . $batch->name)
            ->view('emails.batch_created', [
                'batch' => $batch,
                'createdBy' => $this->createdBy,
            ]);
    }
}
