<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;
use App\Models\SiteMM\SiteOperation\PaymentVoucherDetail;

use Illuminate\Support\Facades\DB;

class PaymentVoucher extends Model {

    use HasFactory;

    protected $table = 'payment_voucher';

    protected $primaryKey = 'ea_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function paymentVoucherDetail(){

        return $this->hasMany(PaymentVoucherDetail::class, 'ea_id');
    }

    public function getSite(){

        return Site::where('site_id', $this->site_id)->first();
    }

    public function getTask(){

        return SiteTask::where('task_id', $this->task_id)->first();
    }

    public function getSubTask(){

        return SiteSubTask::where('sub_task_id', $this->sub_task_id)->first();
    }

    public function savePaymentVoucher($data){

        $employee_advance = $data['ea'];
        $pv = $data['pv'];
        $pv_detail = $data['pv_detail'];

        DB::beginTransaction();

        //try{

            if( count($employee_advance) >= 1 ){

                if( $employee_advance['ea_id'] == '#Auto#' ){

                    unset($employee_advance['ea_id']);
                    DB::table('employee_advance')->insert($employee_advance);
                    $ea_id = DB::getPdo()->lastInsertId();

                }else{

                    $ea_id = $employee_advance['ea_id'];
                    DB::table('employee_advance')->where('ea_id', $employee_advance['ea_id'])->update($employee_advance);
                }

                if( ! is_null(DB::table('payment_voucher')->where('ea_id', $ea_id)->value('pv_id'))  ){

                    $pv['pv_id'] =  DB::table('payment_voucher')->where('ea_id', $ea_id)->value('pv_id');
                }

                $pv['ea_id'] = $ea_id;
            }

            // Payment Voucher Table
            if( $pv['pv_id'] == '#Auto#' ){

                unset($pv['pv_id']);
                DB::table('payment_voucher')->insert($pv);
                $pv_id = DB::getPdo()->lastInsertId();

            }else{

                $pv_id = $pv['pv_id'];
                DB::table('payment_voucher')->where('pv_id', $pv['pv_id'])->update($pv);
            }

            // Detail Table

            // Labour
            if( $pv['cs_id'] == 2){

                $pv_detail['pv_id'] = $pv_id;
                $upsert_flag = PaymentVoucherDetail::where('pv_id', $pv_id)->where('employee_id', $pv_detail['employee_id'])->exists();
                if( $upsert_flag ){

                    DB::table('payment_voucher_detail')->where('pv_id', $pv_id)->where('employee_id', $pv_detail['employee_id'])->update($pv_detail);
                }else{

                    DB::table('payment_voucher_detail')->insert($pv_detail);
                }
            }

            // Overhead Cost
            if( $pv['cs_id'] == 3){

                $pv_detail['pv_id'] = $pv_id;
                $upsert_flag = PaymentVoucherDetail::where('pv_id', $pv_id)->where('oci_id', $pv_detail['oci_id'])->exists();
                if( $upsert_flag ){

                    DB::table('payment_voucher_detail')->where('pv_id', $pv_id)->where('oci_id', $pv_detail['oci_id'])->update($pv_detail);
                }else{

                    DB::table('payment_voucher_detail')->insert($pv_detail);
                }
            }

            // Update Amount
            $pv_total_amount = DB::table('payment_voucher_detail')->where('pv_id', $pv_id)->sum('amount');
            DB::table('payment_voucher')->where('pv_id', $pv_id)->update(['total_amount' => $pv_total_amount]);

            // Delete Zero Amount
            DB::table('payment_voucher_detail')->where('pv_id', $pv_id)->where('oci_id', $pv_detail['oci_id'])->where('amount', 0)->delete();

            DB::commit();

            if( count($employee_advance) >= 1 ){
                $process_result['ea_id'] = $ea_id;
            }else{
                $process_result['pv_id'] = $pv_id;
            }

            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['ea_id'] = $data['ea']['ea_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }


    public function cancelPaymentVoucher($pv_cancel){

        DB::beginTransaction();

        //try{

            $pv_id = $pv_cancel['pv_id'];

            unset($pv_cancel['pv_id']);
            DB::table('payment_voucher')->where('pv_id', $pv_id)->update($pv_cancel);

            DB::commit();

            $process_result['pv_id'] = $pv_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Cancel Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['pv_id'] = $pv_cancel['pv_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'site <br> ' . $e->getLine();

        //     return $process_result;
        // }
    }


}
