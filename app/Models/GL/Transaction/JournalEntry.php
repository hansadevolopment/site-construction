<?php

namespace App\Models\GL\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class JournalEntry extends Model {

    use HasFactory;

    public function addJournalEntry($data){

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

    public function getTmpJournalEntry(){

        $sql_query = " 	select		je_id, je_date, remark, sa_id, sa_name, act.short_name, amount
                        from		tmp_journal_entry tje inner join acc_type act on tje.acc_id = act.acc_id
                        where		saved_by = ? 
                        order by	tje.acc_id, tmp_je_id desc;  ";
					   
		$result = DB::select($sql_query, [Auth::id()]);

		return $result;
    }


}
