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

    <div style="float:left;width:35%;text-align: left">
        <h4>Income Statment</h4>
    </div>
    <div style="float:right;width:60%;text-align: right">
        <p style="float: left;"> From Date: {{$from_date}}, To Date: {{$end_date}}</p>
    </div>

    <hr>
    <br />
    <br />

    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
        <thead>
            <tr>
                <td width="10%">S/L</td>
                <td width="20%">PARTICULARS</td>
                <td width="20%">NOTES</td>
                <td width="20%">VALUE IN TAKA</td>
            </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->
            @php $sumCredit = 0; @endphp
            @foreach($ledgers as $key => $row)
            <tr>
                <td align="center">{{ ++$key }}</td>
                <td align="center">{{ $row['sub_code_title2'] }}</td>
                <td align="center">{{ $row['sub_code2'] }}</td>
                <td align="right">{{ $row['credit_amount'] }}</td>
                @php
                    $sumCredit = $sumCredit + $row['credit_amount'];
                @endphp
            </tr>
            @endforeach
            <tr>
                <td colspan="3" align="right"><strong>Total : </strong></td>
                <td align="right"><strong> {{ number_format($sumCredit,2) }} </strong></td>
            </tr>

        </tbody>
    </table>

</body>

</html>
