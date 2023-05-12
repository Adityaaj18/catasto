<?php

namespace simplerest\libs;

use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;

class OpenApiIT
{
    static function getParams($table){
        $defs = get_defs($table, 'main', false, false);

        return $defs;
    }

    /*
        data=XXXXXXXXXXXXXXXXXXXX
    */
    static function decode(string $res){
        $res = trim($res);

        if (empty($res)){
            return;
        }

        if (!Strings::startsWith('data=', $res)){
            return;
        }

        $str = substr($res, 5);
                
        $str = urldecode($str);
        $str = json_decode($str, true);

        return $str;
    }

    static function makeRequest($data, $url, $callback = null){
        $cfg = config();

        $client = ApiClient::instance()
        ->setHeaders([
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $cfg['openapi_it_key']
        ]);

        if ($callback !== null){
            $data["callback"] = [
                "url"   => $cfg['callback_base_url'] . $callback,
                "field" => "data"
            ];
        }

        $client
        ->disableSSL()
        //->cache()
        //->redirect()
        ->setBody($data)
        ->setUrl($url)
        ->post()
        ->getResponse();

        $res = $client->data();  

        return $res;
    }


}

