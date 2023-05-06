<?php

use Google\Service\CloudNaturalLanguage\Document;
use simplerest\core\libs\HtmlBuilder\Bt5Form;
    use simplerest\core\libs\HtmlBuilder\Tag;

    Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);
?>

<?php

$modal_body = "Some content...";

// Modal

echo tag('modal')
->header(
    tag('modalTitle')->text('Nuevo / Editar') . 
    tag('closeButton')->dataBsDismiss('modal')
)
->body(
    $modal_body
)
->footer(
    tag('closeModal') .
    tag('button')->id("save_row")->text('Guardar')
)
->options([
    //'fullscreen',
    //'center',
    //'scrollable'
])
//->show() ///
->id('row-form-modal');

echo tag('openButton')->target("row-form-modal")->content('Launch demo modal')->class('my-3');


