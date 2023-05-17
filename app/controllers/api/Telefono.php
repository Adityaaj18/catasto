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
        //throw new \Exception("FOO");

        $url  = "https://rintraccio.openapi.it/telefoni/";

        if (config()['mock_responses']){
            OpenApi::mock(ETC_PATH . 'mocks/telefono.json'); // <------- especifico del endpoint
        }
        
        $res  = OpenApi::makeRequest($data, $url, "?r=rintracio&sub=telefoni");

        /*
            La respuesta puede ser variada, incluyendo:

            Array
            (
                [success] => false
                [message] => Insufficient Credit in Wallet: 0.7 > 0.6
                [error] => 223
                [data] =>
                [trace] => WyJpbmRleC5waHBAMjY2IiwiY2xhc3MuQXZXcy5waHBAMjE3IiwiaW5kZXgucGhwQDQ0MiJd
            )

            o...

            array (
                'success' => false,
                'message' => 'cf_piva not valid',
                'error' => 219,
                'data' => NULL,
                'trace' => 'WyJpbmRleC5waHBAMjE0IiwiY2xhc3MuQXZXcy5waHBAMjE3IiwiaW5kZXgucGhwQDQ0MiJd',
            )
        */

        /*
            Ej:

            {
                "status": 219,
                "error": {
                    "type": null,
                    "code": null,
                    "message": "cf_piva not valid",
                    "detail": null,
                    "location": null
                }
            }
        */

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
