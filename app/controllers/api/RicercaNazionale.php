<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\controllers\MyApiController; 

class RicercaNazionale extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function onPostingAfterCheck($id, Array &$data)
    {       
        $url = 'https://catasto.openapi.it/richiesta/ricerca_nazionale/';

        $res = OpenApi::makeRequest($data, $url, "?r=realstate&sub=ricerca_nazionale");

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
