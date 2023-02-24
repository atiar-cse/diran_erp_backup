<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class DailyStatementExport implements FromCollection, WithHeadings, ShouldAutoSize
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
   	
    	$totalDr = $totalCr = 0;

    	foreach ($ledgers as $key => $row) {
            foreach($row->details as $key2 => $row2) {

                $transaction_date = $transaction_reference_no = $transaction_type_name = '';
                if( $key2 == 0 ) {
                    $transaction_date         = dateConvertDBtoForm($row->transaction_date);
                    $transaction_reference_no = $row->transaction_reference_no;
                    $transaction_type_name    = $row->transaction_type_name;
                }

                $drData = $row2->debit_amount > 0 ? $row2->debit_amount : '';
                $crData = $row2->credit_amount > 0 ? $row2->credit_amount : '';

                $dataFormat[] = [
                                    'DATE'              => $transaction_date,
                                    'REFERENCE'         => $transaction_reference_no,
                                    'PURPOSES'          => $transaction_type_name,
                                    'CHART OF ACCOUNTS' => $row2->coa->chart_of_accounts_title .' '. $row2->coa->chart_of_account_code,
                                    'REMARKS'           => $row2->remarks,
                                    'DEBIT'             => " $drData",
                                    'CREDIT'            => " $crData" ,
                                ];

                    $totalDr = $totalDr + $row2->debit_amount; 
                    $totalCr = $totalCr + $row2->credit_amount;
            }    						
    	}

    	$dataFormat[] = [
							'DATE'              => '',
							'REFERENCE'         => '',
							'PURPOSES'          => '',
                            'CHART OF ACCOUNTS' => '',
                            'REMARKS'           => 'Total: ',
							'DEBIT'             => number_format($totalDr,2),
                            'CREDIT'            => number_format($totalCr,2),
    					];

		return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'DATE',
            'REFERENCE',
            'PURPOSES',
            'CHART OF ACCOUNTS',
            'REMARKS',
            'DEBIT',
            'CREDIT',
        ];
    }    
}
