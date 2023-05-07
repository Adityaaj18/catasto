<?php

namespace simplerest\controllers\datagrids;

use simplerest\core\libs\DB;
use simplerest\core\Request;
use simplerest\controllers\MyController;

class TabulatorController extends MyController
{
    function __construct()
    {
        parent::__construct();
        
        css_file('vendors/tabulator/dist/css/tabulator.min.css');
        //css_file('vendors/tabulator/dist/css/tabulator_bootstrap5.min.css');

        js_file('vendors/tabulator/dist/js/tabulator.min.js');
    }

    function index()
    {
        $v = Request::getInstance()
        ->getQuery('v') ?? null;

        if ($v === null){
            $entity = Request::getInstance()
            ->getQuery('entity') ?? null;

            if ($entity == null){
                throw new \Exception("Debe pasarse el parametro 'entity' por url");
            }

            view("datagrids/tabulator/tabulator", [
                'entity'   => $entity,
                'tenantid' => DB::getCurrentConnectionId(true)
            ]);

        } else {

            $v = str_pad($v, 2, '0', STR_PAD_LEFT);

            view("datagrids/tabulator/test{$v}");
        }
      
    }   
    


}

