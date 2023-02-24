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
    <!--mpdf
        <htmlpageheader name="myheader">
            <table width="100%">
                <tr>
                    <td width="60%" style="color:#0000BB; ">
                        @if($company->logo)
                            <img style="vertical-align: top" src="{{url('uploads/company_logo').'/'. $company->logo}}" width="120" /> <br /><br />
                        @else
                            <span style="font-weight: bold; font-size: 14pt;">{{$company->name}} </span><br />
                        @endif
                        {{$company->address}}<br />
                        @if($company->address2) {{$company->address2}} <br /> @endif
                        <span style="font-family:dejavusanscondensed;">&#9742;</span> {{$company->phone}}

                    </td>
                    <td width="40%" style="text-align: right;">

                        Mobile: {{$company->mobile}}<br />
                        Fax: {{$company->fax}}<br />
                        Email: {{$company->email}}<br />
                        Web: {{$company->web}}<br />
                    </td>
                </tr>
            </table>
            <div style="border-top: 1px solid #000000;"></div>
        </htmlpageheader>
        <htmlpagefooter name="myfooter">
            <div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 2mm; ">
                Page {PAGENO} of {nb}
            </div>
        </htmlpagefooter>
        <sethtmlpageheader name="myheader" value="on" show-this-page="1" />
        <sethtmlpagefooter name="myfooter" value="on" />
    mpdf-->
    <div style="text-align: center; display: block; margin: 10px 0;">
        <h4 style=" border-bottom: 1px solid #333; padding: 5px 0; width:150px; margin: 0 auto; ">Assembling Section</h4>
    </div>
    <br>

    <div style="display: block; width: 100%">
        <div style=" width:60% ; float: left">
            <p> <span class="m-invoice__subtitle">Assembling No :</span>{{ $assembling['assembling_no'] }}</p>
            <p><span class="m-invoice__subtitle">Types :</span>{{ $assembling['assembling_types'] }}</p>
            <p><span class="m-invoice__subtitle">Date :</span>{{ $assembling['assembling_date'] }}</p>
            <p><span class="m-invoice__subtitle">Warehouse :</span>{{ $assembling['warehouse_id'] }}</p>
        </div>
        <div style=" width:40%; float: right">
            <p> <span class="m-invoice__subtitle">Finish Product :</span>{{ $assembling['finishing_product_id'] }}</p>
            <p><span class="m-invoice__subtitle">Store Qty :</span>{{ $assembling['trans_to_packing_qty'] }}</p>
            <p><span class="m-invoice__subtitle">Unit Price :</span>{{ $assembling['unit_price'] }}</p>
            <p><span class="m-invoice__subtitle">Total Price :</span>{{ $assembling['total_price'] }}</p>
        </div>
    </div>
    <br/>

    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
        <thead>
            <tr>
                <th>Product Name</th>
                <th class="text-center">Remarks</th>
                <th class="text-right">Semi.F Stock Qty</th>
                <th class="text-right">Inventory Stock Qty</th>
                <th class="text-right">Semi.F Use Qty </th>
                <th class="text-right">Inventory Use Qty</th>
                <th class="text-right">Toal used Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total Price</th>
            </tr>
        </thead>

        <tbody>
            <!-- ITEMS HERE -->
            @foreach($assembling['details'] as $row)
                @php
                    $product = $row->product->product_name;
                @endphp
            <tr>

                <td align="center">{{ $product }}</td>
                <td align="center">{{ $row['remarks'] }}</td>
                <td align="center">{{ $row['semi_finished_stock_qty'] }}</td>
                <td align="center">{{ $row['inventory_stock_qty'] }}</td>
                <td align="center">{{ $row['semi_finished_use_qty'] }}</td>
                <td align="center">{{ $row['inventory_use_qty'] }}</td>
                <td align="center">{{ $row['total_used_qty'] }}</td>
                <td align="center">{{ $row['unit_price'] }}</td>
                <td align="center">{{ $row['total_price'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="text-right m--font-danger" colspan="6" style="text-align: right">Total</td>
                <td class="text-right m--font-danger">{{ $assembling['total_rm_qty'] }}</td>
                <td></td>
                <td class="text-right">{{ $assembling['total_rm_price'] }}</td>

            </tr>

        </tbody>

    </table>

    <div class="description-section" style="margin-top:15px;">
        <p>
            <span style="font-weight: 600">Narration :</span>
            <span>
              {{ $assembling['narration'] }}
            </span>
        </p>
    </div>

    <div style="clear:both;width:100%;margin-top:170px;"></div>

    <div style="display: block;">
        <div style="width: 33.33%;display:block; float:left;">
            <span class="m-invoice__subtitle" style=" border-top: 1px solid #333;">Prepared By</span>
        </div>
        <div style="width: 33.33%; display:block; float:left;text-align: center; ">
            <span class="m-invoice__subtitle signature-center" style=" border-top: 1px solid #333;">Checked By</span>
        </div>
        <div style="width: 33.33%;display:block; float:left;text-align: right;">
            <span class="m-invoice__subtitle signature-right" style=" border-top: 1px solid #333;">Approved By</span>
        </div>
    </div>

</body>

</html>