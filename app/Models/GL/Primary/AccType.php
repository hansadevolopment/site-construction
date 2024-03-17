<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccType extends Model {

    use HasFactory;

    protected $table = 'acc_type';

    protected $primaryKey = 'acc_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';
}
