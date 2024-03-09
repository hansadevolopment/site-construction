<?php

namespace App\Http\Controllers\GL\ListInquiry;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrimaryListInquireController extends Controller{

    public function loadView(){

        //$data['attributes'] = $this->getEmployeeAttributes(NULL, NULL);
        $data['attributes'] = array();

        return view('GL.list.primary_inquire')->with('PI', $data);
    }

}
