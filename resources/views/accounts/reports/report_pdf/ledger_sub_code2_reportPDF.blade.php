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

    <div style="float:left;width:40%;text-align: left">
        <h4>Ledger Sub Code2 Wise Report : {{$sub_code_title2}}</h4>
    </div>
    <div style="float:right;width:60%;text-align: right">
        <p style="float: left;"> From Date: {{$from_date}}, To Date: {{$end_date}}</p>
    </div>

   {{-- <hr>--}}
    <br />
    <br />

    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
        <thead>
            <tr>
                <td width="12%">DATES</td>
                <td width="20%">CHART OF A/C HEAD</td>
                <td width="15%">OPENING BALANCE</td>
                <td width="15%">DEBIT</td>
                <td width="15%">CREDIT</td>
                <td width="15%">BALANCE</td>

            </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->
            @php $totalDebitAmount = $totalCreditAmount = 0; @endphp

            @foreach($ledgers as $row)

            <tr>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['chart_of_accounts_title'] }} {{ $row['chart_of_account_code'] }}</td>
                <td>{{ $row['opening_balance'] }}</td>
                <td align="right">@if($row['debit'] > 0) {{ $row['debit'] }} @endif</td>
                <td align="right">@if($row['credit'] > 0) {{ $row['credit'] }} @endif</td>
                <td align="right">{{ $row['balance'] }}</td>

                @php $totalDebitAmount = $totalDebitAmount + $row['debit']@endphp
                @php $totalCreditAmount = $totalCreditAmount + $row['credit']@endphp

            </tr>
            @endforeach
            <tr>
                <td align="right" colspan="3"><strong>Total : </strong></td>
                <td align="right"><strong>{{number_format($totalDebitAmount,2)}}</strong></td>
                <td align="right"><strong> {{number_format($totalCreditAmount,2)}} </strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>

</body>

</html>
