<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\core\libs\Logger;
use simplerest\controllers\MyApiController; 

class Soci extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

     /*
        Este no requiere callback
    */
    function onPostingAfterCheck($id, Array &$data)
    {       
        $piva_cf_or_id = $data['piva_cf_or_id'];

        $url = 'https://imprese.openapi.it/soci/' . $piva_cf_or_id;

        $res = OpenApi::makeRequest($data, $url);

        if ($res['error'] !== null){
            response()->error("OpenAPI error", $res['error'], $res['message']);
        }

        $_data     = $res['data'];
        $status    = strtoupper($_data['status'] ?? $_data['stato'] ?? '');
       
        // $s_eq   = [
        //     'evasa' => 'SENT' // 'PENDING'
        // ];

        //dd($status, 'STATUS');

        if ($res['error'] !== null){
            response()->error("OpenAPI error", $res['error'] ?? "Error", $res['message'] ?? null);
        } 

        /*
            Actualizo en la DB
        */
        
        DB::table($this->table_name)
        ->find($id)
        ->fill(['status', 'response'])
        ->update([
            'status'   => $status,
            'response' => $res // $_data
        ]);

        // dd(DB::getLog());

        /*
            Lo envio en la respuesta
        */

        $data['status']   = $status;
        $data['response'] = $res;
    }         
} 
