<?php

namespace App\Models\SiteMM\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model {

    use HasFactory;

    protected $table = 'status';

    protected $primaryKey = 'status_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';


}
