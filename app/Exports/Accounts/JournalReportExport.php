<?php

namespace App\Exports\Accounts;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

// use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Shared\Date;

class JournalReportExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        // return collect($this->data);

        $journals = $this->data;

        $dataFormat = [];

        foreach ($journals as $key => $row) {



            $code =substr($row->chart_of_account_code,0,1);


            if($code==2 || $code==3){  //2 = Expense ; 3 = Asset
              $balance =  $row->opening_balance +($row->debit_amount -$row->credit_amount);

            }elseif($code==1 || $code==4){ // 1 = Income ; 4 = liability
               $balance =  $row->opening_balance +($row->credit_amount -$row->debit_amount);

            }

            $dataFormat[] = [
                'Dates'          => dateConvertDBtoForm($row->transaction_date),
                'Account'        => $row->chart_of_accounts_title,
                'Opening Balance'=> $row->opening_balance,
                'Debit'          => $row->debit_amount > 0 ? $row->debit_amount : '',
                'Credit'         => $row->credit_amount > 0 ? $row->credit_amount : '',
                'Balance'        => $balance,

            ];
        }

        return collect($dataFormat);

    }

    public function headings(): array
    {
        return [
            'Dates',
            'Account',
            'Opening Balance',
            'Debit',
            'Credit',
            'Balance',
        ];
    }    
}
