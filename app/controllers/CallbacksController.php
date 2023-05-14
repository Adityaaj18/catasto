<?php

namespace simplerest\controllers;

use simplerest\libs\OpenApi;
use simplerest\core\libs\Logger;
use simplerest\controllers\MyController;

class CallbacksController extends MyController
{
    /*
        https://ticiwe.com/callbacks?r=realstate&sub=elenco_immobili
        https://ticiwe.com/callbacks?r=realstate&sub=prospetto_catastale
        https://ticiwe.com/callbacks?r=realstate&sub=ricerca_persona
        https://ticiwe.com/callbacks?r=realstate&sub=ricerca_nazionale
        https://ticiwe.com/callbacks?r=realstate&sub=indirizzo

        https://ticiwe.com/callbacks?r=company_info&sub=soci

        https://ticiwe.com/callbacks?r=rintracio&sub=telefoni

        Hacer uso de OpenApi::getParams($table) para saber que parametri buscar en la response
        y poder asi "poblar" la tabla
    */
    function index()
    {
        $req = file_get_contents("php://input");

        if ($req === null){
            return;
        }

        $dec = OpenApi::decode($req);

        if (empty($dec)){
            return;
        }

        // $r         = $_GET['r']   ?? null;
        // $sub       = $_GET['sub'] ?? null;
        
        // dd($r, 'R');
        // dd($sub, 'SUB');

        $status    = $dec['status']   ?? $dec['stato'] ?? '';
        $callback  = $dec['callback'] ?? null;
        $callback  = $callback['url'] ?? null;
        $result    = $dec['soggetto'] ?? $dec['risultato'] ?? '';
        $endpoint  = $dec['endpoint'] ?? null;


        $parametri = [];

        if (isset($dec['parametri'])){
            $parametri   = $dec['parametri'];
        } else {
            //$param_names = OpenApi::getParams($table);
        }

        dd($endpoint, 'ENDPOINT');
        dd($parametri,'PARAMS');
        dd($callback, 'CALLBACK');

        dd($status, 'STATUS');
        dd($result, 'RESULT');

        file_put_contents(LOGS_PATH . 'reqs.txt', json_encode($dec) . "\n", FILE_APPEND);

        /*
            - Generar un log con cada respuesta para que no se pierdan

            - Actualizar la TABLA correspondiente !!!

                . status
                . response

            Notas: 
            
                . El status puede ser "status" o "stato" 

                    "evasa" significa "COMPLETED"

                . Si hay callback apareceria tambien el la respuesta:
                
                    [callback] => Array
                    (
                        [url] => https://catasto.000webhostapp.com/callback.php
                        [field] => data
                        [method] => POST
                        [data] => Array
                            (
                            )

                    )

                . Muchas veces los parametros estan sueltos

                    [cf_piva] => CCCMRN48T59E625G

                pero otras veces son parte de "parametri"

                    [parametri] => Array
                    (
                        [tipo_catasto] => F
                        [provincia] => MATERA Territorio-MT
                        [comune] => F052#MATERA#0#0
                        [sezione] =>
                        [sezione_urbana] =>
                        [foglio] => 52
                        [particella] => 597
                    )

                . El resultado a veces es "soggetto" y otras veces "risultato"

                    [soggetto] => Array
                    (
                        [code] => CCCMRN48T59E625G
                        [utenze] => Array
                            (
                                [0] => 3273271075
                                [1] => 3405355951
                            )

                    )

                o ...

                    [risultato] => Array
                    (
                        [immobili] => Array
                            (
                                [0] => Array
                                    (
                                        [sezione_urbana] =>
                                        [foglio] => 52
                                        [particella] => 597
                                        [subalterno] => 1
                                        /...
                                        [partita] => Soppressa
                                        [id_immobile] => MzIzMyMzMjMzI0YjNTIjNTk3I0YwNTIjU29wcHJlc3NhIzEjICNNQVRFUkE=
                                    )

                                [1] => Array
                                    (
                                        [sezione_urbana] =>
                                        [foglio] => 52
                                        [particella] => 597
                                        [subalterno] => 2
                                        /...
                                        [partita] =>
                                        [id_immobile] => MzIzNCMzMjM0I0YjNTIjNTk3I0YwNTIjIzIjICNNQVRFUkE=
                                    )
                            // ...
                    )

        */

        exit;
    }
}

