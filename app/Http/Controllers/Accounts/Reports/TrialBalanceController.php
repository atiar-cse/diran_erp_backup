<?php

namespace App\Http\Controllers\Accounts\Reports;

use App\Exports\Accounts\TrialBalanceExport;
use App\Model\Accounts\AccountsSubCode2;
use App\Model\CompanyInfo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Accounts\AccountsChartofAccounts;
use App\Model\Accounts\AccountsMainCode;
use Maatwebsite\Excel\Facades\Excel;
use DB;

class TrialBalanceController extends Controller
{

    public function index(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];

            if($request->from_date == '' && $request->end_date == '')
            {
                $data['ledgers'] = $this->trialBalanceFilterReport($request);
            }
            elseif ($request->from_date != '' || $request->end_date != '')
            {
                $data['ledgers'] = $this->trialBalanceFilterDateReport($request);
            }


            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                return Excel::download(new TrialBalanceExport( $data['ledgers'] ), 'Trial Balance.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }


        return view('accounts.reports.trial_blance');
    }

    public function sub2(Request $request)
    {
        if ($request->filled('export')) {

            $file_type = $request->export;

            $data = [];

            if($request->from_date == '' && $request->end_date == '')
            {
                $data['ledgers'] = $this->trialBalanceFilterReport($request);
            }
            elseif ($request->from_date != '' && $request->end_date != '')
            {
                $data['ledgers'] = $this->trialBalanceFilterDateReport($request);
            }


            $data['from_date'] = $request->from_date;
            $data['end_date']  = $request->end_date;

            if ($file_type=='pdf') {

                $this->exportAsPdf($data);

            } elseif ($file_type=='excel') {

                return Excel::download(new TrialBalanceExport( $data['ledgers'] ), 'Trial Balance.xlsx');

            } else {
                dd('Something went wrong.');
            }
        }


        return view('accounts.reports.trial_blance_sub2');
    }

    public function trialBalanceFilterReport(Request $request)
    {
        $coaList = DB::table('accounts_chartof_accounts')
            ->select('accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.sub_code2_id','accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.id','accounts_chartof_accounts.chart_of_accounts_title','accounts_chartof_accounts.opening_balance','accounts_sub_code2s.sub_code_title2')
            ->leftJoin('accounts_sub_code2s', 'accounts_chartof_accounts.sub_code2_id', '=', 'accounts_sub_code2s.id')
            ->orderBy('accounts_sub_code2s.sub_code_title2','ASC')
            ->where('accounts_chartof_accounts.status',1)
            ->where('accounts_chartof_accounts.deleted_at',NULL)
            ->get();

        $data = [];
        foreach ($coaList as $row)
        {
            $main_acc_code = substr($row->chart_of_account_code, 0, 1);

            $journal_data = DB::table('accounts_journal_entry_details')
                ->select(DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"),DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"))
                ->where('accounts_journal_entry_details.char_of_account_id',$row->id)
                ->first();

            $total_dr = 0; $total_cr = 0; $isNegativeBalance = 'no';

            if($main_acc_code == 2 || $main_acc_code == 3) //2=Expense, 3=Assets
            {
                if($journal_data){
                    $total_dr = (double) $row->opening_balance + ((int) $journal_data->total_debit - (int) $journal_data->total_credit);
                }

                if ($total_dr < 0){ // If Negative Balance

                    $total_cr = abs($total_dr); //Return the absolute value of different numbers
                    $total_dr = 0;
                    $isNegativeBalance = 'yes';
                }
            }
            else // 1=Income, 4=Liabilities
            {
                if($journal_data){
                    $total_cr = (double) $row->opening_balance - ((int) $journal_data->total_debit + (int) $journal_data->total_credit);
                }

                if ($total_cr < 0){ // If Negative Balance

                    $total_dr = abs($total_cr); //Return the absolute value of different numbers
                    $total_cr = 0;
                    $isNegativeBalance = 'yes';
                }
            }

            $negativeHtml = '';
            if($isNegativeBalance=='yes') { $negativeHtml=' ~ [[[NEGATIVE]]]'; }

            $data[] = [
                'sub2_title' => $row->sub_code_title2,
                'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code) $negativeHtml",
                'total_debit' => $total_dr,
                'total_credit' => $total_cr,
            ];
        }
        return $data;
    }

    function trialBalanceFilterDateReport(Request $request)
    {
        $from_date = $request->from_date;
        $end_date  = $request->end_date;
        $result  = '';

        if($from_date!=''){
            $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date)));
        }else{$from_date='';}

        if($end_date!=''){
            $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date)));
        }else{$end_date='';}

        $result = DB::table('accounts_chartof_accounts')
            ->select('accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.sub_code2_id','accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.id','accounts_chartof_accounts.chart_of_accounts_title','accounts_chartof_accounts.opening_balance')//,DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"),DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"))
            //->orderBy('accounts_chartof_accounts.chart_of_accounts_title','ASC')
            ->leftJoin('accounts_sub_code2s', 'accounts_chartof_accounts.sub_code2_id', '=', 'accounts_sub_code2s.id')
            ->orderBy('accounts_sub_code2s.sub_code_title2','ASC')
            ->orderBy('accounts_chartof_accounts.chart_of_accounts_title','ASC')
            ->whereBetween('accounts_chartof_accounts.opening_date',[$from_date,$end_date])
            ->where('accounts_chartof_accounts.status',1)
            ->where('accounts_chartof_accounts.deleted_at',NULL)
            ->get();


        if ($result) {

            $data = [];
            foreach ($result as $row) {

                $chart_of_acc_id = $row->id;

                $sub2_acc_title = AccountsSubCode2::where('id',$row->sub_code2_id)->first();

                if($sub2_acc_title)
                {
                    $sub2_acc_title = $sub2_acc_title->sub_code_title2;
                }
                else{
                    $sub2_acc_title = '';
                }

                $main_acc_code = substr($row->chart_of_account_code, 0, 1);


                if ($from_date != '') {

                    $journal_data = DB::table('accounts_journal_entries')
                        ->select('accounts_journal_entries.*', DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"), DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"), 'accounts_journal_entry_details.char_of_account_id')
                        ->leftJoin('accounts_journal_entry_details', 'accounts_journal_entries.id', '=', 'accounts_journal_entry_details.journal_entry_id')
                        ->where('accounts_journal_entries.approve_status', 1)
                        ->where('accounts_journal_entries.transaction_date', '>=', $from_date)
                        ->where('accounts_journal_entry_details.char_of_account_id',$chart_of_acc_id)
                        ->first();
                }

                if ($end_date != '') {

                    $journal_data = DB::table('accounts_journal_entries')
                        ->select('accounts_journal_entries.*', DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"), DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"), 'accounts_journal_entry_details.char_of_account_id')
                        ->leftJoin('accounts_journal_entry_details', 'accounts_journal_entries.id', '=', 'accounts_journal_entry_details.journal_entry_id')
                        ->where('accounts_journal_entries.approve_status', 1)
                        ->where('accounts_journal_entries.transaction_date', '<=', $end_date)
                        ->where('accounts_journal_entry_details.char_of_account_id',$chart_of_acc_id)
                        ->first();

                }

                if ($from_date != '' && $end_date != '')
                {
                    $journal_data = DB::table('accounts_journal_entries')
                        ->select('accounts_journal_entries.*', DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"), DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"), 'accounts_journal_entry_details.char_of_account_id')
                        ->leftJoin('accounts_journal_entry_details', 'accounts_journal_entries.id', '=', 'accounts_journal_entry_details.journal_entry_id')
                        ->where('accounts_journal_entries.approve_status', 1)
                        ->where('accounts_journal_entries.transaction_date', '>=', $from_date)
                        ->where('accounts_journal_entries.transaction_date', '<=', $end_date)
                        ->where('accounts_journal_entry_details.char_of_account_id',$chart_of_acc_id)
                        ->first();
                }


                if($main_acc_code == 2 || $main_acc_code == 3)
                {
                    if($journal_data)
                    {
                        if($row->opening_balance < 0)
                        {
                            $ttl_abs_dbt = (double) abs($row->opening_balance) + ( (double) $journal_data->total_debit - (double) $journal_data->total_credit );
                        }
                        else
                        {
                            $ttl_dbt = (double) $row->opening_balance + ( (double) $journal_data->total_debit - (double) $journal_data->total_credit );
                        }
                    }
                    else
                    {
                        $ttl_dbt = 0;
                    }


                    if(isset($ttl_abs_dbt))
                    {
                        $data[] = [
                            'sub2_title' => $sub2_acc_title,
                            'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code)",
                            'total_debit' => 0,
                            'total_credit' => round($ttl_abs_dbt,2),
                        ];
                        unset($ttl_abs_dbt);
                    }
                    elseif(isset($ttl_dbt) && $ttl_dbt != 0)
                    {
                        $data[] = [
                            'sub2_title' => $sub2_acc_title,
                            'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code)",
                            'total_debit' => round($ttl_dbt,2),
                            'total_credit' => 0,
                        ];
                    }

                }
                else
                {
                    if($journal_data)
                    {
                        if($row->opening_balance < 0)
                        {
                            $ttl_abs_crdt = (double) abs($row->opening_balance) + ( (double) $journal_data->total_credit - (double) $journal_data->total_debit );
                        }
                        else
                        {
                            $ttl_crdt = (double) $row->opening_balance + ( (double) $journal_data->total_credit - (double) $journal_data->total_debit );
                        }
                    }
                    else
                    {
                        $ttl_crdt = 0;
                    }


                    if(isset($ttl_abs_crdt))
                    {
                        $data[] = [
                            'sub2_title' => $sub2_acc_title,
                            'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code)",
                            'total_debit' => round($ttl_abs_crdt,2),
                            'total_credit' => 0,
                        ];
                        unset($ttl_abs_crdt);
                    }
                    elseif(isset($ttl_crdt) && $ttl_crdt != 0)
                    {
                        $data[] = [
                            'sub2_title' => $sub2_acc_title,
                            'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code)",
                            'total_debit' => 0,
                            'total_credit' => round($ttl_crdt,2),
                        ];
                    }

                }

            }
            return $data;
        } else {
            return $result;
        }
    }

    public function exportAsPdf($data)
    {
        $company = CompanyInfo::Find(1);

        $data = array(
            'company' => $company,
            'ledgers' => $data['ledgers'],
            'from_date' => $data['from_date'],
            'end_date' => $data['end_date'],
        );
        $html = \View::make('accounts.reports.report_pdf.trial_balancePDF')->with($data);

        $mpdf = new \Mpdf\Mpdf([
            'margin_left' => 20,
            'margin_right' => 15,
            'margin_top' => 48,
            'margin_bottom' => 25,
            'margin_header' => 10,
            'margin_footer' => 10
        ]);

        $title = 'Trial Balance';
        $author = 'iVenture';
        if ($company) {
            $title .= ' - '.$company->name;
            $author = $company->name;
        }

        $mpdf->SetProtection(array('print'));
        $mpdf->SetTitle($title);
        $mpdf->SetAuthor($author);
        $mpdf->SetWatermarkText("Trial Balance");
        $mpdf->showWatermarkText = true;
        $mpdf->watermark_font = 'DejaVuSansCondensed';
        $mpdf->watermarkTextAlpha = 0.07;
        $mpdf->SetDisplayMode('fullpage');
        // $stylesheet = file_get_contents(public_path().'/css/mpdf.css');
        // $mpdf->WriteHTML($stylesheet,1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    public function trialBalanceFilterReportSub2(Request $request)
    {
        $coaList = DB::table('accounts_chartof_accounts')
            ->select('accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.sub_code2_id','accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.id','accounts_chartof_accounts.chart_of_accounts_title','accounts_chartof_accounts.opening_balance','accounts_sub_code2s.sub_code_title2')
            ->leftJoin('accounts_sub_code2s', 'accounts_chartof_accounts.sub_code2_id', '=', 'accounts_sub_code2s.id')
            ->groupBy('accounts_chartof_accounts.sub_code2_id')
            ->orderBy('accounts_sub_code2s.sub_code_title2','ASC')
            ->where('accounts_chartof_accounts.status',1)
            ->where('accounts_chartof_accounts.deleted_at',NULL)
            ->get();

        $data = [];
        foreach ($coaList as $row)
        {
            $subCode2 = $row->sub_code2_id;

            $opening_balance = DB::table('accounts_chartof_accounts')
                ->select(DB::raw("SUM(accounts_chartof_accounts.opening_balance)as opening_balance"))
                ->where('accounts_chartof_accounts.sub_code2_id',$subCode2)
                ->first()->opening_balance;

            $coaIds = AccountsChartofAccounts::select('id')->where('sub_code2_id',$subCode2)->get()->toArray();

            $main_acc_code = substr($row->chart_of_account_code, 0, 1);

            $journal_data = DB::table('accounts_journal_entry_details')
                ->select(DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"),DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"))
                ->whereIn('accounts_journal_entry_details.char_of_account_id',$coaIds)
                /*->leftjoin('accounts_chartof_accounts','accounts_journal_entry_details.char_of_account_id', '=', 'accounts_chartof_accounts.id')
                ->leftjoin('accounts_sub_code2s','accounts_chartof_accounts.sub_code2_id', '=', 'accounts_sub_code2s.id')*/
                ->first();

            $total_dr = 0; $total_cr = 0; $isNegativeBalance = 'no';

            if($main_acc_code == 2 || $main_acc_code == 3) //2=Expense, 3=Assets
            {
                if($journal_data){
                    $total_dr = (double) $opening_balance + ((int) $journal_data->total_debit - (int) $journal_data->total_credit);
                }

                if ($total_dr < 0){ // If Negative Balance

                    $total_cr = abs($total_dr); //Return the absolute value of different numbers
                    $total_dr = 0;
                    $isNegativeBalance = 'yes';
                }
            }
            else // 1=Income, 4=Liabilities
            {
                if($journal_data){
                    $total_cr = (double) $opening_balance - ((int) $journal_data->total_debit + (int) $journal_data->total_credit);
                }

                if ($total_cr < 0){ // If Negative Balance

                    $total_dr = abs($total_cr); //Return the absolute value of different numbers
                    $total_cr = 0;
                    $isNegativeBalance = 'yes';
                }
            }

            $negativeHtml = '';
            if($isNegativeBalance=='yes') { $negativeHtml=' ~ [[[NEGATIVE]]]'; }

            $data[] = [
                'sub2_title' => $row->sub_code_title2,
                'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code) $negativeHtml",
                'total_debit' => $total_dr,
                'total_credit' => $total_cr,
            ];
        }
        return $data;
    }

    function trialBalanceFilterDateReportSub2(Request $request)
    {
        $from_date = $request->from_date;
        $end_date  = $request->end_date;
        $result  = '';

        if($from_date!=''){
            $from_date=date('Y-m-d', strtotime(str_replace('/', '-',$from_date)));
        }else{$from_date='';}

        if($end_date!=''){
            $end_date=date('Y-m-d', strtotime(str_replace('/', '-',$end_date)));
        }else{$end_date='';}

        $result = DB::table('accounts_chartof_accounts')
            ->select('accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.sub_code2_id','accounts_chartof_accounts.chart_of_account_code','accounts_chartof_accounts.id','accounts_chartof_accounts.chart_of_accounts_title','accounts_chartof_accounts.opening_balance')//,DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"),DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"))
            //->orderBy('accounts_chartof_accounts.chart_of_accounts_title','ASC')
            ->leftJoin('accounts_sub_code2s', 'accounts_chartof_accounts.sub_code2_id', '=', 'accounts_sub_code2s.id')
            ->groupBy('accounts_chartof_accounts.sub_code2_id')
            ->orderBy('accounts_sub_code2s.sub_code_title2','ASC')
            ->whereBetween('accounts_chartof_accounts.opening_date',[$from_date,$end_date])
            ->where('accounts_chartof_accounts.status',1)
            ->where('accounts_chartof_accounts.deleted_at',NULL)
            ->get();



        if ($result) {

            $data = [];
            foreach ($result as $row) {

                $subCode2 = $row->sub_code2_id;

                $opening_balance = DB::table('accounts_chartof_accounts')
                    ->select(DB::raw("SUM(accounts_chartof_accounts.opening_balance)as opening_balance"))
                    ->where('accounts_chartof_accounts.sub_code2_id',$subCode2)
                    ->first()->opening_balance;

                $coaIds = AccountsChartofAccounts::select('id')->where('sub_code2_id',$subCode2)->get()->toArray();

                //$chart_of_acc_id = $row->id;

                $sub2_acc_title = AccountsSubCode2::where('id',$row->sub_code2_id)->first();

                if($sub2_acc_title)
                {
                    $sub2_acc_title = $sub2_acc_title->sub_code_title2;
                }
                else{
                    $sub2_acc_title = '';
                }

                $main_acc_code = substr($row->chart_of_account_code, 0, 1);

                /*$journal_data = DB::table('accounts_journal_entry_details')
                    ->select(DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"),DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"))
                    ->where('accounts_journal_entry_details.char_of_account_id',$chart_of_acc_id)
                    ->first();*/

                if ($from_date != '') {

                    $journal_data = DB::table('accounts_journal_entries')
                        ->select('accounts_journal_entries.*', DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"), DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"), 'accounts_journal_entry_details.char_of_account_id')
                        ->leftJoin('accounts_journal_entry_details', 'accounts_journal_entries.id', '=', 'accounts_journal_entry_details.journal_entry_id')
                        ->where('accounts_journal_entries.approve_status', 1)
                        ->where('accounts_journal_entries.transaction_date', '>=', $from_date)
                        ->whereIn('accounts_journal_entry_details.char_of_account_id',$coaIds)
                        ->first();



                }

                if ($end_date != '') {

                    $journal_data = DB::table('accounts_journal_entries')
                        ->select('accounts_journal_entries.*', DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"), DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"), 'accounts_journal_entry_details.char_of_account_id')
                        ->leftJoin('accounts_journal_entry_details', 'accounts_journal_entries.id', '=', 'accounts_journal_entry_details.journal_entry_id')
                        ->where('accounts_journal_entries.approve_status', 1)
                        ->where('accounts_journal_entries.transaction_date', '<=', $end_date)
                        ->whereIn('accounts_journal_entry_details.char_of_account_id',$coaIds)
                        ->first();


                }

                if ($from_date != '' && $end_date != '')
                {
                    $journal_data = DB::table('accounts_journal_entries')
                        ->select('accounts_journal_entries.*', DB::raw("SUM(accounts_journal_entry_details.debit_amount)as total_debit"), DB::raw("SUM(accounts_journal_entry_details.credit_amount)as total_credit"), 'accounts_journal_entry_details.char_of_account_id')
                        ->leftJoin('accounts_journal_entry_details', 'accounts_journal_entries.id', '=', 'accounts_journal_entry_details.journal_entry_id')
                        ->where('accounts_journal_entries.approve_status', 1)
                        ->where('accounts_journal_entries.transaction_date', '>=', $from_date)
                        ->where('accounts_journal_entries.transaction_date', '<=', $end_date)
                        ->whereIn('accounts_journal_entry_details.char_of_account_id',$coaIds)
                        ->first();


                }


                if($main_acc_code == 2 || $main_acc_code == 3)
                {
                    if($journal_data)
                    {
                        $ttl_dbt = (double) $opening_balance + ((int) $journal_data->total_debit - (int) $journal_data->total_credit);

                    }
                    else{
                        $ttl_dbt =0;
                    }


                    if($ttl_dbt != 0)
                    {
                        $data[] = [
                            'sub2_title' => $sub2_acc_title,
                            //'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code)",
                            //'opening_balance' => '',//$row->opening_balance,
                            'total_debit' => round($ttl_dbt,2),//$row->opening_balance + ($journal_data->total_debit ? $journal_data->total_debit : 0 - $journal_data->total_credit ? $journal_data->total_credit : 0),
                            'total_credit' => 0,
                            //'current_balance' => '',//round($row->opening_balance + ($row->total_debit - $row->total_credit),2),
                        ];
                    }



                }
                else
                {
                    if($journal_data)
                    {
                        $ttl_crdt = (double) $opening_balance - ((int) $journal_data->total_debit + (int) $journal_data->total_credit);

                    }
                    else
                    {
                        $ttl_crdt = 0;
                    }


                    if($ttl_crdt != 0)
                    {
                        $data[] = [
                            'sub2_title' => $sub2_acc_title,
                            //'chart_of_accounts_title' => $row->chart_of_accounts_title."($row->chart_of_account_code)",
                            //'opening_balance' => '',//$row->opening_balance,
                            'total_debit' => 0,
                            'total_credit' => round($ttl_crdt,2),//$row->opening_balance - ($journal_data->total_debit ? $journal_data->total_debit : 0 + $journal_data->total_credit ? $journal_data->total_credit : 0),
                            //'current_balance' => '',//round($row->opening_balance + ($row->total_debit - $row->total_credit),2),
                        ];
                    }



                }



            }
            return $data;
        } else {
            return $result;
        }
    }

}
