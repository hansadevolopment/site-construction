<?php

namespace App\Models\GL\Primary;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountType extends Model{

    use HasFactory;

    protected $table = 'account_type';

    protected $primaryKey = 'at_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';



}
