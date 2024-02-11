<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoInquire extends Model {

    use HasFactory;

    protected $table = 'so_inquire_item';

    protected $primaryKey = 'soit_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';
}
