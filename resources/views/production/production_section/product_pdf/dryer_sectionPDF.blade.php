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
        <h4 style=" border-bottom: 1px solid #333; padding: 5px 0; width:120px; margin: 0 auto; ">Dryer Section</h4>
    </div>
    <br>

    <div style="display: block;">
        <div style="width: 50%;display:block; float:left;">
            <p> <span class="m-invoice__subtitle">Dryer No :</span>{{ $dryer['dryer_no'] }}</p>
        </div>
        <div style="width: 50%;display:block; float:left; text-align: right">
            <p><span class="m-invoice__subtitle">Dryer Date :</span>{{ $dryer['dryer_date'] }}</p>
        </div>
    </div>
    <br/>

    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">
        <thead>
            <tr>
                <th>Product Name</th>
                <th class="text-center">Remarks</th>
                <th class="text-center">Weight</th>
                <th class="text-right">Opening / Current QTY</th>
                <th class="text-right">Rec.F Shaping Qty</th>
                <th class="text-right">Trans to Glaze Qty</th>
                <th class="text-right">Trans Weight</th>
                <th class="text-right">Balance Qty</th>
            </tr>
        </thead>

        <tbody>
            <!-- ITEMS HERE -->
            @foreach($dryer['details'] as $row)
                @php
                    $product = $row->product->product_name;
                @endphp
            <tr>

                <td align="center">{{ $product }}</td>
                <td align="center">{{ $row['remarks'] }}</td>
                <td align="center">{{ $row['dryer_product_weight'] }}</td>
                <td align="center">{{ $row['current_qty'] }}</td>
                <td align="center">{{ $row['receive_qty'] }}</td>
                <td align="center">{{ $row['trans_to_glaze_qty'] }}</td>
                <td align="center">{{ $row['trans_to_glaze_weight'] }}</td>
                <td align="center">{{ $row['remain_qty'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td class="text-right m--font-danger" colspan="4" style="text-align: right">Total</td>
                <td class="text-right m--font-danger">{{ $dryer['total_receive_qty'] }}</td>
                <td class="text-right">{{ $dryer['total_trans_to_glaze_qty'] }}</td>
                <td class="text-right">{{ $dryer['total_trans_to_glaze_weight'] }}</td>
                <td class="text-right">{{ $dryer['total_remain_qty'] }}</td>
            </tr>

        </tbody>

    </table>

    <div class="description-section" style="margin-top:15px;">
        <p>
            <span style="font-weight: 600">Narration :</span>
            <span>
              {{ $dryer['narration'] }}
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