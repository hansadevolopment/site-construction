<?php

namespace App\Http\Controllers\GL\Transaction;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JournalEntryController extends Controller{

    public function loadView(){

        //$data['attributes'] = $this->getEmployeeAttributes(NULL, NULL);
        $data['attributes'] = array();

        return view('GL.transaction.journal_entry')->with('JE', $data);
    }

}
