<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class CashBookExport implements FromCollection, WithHeadings, ShouldAutoSize
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

    	    $drData      = $row['debit'];
    	    $crData      = $row['credit'];
    	    $balanceData = $row['balance'];

            $dataFormat[] = [
                                'COA Code'       => $row['chart_of_account_code'],
                                'COA Head'       => strip_tags($row['chart_of_accounts_title']),
                                'Particular'     => strip_tags($row['particular']),
                                'Party'          => strip_tags($row['party']),
                                'Debit/Received' => ' '.strip_tags($drData),
                                'Credit/Payment' => ' '.strip_tags($crData),
                                'Balance'        => ' '.strip_tags($balanceData),
                            ];    						
    	}

		return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'COA Code',
            'COA Head',
            'Particular',
            'Party',
            'Debit/Received',
            'Credit/Payment',
            'Balance',
        ];
    }    
}
