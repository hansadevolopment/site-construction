<?php

namespace App\Http\Controllers\GL\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LedgerController extends Controller{

    public function loadView(){

        //$data['attributes'] = $this->getEmployeeAttributes(NULL, NULL);
        $data['attributes'] = array();

        return view('GL.report.ledger')->with('LS', $data);
    }

}
