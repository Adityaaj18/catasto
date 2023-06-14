<?php

namespace simplerest\pages\admin;

use simplerest\core\libs\DB;

class Tabulator /* extends Page */
{
    public $tpl_params    = [
        'title'      => 'DataGrid',
        'page_name'  => ''
    ];

    function __construct()
    {   
        css_file('vendors/tabulator/dist/css/tabulator.min.css?v=1');
        //css_file('vendors/tabulator/dist/css/tabulator_bootstrap5.min.css');
        js_file('vendors/tabulator/dist/js/tabulator.min.js?v=1');
    }

    function index($entity = '')
    { 
        $tenant_id = DB::getCurrentConnectionId(true);

        $incl_rels = false;

        if (!empty($entity)){    
            $defs = get_defs($entity, $tenant_id, false, false, $incl_rels);
            
            $this->tpl_params['page_name'] = ucfirst(
                str_replace('_', ' ', $entity)
            );         
        
            /*
                Deberia recibir el contenido de $right_cont desde el controller

                Ademas deberia poder pasarse una funcion callback que procese a $main (content)
                de forma de alterar su contenido de forma condicional desde el controler <--------- HOOK
            */

            $main = get_view("datagrids/tabulator/tabulator", [
                'entity'   => $entity,
                'tenantid' => DB::getCurrentConnectionId(true),
                'defs'     => $defs
            ]);  
        } 

        /* 
            Esto es una (sub) vista y deberia estar en views y  
            deberia ser pasado desde el controller 
        */

        return '
        <div class="row">
            <div class="col-sm-12 col-md-10 col-9">
            '. ($main ?? '') . '
            </div>
            <div class="col-sm-0 col-md-2 col-3">
            '. ($right_cont ?? '') . '
            </div>
        </div>';
    
    }   
}

