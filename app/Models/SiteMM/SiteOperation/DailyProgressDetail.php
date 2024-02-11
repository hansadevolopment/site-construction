<?php

namespace App\Models\SiteMM\SiteOperation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\SiteMM\SiteOperation\DailyProgress;
use App\Models\SiteMM\Master\Item;
use App\Models\SiteMM\Master\OverheadCostItem;

class DailyProgressDetail extends Model {

    use HasFactory;

    protected $table = 'dpr_detail';

    protected $primaryKey = 'dprd_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function getItemName(){

        $cs_id = DailyProgress::where('dpr_id', $this->dpr_id)->value('cs_id');

        if($cs_id == 1){

            return Item::where('item_id', $this->item_id)->value('item_name');

        }elseif($cs_id == 2){

            return OverheadCostItem::where('oci_id', $this->item_id)->value('oci_name');
        }
    }


}
