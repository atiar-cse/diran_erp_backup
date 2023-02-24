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
        <h4>Daily Statements</h4>
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
                <th>DATE</th>
                <th>REFERENCE</th>
                <th>PURPOSES</th>
                <th>CHART OF ACCOUNTS</th>
                <th>REMARKS</th>
                <th>DEBIT</th>
                <th>CREDIT</th>
            </tr>
        </thead>

        @php $totalDr = $totalCr = 0; @endphp
        @foreach($ledgers as $key => $row)
        <tbody>    
            @foreach($row->details as $key2 => $row2)                
            <tr>
                @if( $key2 == 0 )
                    <td>{{ dateConvertDBtoForm($row->transaction_date) }}</td>
                    <td>{{ $row->transaction_reference_no }}</td>
                    <td>{{ $row->transaction_type_name }}</td>
                @endif
                @if( $key2 > 0 )
                    <td colspan="3"></td>
                @endif                
                <td>{{ $row2->coa->chart_of_accounts_title }} ({{ $row2->coa->chart_of_account_code }})</td>
                <td>{{ $row2->remarks }}</td>
                <td align="right">
                    @if( $row2->debit_amount > 0) 
                        {{ $row2->debit_amount}} 
                    @endif 
                </td>
                <td align="right">
                    @if( $row2->credit_amount > 0) 
                        {{ $row2->credit_amount}} 
                    @endif 
                </td>
                @php 
                    $totalDr = $totalDr + $row2->debit_amount; 
                    $totalCr = $totalCr + $row2->credit_amount; 
                @endphp
            </tr>
            @endforeach
        </tbody>
        @endforeach

        <tfoot>
            <tr>
                <td colspan="5" align="right"><strong>Total: </strong></td>
                <td align="right"><strong> {{ number_format($totalDr,2) }} </strong></td>
                <td align="right"><strong> {{ number_format($totalCr,2) }} </strong></td>
            </tr>                    
        </tfoot>                
    </table>

</body>

</html>
