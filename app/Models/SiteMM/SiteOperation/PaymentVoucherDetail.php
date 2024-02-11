<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucherDetail extends Model {

    use HasFactory;

    protected $table = 'payment_voucher_detail';

    protected $primaryKey = 'pvd_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';
}
