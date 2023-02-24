<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class TrialBalanceExport implements FromCollection, WithHeadings, ShouldAutoSize
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
    	$sumDebit = $sumCredit = 0;

    	foreach ($ledgers as $row) {


    		$dataFormat[] = [
    							'SUB CODE2'     => $row['sub2_title'],
    							'ACCOUNT NAME' => $row['chart_of_accounts_title'],
    							'DEBIT'        => $row['total_debit'],
    							'CREDIT'       => $row['total_credit'],
    						];

                    $sumDebit = $sumDebit + $row['total_debit'];
                    $sumCredit = $sumCredit + $row['total_credit'];
    	}

    	$dataFormat[] = [
                            'SUB CODE2'     =>'',
							'ACCOUNT NAME' => 'Total : ',
							'DEBIT'        => number_format($sumDebit,2),
							'CREDIT'       => number_format($sumCredit,2),
    					];

		return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'SUB CODE2',
            'ACCOUNT NAME',
            'DEBIT',
            'CREDIT',
        ];
    }    
}
