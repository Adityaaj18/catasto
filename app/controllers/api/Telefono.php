<?php

namespace simplerest\controllers\api;

use simplerest\core\libs\DB;
use simplerest\libs\OpenApi;
use simplerest\core\libs\Url;
use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\controllers\MyApiController; 

class Telefono extends MyApiController
{ 
    static protected $soft_delete = true;
    static protected $connect_to = [
		
	];

    /*
        Los campos ocultos desde la API si son entregados a la vista
        pero desde la vista pueden ser "marcados" como no-visibles
        por ejemplo con un data-visibility="false"
    */
    static protected $hidden = [
        'response',
        'result',
        'status'
    ];

    static protected $hide_in_response = false;

    function onPost($id, Array &$data)
    {  
        $url  = "https://rintraccio.openapi.it/telefoni/";

        // if (config()['mock_responses']){
        //     OpenApi::mock(ETC_PATH . 'mocks/telefono.json'); // <------- especifico del endpoint
        // }
        
        $res  = OpenApi::makeRequest($data, $url, "?r=rintracio&sub=telefoni");
        $dec = json_decode($res, true); ///

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
