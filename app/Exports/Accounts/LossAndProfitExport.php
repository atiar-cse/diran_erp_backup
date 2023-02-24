<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class LossAndProfitExport implements FromCollection, WithHeadings, ShouldAutoSize
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

    	//Income
    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Income',
							'NOTES' => '',
							'VALUE IN TAKA' => '',
    					];    	
    	$totalIncome = 0;
    	foreach ($ledgers['income'] as $key => $row) {

    		$dataFormat[] = [
								'S/L' => ++$key,
								'PARTICULARS' => $row['sub_code_title2'],
								'NOTES' => $row['sub_code2'],
								'VALUE IN TAKA' => $row['credit_amount'] ? $row['credit_amount'] :'0',
    						];
                    		$totalIncome = $totalIncome + $row['credit_amount'];    						
    	}

    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Total Income: ',
							'NOTES' => '',
							'VALUE IN TAKA' => number_format($totalIncome,2),
    					];

    	//Expense
    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => '',
							'NOTES' => '',
							'VALUE IN TAKA' => '',
    					];
    	$totalExpense = 0;
    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Expense',
							'NOTES' => '',
							'VALUE IN TAKA' => '',
    					];

    	foreach ($ledgers['expense'] as $key => $row) {

    		$dataFormat[] = [
								'S/L' => ++$key,
								'PARTICULARS' => $row['sub_code_title2'],
								'NOTES' => $row['sub_code2'],
								'VALUE IN TAKA' => $row['debit_amount'] ? $row['debit_amount']:'0',
    						];
                    		$totalExpense = $totalExpense + $row['debit_amount'];    						
    	}

    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Total Expense: ',
							'NOTES' => '',
							'VALUE IN TAKA' => number_format($totalExpense,2),
    					];

        $dataFormat[] = [
                            'S/L' => '',
                            'PARTICULARS' => 'Net Profit: ',
                            'NOTES' => '',
                            'VALUE IN TAKA' => number_format($totalIncome - $totalExpense,2),
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
