<?php

namespace App\Http\Controllers\GL\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GL\Primary\MainAccount;
use App\Models\GL\Primary\ControllAccount;
use App\Models\GL\Primary\SubAccount;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use App\Rules\ZeroValidation;
use App\Rules\CurrencyValidation;
use App\Rules\GL\Transaction\JournalEntryGLPostValidation;

class ChartOfAccountController extends Controller {

    public function loadView(){

        $data['main_account'] = MainAccount::all();
        $data['controll_account'] = ControllAccount::all();
        $data['sub_account'] = SubAccount::all();
        $data['attributes'] = $this->getChartOfAccountAttributes(NULL, NULL);

        return view('GL.report.chart_of_account')->with('CA', $data);
    }

    private function getChartOfAccountAttributes($process, $request){

        $attributes['ma_id'] = 0;
        $attributes['ca_id'] = 0;
        $attributes['sa_id'] = 0;

        $attributes['validation_messages'] = new MessageBag();;
        $attributes['process_message'] = '';

        if( (is_null($request) == TRUE) && (is_null($process) == TRUE) ){

            return $attributes;
        }

    }

}
