<?php

namespace App\Http\Controllers\Sales\Primary;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DebtorController extends Controller {

    public function loadView(){

        return view('Sales.Primary.debtor');
    }
    
}
