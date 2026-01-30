<?php

namespace App\Jobs;

use App\Exports\CreditReportExport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Facades\Excel;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    public string $start_date;
    public string $end_date;

    public string $file_name;

    public function __construct(string $start_date, string $end_date, string $file_name)
    {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->file_name = $file_name;
    }

    public function handle(): void
    {

        Excel::store(
            new CreditReportExport($this->start_date, $this->end_date),
            'reports/' . $this->file_name,
            'local'
        );
    }
}
