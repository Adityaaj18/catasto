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
    function onPost($id, Array &$data)
    {       
        $piva_cf_or_id = $data['piva_cf_or_id'];

        $url = 'https://imprese.openapi.it/soci/' . $piva_cf_or_id;

        $res = OpenApi::makeRequest($data, $url);
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
