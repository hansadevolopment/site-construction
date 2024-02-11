<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SiteMM\Master\Site;
use App\Models\SiteMM\SiteForcast\SiteTask;
use App\Models\SiteMM\SiteForcast\SiteSubTask;

class EmployeeAdvance extends Model {

    use HasFactory;

    protected $table = 'employee_advance';

    protected $primaryKey = 'ea_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getSite(){

        return Site::where('site_id', $this->site_id)->first();
    }

    public function getTask(){

        return SiteTask::where('task_id', $this->task_id)->first();
    }

    public function getSubTask(){

        return SiteSubTask::where('sub_task_id', $this->sub_task_id)->first();
    }


}
