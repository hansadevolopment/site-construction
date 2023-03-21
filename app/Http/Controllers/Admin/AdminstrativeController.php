<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminstrativeController extends Controller {

    public function genaralLedger(){

        return view('gl.gl_dashboard');
    }

    public function getSalesDashboard(){

        return view('sales.sales_dashboard');
    }

    public function getPurchasingDashboard(){

        return view('purchase.purchasing_dashboard');
    }

    public function getInventoryDashboard(){

        return view('inventory.inventory_dashboard');
    }

    
}
