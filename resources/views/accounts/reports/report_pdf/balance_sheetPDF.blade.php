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
        <h4>Balance Sheet</h4>
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
                <th colspan="4" valign="middle" class="text-center">Assets</th>
            </tr>
            <tr>
                <th>S/L</th>
                <th>PARTICULARS</th>
                <th>NOTES</th>
                <th> VALUE IN TAKA</th>
            </tr>
        </thead>                
        <tbody>
            @php $total = 0; @endphp
            @foreach($ledgers['assets'] as $key => $row)                    
            <tr>
                <td>{{++$key}}</td>
                <td>{{ $row['sub_code_title'] }}</td>
                <td>{{ $row['sub_code'] }}</td>
                <td align="right">{{ $row['balance'] }}</td>
                @php $total = $total + $row['balance']; @endphp
            </tr>
            @endforeach
            <tr>
                <td colspan="3" align="right"><strong>Total Assets: </strong></td>
                <td align="right"><strong> {{ number_format($total,2) }} </strong></td>
            </tr>                    
        </tbody>

        <thead>
            <tr>
                <th colspan="4" valign="middle" class="text-center">Liabilities</th>
            </tr>                    
            <tr>
                <th>S/L</th>
                <th>PARTICULARS</th>
                <th>NOTES</th>
                <th> VALUE IN TAKA</th>
            </tr>
        </thead>
        <tbody>
            @php $total = 0; @endphp
            @foreach($ledgers['liabilities'] as $key => $row)                    
            <tr>
                <td>{{++$key}}</td>
                <td>{{ $row['sub_code_title'] }}</td>
                <td>{{ $row['sub_code'] }}</td>
                <td align="right">{{ $row['balance'] }}</td>
                @php $total = $total + $row['balance']; @endphp
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <td colspan="3" align="right"><strong>Total Liabilities: </strong></td>
                <td align="right"><strong> {{ number_format($total,2) }} </strong></td>
            </tr>                    
        </tfoot>                
    </table>

</body>

</html>
