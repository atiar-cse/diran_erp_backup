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
        <h4>Customer Summary Report</h4>
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
                <td width="15%">Date</td>
                <td width="20%">Supplier</td>
                <td width="20%">Product</td>
                <td width="15%">Narration</td>
                <td width="10%">Unit</td>
                <td width="15%">Qty</td>
                <td width="15%">Price</td>
                <td width="15%">Total</td>
            </tr>
        </thead>
        <tbody>
            <!-- ITEMS HERE -->
            @foreach($purchases as $row)
            <tr>
                <td align="center">{{ dateConvertDBtoForm($row->po_receive_date) }}</td>
                <td align="center">{{ $row->purchase_supplier_name }}</td>
                <td align="center">{{ $row->product_name }}</td>
                <td align="center">{{ $row->po_narration }}</td>
                <td align="center">{{ $row->pod_unit }}</td>
                <td align="center">{{ $row->pod_qty }}</td>
                <td align="center">{{ $row->pod_unit_price }}</td>
                <td align="center">{{ $row->pod_total_price }}</td>
            </tr>
            @endforeach

        </tbody>
    </table>

</body>

</html>
