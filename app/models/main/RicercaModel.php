<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\schemas\main\RicercaSchema;

class RicercaModel extends MyModel
{
	protected $hidden       = [];
	protected $not_fillable = [];

	/*
		Podria incluir descripcion corta para el datatable
		y la larga para el modal de crear / modificar
	*/
	protected $field_names   = [
		'cf_socio'     => 'CF socio',
		'last_req'     => 'Request',
		'cf_ditta'     => 'CF ditta',
		'tipo_catasto' => 'Tipo catasto'
	];
	
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, RicercaSchema::class);
	}	
}

