<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class BalanceSheetExport implements FromCollection, WithHeadings, ShouldAutoSize
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

    	//Asset
    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Asset',
							'NOTES' => '',
							'VALUE IN TAKA' => '',
    					];    	
    	$total = 0;
    	foreach ($ledgers['assets'] as $key => $row) {

    		$dataFormat[] = [
								'S/L' => ++$key,
								'PARTICULARS' => $row['sub_code_title'],
								'NOTES' => $row['sub_code'],
								'VALUE IN TAKA' => $row['balance'],
    						];
                    		$total = $total + $row['balance'];    						
    	}

    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Total Assets: ',
							'NOTES' => '',
							'VALUE IN TAKA' => number_format($total,2),
    					];

    	//Liabilities
    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => '',
							'NOTES' => '',
							'VALUE IN TAKA' => '',
    					];
    	$total = 0;
    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Liabilities',
							'NOTES' => '',
							'VALUE IN TAKA' => '',
    					];

    	foreach ($ledgers['liabilities'] as $key => $row) {

    		$dataFormat[] = [
								'S/L' => ++$key,
								'PARTICULARS' => $row['sub_code_title'],
								'NOTES' => $row['sub_code'],
								'VALUE IN TAKA' => $row['balance'],
    						];
                    		$total = $total + $row['balance'];    						
    	}

    	$dataFormat[] = [
							'S/L' => '',
							'PARTICULARS' => 'Total Liabilities: ',
							'NOTES' => '',
							'VALUE IN TAKA' => number_format($total,2),
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
