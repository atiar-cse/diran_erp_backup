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
            <img width="150px" src="{{url('uploads/company_logo').'/'. $company_info->logo}}" class="img responsive" onerror="this.src='img/1200px-Channel-i.png';" alt="Missing Image">
        @else
        <img width="150px" src="{{url('img/Diran_Insulator_Ltd.png')}}" class="img responsive">
        @endif
    </span>

    <div style="display: block; clear: both; overflow: hidden;">
        <div style="float:left;width:35%;text-align: left">
            <h4>Ledger Report :{{$coa_title}} ({{$type_name}})</h4>
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
                <td>Dates</td>
                <td>Voucher No</td>
                <td>Account Type</td>
                <td>Cheque Info</td>
                <td>Narration</td>
                <td>Debit</td>
                <td>Credit</td>
                <td>Balance</td>
            </tr>
        </thead>
        <tbody>
        <?php
            $total_debit = 0;
            $total_credit = 0;
        ?>
            <!-- ITEMS HERE -->
            @foreach($ledgers as $row)
            <tr>
                <td align="center">{{ $row['date'] }}</td>
                <td align="center">{{ $row['ref_no'] }}</td>
                <td align="center">{{ $row['type'] }}</td>
                <td align="center">{{ $row['cheque_info'] }}</td>
                <td align="center">{{ $row['detail'] }}</td>
                <td align="center">@if($row['debit'] > 0) {{ $row['debit'] }} @endif</td>
                <td align="center">@if($row['credit'] > 0) {{ $row['credit'] }} @endif</td>
                <td align="center">{{ $row['balance'] }}</td>
            </tr>
                <?php
                $total_debit = $total_debit + (float) $row['debit'];
                $total_credit = $total_credit + (float) $row['credit'];
                ?>
            @endforeach

        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right"><b>Total:</b></td>
                <td>{{number_format($total_debit)}}</td>
                <td>{{number_format($total_credit)}}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</body>

</html>
