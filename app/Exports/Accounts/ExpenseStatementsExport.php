<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExpenseStatementsExport implements FromCollection, WithHeadings, ShouldAutoSize
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
    	$sumCredit = 0;

    	foreach ($ledgers as $key => $row) {

    		$dataFormat[] = [
								'S/L' => ++$key,
								'PARTICULARS' => $row['sub_code_title2'],
								'NOTES' => $row['sub_code2'],
								'VALUE IN TAKA' => $row['debit_amount'],
    						];

                    $sumCredit = $sumCredit + $row['debit_amount'];    						
    	}

    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Total: ',
							'NOTES' => '',
							'VALUE IN TAKA' => number_format($sumCredit,2),
    					];

		return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'S/L',
            'PARTICULARS',
            'NOTES',
            'VALUE IN TAKA',
        ];
    }    
}
