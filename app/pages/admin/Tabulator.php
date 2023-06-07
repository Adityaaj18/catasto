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
        css_file('vendors/tabulator/dist/css/tabulator.min.css');
        //css_file('vendors/tabulator/dist/css/tabulator_bootstrap5.min.css');
        js_file('vendors/tabulator/dist/js/tabulator.min.js');
    }

    function index($entity = '')
    { 
        $tenant_id = DB::getCurrentConnectionId(true);

        $incl_rels = false;

        if (!empty($entity)){    
            /** Definiciones de la vista */
            $defs = get_defs($entity, $tenant_id, false, false, $incl_rels);
            
            $this->tpl_params['page_name'] = ucfirst(
                str_replace('_', ' ', $entity)
            );         
            
            $main = get_view("datagrids/tabulator/tabulator", [
                'entity'   => $entity,
                'tenantid' => DB::getCurrentConnectionId(true),
                'defs'     => $defs
            ]);  
        } 

        return '
        <div class="row">
            <div class="col-9">
            '. ($main ?? '') . '
            </div>
            <div class="col-3">
            '. ($right_cont ?? '') . '
            </div>
        </div>';
    }   
}

