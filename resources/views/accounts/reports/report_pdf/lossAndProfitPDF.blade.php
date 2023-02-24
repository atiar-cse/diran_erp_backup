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
        table thead tr th {
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
        <h4>Profit & Loss Statements</h4>
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
                <th colspan="4" valign="middle" class="text-center">Income</th>
            </tr>
            <tr>
                <th>S/L</th>
                <th>PARTICULARS</th>
                <th>NOTES</th>
                <th> VALUE IN TAKA</th>
            </tr>
        </thead>                
        <tbody>
            @php $totalIncome = 0; @endphp
            @foreach($ledgers['income'] as $key => $row)                    
            <tr>
                <td>{{++$key}}</td>
                <td>{{ $row['sub_code_title2'] }}</td>
                <td>{{ $row['sub_code2'] }}</td>
                <td align="right">{{ $row['credit_amount'] }}</td>
                @php $totalIncome = $totalIncome + $row['credit_amount']; @endphp
            </tr>
            @endforeach
            <tr>
                <td colspan="3" align="right"><strong>Total Income: </strong></td>
                <td align="right"><strong> {{ number_format($totalIncome,2) }} </strong></td>
            </tr>                    
        </tbody>

        <thead>
            <tr>
                <th colspan="4" valign="middle" class="text-center">Expense</th>
            </tr>                    
            <tr>
                <th>S/L</th>
                <th>PARTICULARS</th>
                <th>NOTES</th>
                <th> VALUE IN TAKA</th>
            </tr>
        </thead>
        <tbody>
            @php $totalExpense = 0; @endphp
            @foreach($ledgers['expense'] as $key => $row)                    
            <tr>
                <td>{{++$key}}</td>
                <td>{{ $row['sub_code_title2'] }}</td>
                <td>{{ $row['sub_code2'] }}</td>
                <td align="right">{{ $row['debit_amount'] }}</td>
                @php $totalExpense = $totalExpense + $row['debit_amount']; @endphp
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" align="right"><strong>Total Expense: </strong></td>
                <td align="right"><strong> {{ number_format($totalExpense,2) }} </strong></td>
            </tr>
            <tr>
                <td colspan="3" align="right"><strong>Net Profit: </strong></td>
                <td align="right"><strong> {{ number_format($totalIncome - $totalExpense,2) }} </strong></td>
            </tr>                    
        </tfoot>                
    </table>

</body>

</html>
