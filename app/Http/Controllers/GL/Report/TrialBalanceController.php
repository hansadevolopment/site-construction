<?php

namespace App\Http\Controllers\GL\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller{

    public function loadView(){

        //$data['attributes'] = $this->getEmployeeAttributes(NULL, NULL);
        $data['attributes'] = array();

        return view('GL.report.trial_balance')->with('TB', $data);
    }

}
