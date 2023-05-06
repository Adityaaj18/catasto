<?php

namespace simplerest\controllers;

use simplerest\controllers\MyController;
use simplerest\core\Strings;


class CatastoController extends MyController
{
    function __construct()
    {
        parent::__construct();        
    }

    function index()
    {
        /*
            Contiene respuesta para "Rintracio -> telefono"
        */
        $str = "data=%7B%22status%22%3A%22COMPLETED%22%2C%22date_request%22%3A%222023-04-27+17%3A13%3A02%22%2C%22date_completion%22%3A%222023-04-27+17%3A13%3A56%22%2C%22id%22%3A%22644a90d9efe0a35a8820bf09%22%2C%22cf_piva%22%3A%22PLLPLN50E03A161H%22%2C%22callback%22%3A%7B%22url%22%3A%22https%3A%5C%2F%5C%2Fcatasto.000webhostapp.com%5C%2Fcallback.php%22%2C%22field%22%3A%22data%22%2C%22method%22%3A%22POST%22%2C%22data%22%3A%5B%5D%7D%2C%22tipo%22%3A%5B%22telefoni%22%5D%2C%22esito%22%3A%7B%22codice%22%3A200%2C%22info%22%3A%22OK%22%7D%2C%22timestamp%22%3A1682608345%2C%22owner%22%3A%22fabio56istrefi%40gmail.com%22%2C%22soggetto%22%3A%7B%22code%22%3A%22PLLPLN50E03A161H%22%2C%22utenze%22%3A%5B%223384938960%22%2C%223938862423%22%5D%7D%7D";
        
        $reqs = explode('data=', trim($str));
        
        foreach($reqs as $ix => $req){
            $str = trim($req);

            if (empty($str)){
                continue;
            }

            $str = urldecode($str);

            $str = json_decode($str, true);
            
            dd($str);
            
            dd($str['soggetto']['utenze'], $str['cf_piva']. $ix);

        }
                        
    }
}

