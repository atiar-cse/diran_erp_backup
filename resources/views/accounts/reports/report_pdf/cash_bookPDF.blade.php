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

    <span class="m-widget1__number m--font-success">
        @if(isset($company_info->logo))
            <img width="200px" src="{{url('uploads/company_logo').'/'. $company_info->logo}}" class="img responsive" onerror="this.src='img/1200px-Channel-i.png';" alt="Missing Image">
        @else
            <img width="200px" src="{{url('img/Diran_Insulator_Ltd.png')}}" class="img responsive">
        @endif
    </span>

    <div style="display: block; clear: both; overflow: hidden;">
        <div style="float:left;width:45%;text-align: left">
            <h3>Cash Book Report</h3>
        </div>
        <div style="float:right;width:60%;text-align: right">
            <p style="float: left;"> From Date: {{$from_date}}, To Date: {{$end_date}}</p>
        </div>
    </div>


    <hr>
    <br />
    <br />


    <table class="items" width="100%" style="font-size: 9pt; border-collapse: collapse; " cellpadding="8">

        <thead>
            <tr>
                <th width="10%">COA Code</th>
                <th width="10%">COA Head</th>
                <th width="13%">Particular</th>
                <th width="15%">Party</th>
                <th width="18%">Debit/Received</th>
                <th width="18%">Credit/Payment</th>
                <th width="18%">Balance</th>
            </tr>
        </thead>

        <tbody>
            @foreach($ledgers as $key => $row)
            <tr>
                <td>{{ $row['chart_of_account_code'] }}</td>
                <td>{!! $row['chart_of_accounts_title'] !!}</td>
                <td>{!! $row['particular'] !!}</td>
                <td>{!! $row['party'] !!}</td>
                <td align="right">
                    {!! $row['debit'] !!}
                </td>
                <td align="right">
                    {!! $row['credit'] !!}
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
