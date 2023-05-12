<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\schemas\main\RicercaPersonaSchema;

class RicercaPersonaModel extends MyModel
{
	protected $hidden       = [];

	protected $not_fillable = [];

	protected $field_names  = [
	];
	
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, RicercaPersonaSchema::class);
	}	
}

