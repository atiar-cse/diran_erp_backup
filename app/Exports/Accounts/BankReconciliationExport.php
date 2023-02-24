<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class BankReconciliationExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
    	$ledgers = $this->data;

    	$dataFormat = [];

    	foreach ($ledgers as $key => $row) {

            $dataFormat[] = [
                                'S/L' => ++$key,
                                'DATE' => $row['date'],
                                'PARTICULAR' => $row['type'],
                                'DEBIT' => $row['debit'],
                                'CREDIT' => $row['credit'],
                                'BALANCE' => $row['balance'],
                            ];    						
    	}

		return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'S/L',
            'DATE',
            'PARTICULAR',
            'DEBIT',
            'CREDIT',
            'BALANCE',
        ];
    }    
}
