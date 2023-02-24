<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        p {
            margin: 0pt;
        }

        table.items {
            border: 0.1mm solid #000000;
        }

        td {
            vertical-align: top;
        }

        .items td {
            border-left: 0.1mm solid #000000;
            border-right: 0.1mm solid #000000;
            border-bottom: 0.1mm solid #000000;
        }

        table thead td {
            background-color: #EEEEEE;
            text-align: center;
            border: 0.1mm solid #000000;
            font-variant: small-caps;
        }

        .items td.blanktotal {
            background-color: #EEEEEE;
            border: 0.1mm solid #000000;
            background-color: #FFFFFF;
            border: 0mm none #000000;
            border-top: 0.1mm solid #000000;
            border-right: 0.1mm solid #000000;
        }

        .items td.totals {
            text-align: right;
            border: 0.1mm solid #000000;
        }

        .items td.cost {
            text-align: "." center;
        }
    </style>
</head>

<body>

    <span class="m-widget1__number m--font-success">
        @if(isset($company_info->logo))
            <img width="200px" src="{{url('uploads/company_logo').'/'. $company_info->logo}}" class="img responsive" onerror="this.src='img/1200px-Channel-i.png';" alt="Missing Image">
        @else
            <img width="200px" src="{{url('img/Diran_Insulator_Ltd.png')}}" class="img responsive">
        @endif
    </span>

    <div style="display: block; clear: both; overflow: hidden;">
        <div style="float:left;width:35%;text-align: left">
            <h3>Journal Report</h3>
        </div>
        <div style="float:right;width:60%;text-align: right">
            <p style="float: left;"> From Date: {{$from_date}}, To Date: {{$end_date}}</p>
        </div>
    </div>


    <hr>
    <br />
    <br />

    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
        <thead>
            <tr>
                <td width="12%">Dates</td>
                <td width="15%">Account</td>
                <td width="15%">Opening Balance</td>
                <td width="15%">Debit</td>
                <td width="15%">Credit</td>
                <td width="15%">Balance</td>
            </tr>
        </thead>

        <tbody>
            <!-- ITEMS HERE -->
            @foreach($journals as $row)
            <tr>
                <td align="center">{{dateConvertDBtoForm($row->transaction_date) }}</td>
                <td align="center">{{ $row->chart_of_accounts_title }} ({{$row->chart_of_account_code}})</td>
                <td align="center">{{ $row->opening_balance }} </td>
                <td align="center">@if($row->debit_amount > 0) {{ $row->debit_amount }} @endif</td>
                <td align="center">@if($row->credit_amount > 0) {{ $row->credit_amount }} @endif</td>

                @php
                   $code =substr($row->chart_of_account_code,0,1);
                @endphp

                <td align="center">
                    @if($code==2 || $code==3)  {{--Expense =2; Asset=3 --}}
                        {{ $row->opening_balance +($row->debit_amount -$row->credit_amount) }}
                     @elseif($code ==1 || $code==4) {{--Income =1; Liability =4--}}
                        {{ $row->opening_balance +($row->credit_amount -$row->debit_amount) }}
                  @endif
                </td>
            </tr>
            @endforeach

        </tbody>

    </table>

</body>

</html>
