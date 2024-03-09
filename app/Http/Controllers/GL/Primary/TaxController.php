<?php

namespace App\Http\Controllers\GL\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaxController extends Controller {

    public function loadView(){

        //$data['attributes'] = $this->getEmployeeAttributes(NULL, NULL);
        $data['attributes'] = array();

        return view('GL.primary.tax')->with('T', $data);
    }

}
