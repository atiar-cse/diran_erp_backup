<?php

namespace App\Http\Controllers\Production\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CementingReportController extends Controller
{

    public function index()
    {
        return view('production.production_report.cementing_report');
    }
}
