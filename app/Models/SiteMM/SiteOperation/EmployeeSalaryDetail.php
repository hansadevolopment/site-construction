<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryDetail extends Model {

    use HasFactory;

    protected $table = 'employee_salary_detail';

    protected $primaryKey = 'esd_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';



}
