<?php

namespace App\Exports;

use App\Support\Report\CreditReportBuilder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CreditReportExport implements FromQuery, WithHeadings, WithChunkReading
{

    private $start_date;
    private $end_date;
    public function __construct(
        string $start_date,
        string $end_date
    ) {
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }

    public function query()
    {
        $CreditReportBuilder = new CreditReportBuilder();

        return $CreditReportBuilder->build(
            $this->start_date,
            $this->end_date
        );
    }

    public function chunkSize(): int
    {
        return env('MAX_CHUNK_SIZE', 2500);
    }

    public function headings(): array
    {
        return [
            'Id',
            'Nombre Completo',
            'Documento',
            'Email',
            'Teléfono',
            'Compañía',
            'Tipo deuda',
            'Situación',
            'Atraso',
            'Entidad',
            'Monto total',
            'Línea total',
            'Línea usada',
            'Reporte subido el',
            'Estado',
        ];
    }
}
