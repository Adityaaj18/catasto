<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\controllers\MyApiController; 

class Indirizzo extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    static protected $hidden = [

    ];

    static protected $hide_in_response = false;

    function onPost($id, Array &$data)
    {       
        $url = 'https://catasto.openapi.it/richiesta/indirizzo/';

        $res = OpenApi::makeRequest($data, $url, "?r=realstate&sub=indirizzo");
        $dec = Strings::isJSON($res) ? json_decode($res, true) : $res; ///

        $_data     = $dec['data'];
        $status    = strtoupper($_data['status'] ?? $_data['stato'] ?? '');

        if ($dec['error'] !== null){
            response()->error("OpenAPI error", $dec['error'] ?? "Error", $dec['message'] ?? null);
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
