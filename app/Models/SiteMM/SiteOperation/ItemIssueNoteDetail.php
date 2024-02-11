<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SiteMM\SiteOperation\ItemIssueNote;

class ItemIssueNoteDetail extends Model {

    use HasFactory;

    protected $table = 'item_issue_note_detail';

    protected $primaryKey = 'iind_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function iin(){

        return $this->belongsTo(ItemIssueNote::class, 'iin_id');
    }


}
