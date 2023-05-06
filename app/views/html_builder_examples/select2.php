<h3>Select 2</h3>

<?php

use simplerest\core\libs\HtmlBuilder\Tag;

Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
        
echo tag('select2')->name('sexo')
->options([
    'mujer'  => 1,
    'hombre' => 2,
    'indef'  => 3,
])
->default(1)
->placeholder('Su sexo')
->attributes(['class' => 'my-3']);

