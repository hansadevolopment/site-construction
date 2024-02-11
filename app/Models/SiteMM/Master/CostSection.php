<?php

namespace App\Models\SiteMM\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostSection extends Model {

    use HasFactory;

    protected $table = 'cost_section';

    protected $primaryKey = 'cs_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

}
