<?php

namespace App\Models\GL\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalEntry extends Model {

    use HasFactory;

    protected $table = 'journal_entry';

    protected $primaryKey = 'je_id';

    public $incrementing = true;

    public $timestamps = true;

    protected $dateFormat = 'U';

    public function saveJournalEntry($data){

        $journal_entry = $data['journal_entry'];
        $journal_entry_detail = $data['journal_entry_detail'];
        $general_ledger_entry = $data['general_ledger_entry'];

        DB::beginTransaction();

        //try{

            // Journal Entry Table
            if( $journal_entry['je_id'] == '#Auto#' ){

                unset($journal_entry['je_id']);
                DB::table('journal_entry')->insert($journal_entry);
                $je_id = DB::getPdo()->lastInsertId();

            }else{

                $je_id = $journal_entry['je_id'];
                DB::table('journal_entry')->where('je_id', $journal_entry['je_id'])->update($journal_entry);
            }

            // Journal Entry Detail Table
            foreach($journal_entry_detail as $row => $value){

                $value['je_id'] = $je_id;
                DB::table('journal_entry_detail')->insert($value);
            }

            //Generate General Ledger Id
            $gl_id = DB::table('general_ledger')->max('gl_entry_id') + 1;

            // General Ledger Table
            foreach($general_ledger_entry as $row => $value){

                $value['gl_entry_id'] = $gl_id;
                $value['gl_entry_sub_id'] = ($row + 1);
                $value['source_id'] = $je_id;

                DB::table('general_ledger')->insert($value);
            }

            //Clear tmp journal entry
            DB::table('tmp_journal_entry')->where('saved_by', Auth::user()->id)->delete();


            DB::commit();

            $process_result['je_id'] = $je_id;
            $process_result['process_status'] = TRUE;
            $process_result['front_end_message'] = "Saving Process is Completed successfully.";
            $process_result['back_end_message'] = "Commited.";

            return $process_result;

        // }catch(\Exception $e){

        //     DB::rollback();

        //     $process_result['je_id'] = $data['employee_salary']['je_id'];
        //     $process_result['process_status'] = FALSE;
        //     $process_result['front_end_message'] = $e->getMessage();
        //     $process_result['back_end_message'] = 'Journal Entry ' . $e->getLine();

        //     return $process_result;
        // }

    }


}
