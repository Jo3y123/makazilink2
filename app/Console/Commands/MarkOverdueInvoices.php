<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;

class MarkOverdueInvoices extends Command
{
    protected $signature   = 'invoices:mark-overdue';
    protected $description = 'Mark unpaid invoices as overdue when past due date';

    public function handle()
    {
        $count = Invoice::whereIn('status', ['draft', 'sent', 'partial'])
            ->whereDate('due_date', '<', today())
            ->update(['status' => 'overdue']);

        $this->info("Marked {$count} invoice(s) as overdue.");

        return Command::SUCCESS;
    }
}