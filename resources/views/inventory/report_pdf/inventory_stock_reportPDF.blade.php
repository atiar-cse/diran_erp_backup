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
        
        table thead th {
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
        <h4>Stock Report</h4>
    </div>
    <div style="float:right;width:60%;text-align: right">
       {{-- <p style="float: left;"> From Date: {{$from_date}}, To Date: {{$end_date}}</p>--}}
    </div>

    {{--<hr>--}}
    <br />
    <br />

    <table class="items " width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
        <thead>
        <tr style="text-align: center">
            <th width="8%">S/L</th>
            <th width="20%">Warehouse</th>
            <th width="25%">Product</th>
            <th width="15%">Open qty</th>
            <th width="15%">Current Qty</th>
        </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->
            @foreach($stock_reports as $key =>$row)
            <tr>
                <td>{{ ++$key }}</td>
                <td>{{ $row->purchase_ware_houses_name }}</td>
                <td>{{ $row->product_name}}</td>
                <td>{{$row->inventory_stocks_open_qty }}</td>
                <td>{{$row->inventory_stocks_current_qty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
