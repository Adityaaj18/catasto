
/*
    https://stackoverflow.com/a/66545752/980631
*/
function hideModal(id) {
    // Esto por alguna razon destruye el modal
    const modal_el  = document.querySelector('#'+id);
    const modal_obj = bootstrap.Modal.getInstance(modal_el);

    if (modal_obj ==  null){
        return;
    }

    modal_obj.hide();
}

function showModal(id) {
    const modal_el  = document.querySelector('#'+id);
    let   modal_obj = bootstrap.Modal.getInstance(modal_el);

    if (modal_obj ==  null){
        modal_obj = new bootstrap.Modal(modal_el, {
            // el modal no se cerrará cuando se hace clic en el fondo del modal o se presiona la tecla ESC.
            backdrop: 'static'
        });
    }

    modal_obj.show();
}

const hide_elem_by_id = id => {
    $(`#${id}`).hide();
}

const show_elem_by_id = id => {
    $(`#${id}`).show();
}


