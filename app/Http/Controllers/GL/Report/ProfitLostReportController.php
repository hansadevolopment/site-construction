<?php

namespace App\Http\Controllers\GL\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfitLostReportController extends Controller{

    public function loadView(){

        //$data['attributes'] = $this->getEmployeeAttributes(NULL, NULL);
        $data['attributes'] = array();

        return view('GL.report.profit_loss_account')->with('PL', $data);
    }

}
