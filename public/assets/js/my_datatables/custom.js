/*
  Computed properties 
*/

function addComputedFields(columns, field) {  
    // Ejemplo: Agregar un campo computado después del campo "id"
    if (field === "id") {
      const computedField = {
        title: "Res?",
        field: "checkbox",
        formatter: function (cell, formatterParams, onRendered) {
          const data = cell.getRow().getData();
          const result = data.result;
          const isChecked = result !== null && result !== "";
  
          return `<input type="checkbox" ${isChecked ? "checked" : ""}>`;
        },
        headerSort: false,
        hozAlign: "center",
        width: 58
      };
  
      // Buscar la posición del campo "id"
      const idIndex = columns.findIndex(column => column.field === "id");
  
      // Insertar el campo computado después del campo "id"
      columns.splice(idIndex + 1, 0, computedField);
    }
  
    // mas propiedades computadas
  }
  
  /*
    Eventos 
  */
  
  const onViewResult = () => {
      $('#col-result').removeAttr('readonly');
      $('#col-result').val($('#col-result').val() + ' ');
      $('#col-result').trigger('input');	
  }