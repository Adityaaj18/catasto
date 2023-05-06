
/*
  Server Side Validation
*/

(function () {
    'use strict'
  
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var bt_forms = document.querySelectorAll('.bt-form')
  
    // Loop over them and prevent submission
    Array.prototype.slice.call(bt_forms)
      .forEach(function (bt_forms) {
        bt_forms.addEventListener('submit', function (event) {
         
          // if (!bt_forms.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          // }
  
          //bt_forms.classList.add('was-validated')
       
        }, false)
      })
  })()