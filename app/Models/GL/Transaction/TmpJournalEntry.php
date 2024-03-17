<?php

namespace App\Models\GL\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;

class TmpJournalEntry extends Model {

    use HasFactory;

    protected $table = 'tmp_journal_entry';

    protected $primaryKey = 'tmp_je_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function addTmpJournalEntry($data){

        $journal_entry = $data['journal_entry'];

        //try{

            DB::table('tmp_journal_entry')->insert($journal_entry);

            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

		// }catch(\Exception $e){

        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Journal Entry Process -> Saving Process <br> ' . $e->getLine();

        //     return $process_result;
		// }

    }

}
