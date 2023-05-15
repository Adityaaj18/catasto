<?php

namespace simplerest\libs;

use simplerest\core\libs\Logger;
use simplerest\core\libs\Strings;
use simplerest\core\libs\ApiClient;

class OpenApi
{
    static private $mock;

    static function mock($mock){
        static::$mock = $mock;
    }

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

    /*
        En teoria, existe un Sandbox que funciona con otro token
        y los endpoints comenzarian con https://test.{resto-del-endpoint}
    */
    static function makeRequest($data, $url, $callback = null, $use_sandbox = false){
        $cfg = config();

        $token     = $cfg['openapi_it_key'];
        $test_mode = $cfg['openapi_testing_mode'];

        if ($use_sandbox && $test_mode){
            $token = $cfg['sanbbox_openapi_it_key'];
            $url   = str_replace('https://','https://test.', $url);
        }

        $client = ApiClient::instance()
        ->setHeaders([
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);

        if ($callback !== null){
            $data["callback"] = [
                "url"   => $cfg['callback_base_url'] . $callback,
                "field" => "data"
            ];
        }

        $client
        ->when(static::$mock, function($it){
            $it->mock(static::$mock);
        })
        ->disableSSL()
        //->clearCache()
        //->cache(17 * 3600)
        //->redirect()
        ->setBody($data)
        ->setUrl($url)
        ->post()
        ->getResponse();

        $res = $client->data();  

        // a veces viene basura antes y luego del JSON 
        if (isset($res['message']) && Strings::contains('{', $res['message']) && Strings::contains('}', $res['message'])){
            $res['message'] = '{' . Strings::after($res['message'], '{');
            $res['message'] =       Strings::beforeLast($res['message'], '}') . '}';
        }

        Logger::dump($client->dump(), null, true);
        Logger::dump($res, null, true);

        return $res;
    }


}

