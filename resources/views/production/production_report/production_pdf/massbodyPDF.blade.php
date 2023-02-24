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

    <div style="float:left;width:35%;text-align: left">
        <h4>Massbody Report</h4>
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
                <th>SI NO.</th>
                <th>Name Of Materials</th>
                <th>Consumption</th>
                <th>Remarks</th>
            </tr>
        </thead>


        <tbody>
        @php $total = 0; @endphp
        @foreach($massbodies as $key => $row)
            <tr>
                <td>{{++$key}}</td>
                <td>{{ $row->product_name }}</td>
                <td>{{ $row->consumption }}</td>
                <td>{{ $row->remarks }}</td>
                @php $total = $total + $row->consumption ; @endphp
            </tr>
        @endforeach
        <tr>
            <td colspan="2" align="right"><strong>Total : </strong></td>
            <td align="right"><strong> {{ number_format($total,2) }} </strong></td>

        </tr>
        </tbody>

    </table>

</body>

</html>