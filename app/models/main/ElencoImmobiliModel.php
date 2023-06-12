<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\core\libs\Logger;
use simplerest\schemas\main\ElencoImmobiliSchema;

class ElencoImmobiliModel extends MyModel
{
	protected $hidden = [
		'created_at',
		'updated_at',
		'deleted_at'
	];

	protected $not_fillable = [];

	protected $field_names   = [
		'tipo_catasto'   => 'Tipo catasto',
		'sezione_urbana' => 'Sezione urbana',

		'created_at'     => 'Created at',
		'updated_at'     => 'Updated at',
		'deleted_at'     => 'Deleted at'
	];
	
	protected $formatters    = [];
	

    function __construct(bool $connect = false){
        parent::__construct($connect, ElencoImmobiliSchema::class);

        /*
            Default sort
        */
        $this->order       = [
            $this->id() => 'DESC'
        ];
	}	
}

