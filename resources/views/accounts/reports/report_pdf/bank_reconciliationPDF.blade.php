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

    <div style="float:left;width:40%;text-align: left">
        <h4>Bank Reconcilation : {{$coa_title}}</h4>
    </div>
    <div style="float:right;width:60%;text-align: right">
        <p style="float: left;"> From Date: {{$from_date}}, To Date: {{$end_date}}</p>
    </div>

    {{--<hr>--}}
    <br />
    <br />

    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
        
        <thead>
            <tr>
                <th width="10%">S/L</th>
                <th width="15%">DATE</th>
                <th width="28%">PARTICULAR</th>
                <th width="20%">DEBIT</th>
                <th width="20%">CREDIT</th>
                <th width="20%">BALANCE</th>
            </tr>
        </thead>

        <tbody>    
            @foreach($ledgers as $key => $row)                
            <tr>
                <td align="center">{{ ++$key }}</td>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['type'] }}</td>
                <td align="right">
                    {{ $row['debit'] }}
                </td>
                <td align="right">
                    {{ $row['credit'] }}
                </td>
                <td align="right">
                    {{ $row['balance'] }}
                </td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>

            </tr>                    
        </tfoot>                
    </table>

</body>

</html>
