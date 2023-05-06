<?php

use simplerest\core\libs\HtmlBuilder\Bt5Form;
use simplerest\core\libs\HtmlBuilder\Tag;

Tag::registerBuilder(\simplerest\core\libs\HtmlBuilder\Bt5Form::class);

echo tag('shadow')
->content([
    '<div class="text-center text-uppercase">
        <h3>CATASTO</h3>
    </div>',
    '<div class="text-center">
        <p>Modules</p>
    </div>'
])
->class("mt-3 p-3 mb-5");
?>

<div class="row">

    <div class="col-sm-offset-4 col-sm-4">   
    </div>
   
    <div class="col-sm-4">
        <div class="card zooming-card shadow py-3">
            <div class="bd-vertical-align-wrapper">
                <div class=" bd-joomlaposition-19 clearfix">
                    <div class=" bd-block-16 bd-own-margins ">

                        <div class="text-center text-uppercase">
                            <h4>ITALY'S TERRITORY</h4>
                        </div>

                        <div class="bd-blockcontent bd-tagstyles">
                            <div class="custom">
                                <p><img class="bd-imagelink-54 bd-own-margins bd-imagestyles" style="display: block; margin-left: auto; margin-right: auto;" src="<?= asset('img/icons/teams_icon.jpg') ?>" width="80"></p>
                                <p class=" bd-textblock-105 bd-content-element" style="text-align: center;">Get personal data</p>
                                <p style="text-align: center;"><a class="btn btn-primary" href="/admin/tabulator/ricerca"> OPEN</a></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</div>