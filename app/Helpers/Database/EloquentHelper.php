<?php

namespace App\Helpers\Database;

class EloquentHelper{

    public static function recordExists($result){

        if( is_null($result)) {

            return FALSE;
        }

        $data_type = gettype($result);
        if( ($data_type == 'integer') || ($data_type == 'string') || ($data_type == 'double') || ($data_type == 'boolean')){

            return TRUE;

        }else{

            if($data_type == 'array'){

                if( count($result) >= 1 ){

                    return TRUE;

                }else{

                    return FALSE;
                }

            }elseif($data_type == 'object'){

                if( empty($result) ){

                    return FALSE;

                }else{

                    $object_var_result = (get_object_vars($result));
                    if (count($object_var_result) >= 1){

                        return TRUE;

                    }else{

                        return FALSE;
                    }
                }
            }
        }
    }

}
