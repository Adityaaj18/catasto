<?php

namespace simplerest\models\main;

use simplerest\models\MyModel;
use simplerest\schemas\main\IndirizzoSchema;

class IndirizzoModel extends MyModel
{
	protected $hidden = [
		'created_at',
		'updated_at',
		'deleted_at'
	];
	
	protected $not_fillable = [];

	protected $field_names  = [];
	protected $formatters    = [];

    function __construct(bool $connect = false){
        parent::__construct($connect, IndirizzoSchema::class);
	}	
}

