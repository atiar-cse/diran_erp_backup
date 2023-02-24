<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class LedgerSubCode2ReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
         $sumDr = $sumCr = 0;
        foreach ($ledgers as $key => $row) {
            $openingBalance = $row['opening_balance'];
            $drData         = $row['debit'];
            $crData         = $row['credit'];
            $balanceData    = $row['balance'];

            $dataFormat[] = [
                'S/L'              => ++$key,
                'DATE'             => $row['date'],
                'CHART OF A/C HEAD'=> $row['chart_of_accounts_title'].' ('. $row['chart_of_account_code'].')',
                'OPENING BALANCE'  => " $openingBalance",
                'DEBIT'            => " $drData",
                'CREDIT'           => " $crData",
                'BALANCE'          => " $balanceData",
            ];

            $sumDr = $sumDr + $row['debit'];
            $sumCr = $sumCr + $row['credit'];

        }

        $dataFormat[] = [
            'S/L'              => "",
            'DATE'             => "",
            'CHART OF A/C HEAD'=> "",
            'OPENING BALANCE'  => "Total",
            'DEBIT'            => number_format($sumDr,2),
            'CREDIT'           => number_format($sumCr,2),
            'BALANCE'          => "",

        ];

        return collect($dataFormat);
    }

    public function headings(): array
    {
        return [
            'S/L',
            'DATE',
            'Chart Of A/C Head',
            'Opening Balance',
            'Debit',
            'Credit',
            'Balance',
        ];
    }    
}
