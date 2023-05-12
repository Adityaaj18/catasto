<?php

namespace simplerest\libs;

use simplerest\core\libs\ApiClient;

class OpenApiIT
{
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

        $res = json_decode($client->data(), true);  

        return $res;
    }


}

