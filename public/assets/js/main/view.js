/** Constantes Globales */

const uid   = localStorage.getItem("uid");
const token = localStorage.getItem("access_token");
const roles = localStorage.getItem("roles");

const defaultHeaders = {
  "Authorization": `Bearer ${token}`,
  "Accept-Language": 'es'
};

const axiosInstance = axios.create({
  baseURL: `${window.location.origin}/api/v1/`,
  timeout: 1000,
  headers: defaultHeaders,
});

/** Definiciones de la vista */
const viewData = {};

/** Variables y constantes globales para el DataGrid*/
var   table = undefined;
const columns = [];

// * Obtiene los parámetros para la vista provistos desde el servidor mediante campos ocultos
// * Los parámetros están codificados en base64
document.addEventListener("DOMContentLoaded", () => {
  viewData.defs     = var_decode('defs');
  viewData.tenantid = var_decode('tenantid');
  viewData.entity   = var_decode('entity');
  viewData.userId   = uid;
  viewData.roles    = roles
  viewData.api_url  = `${window.location.origin}/api/v1/${viewData.entity}`;

  defaultHeaders["X-TENANT-ID"] = viewData.tenantid
})

/** Event Listeners y definición del DataGrid */
document.addEventListener("DOMContentLoaded", () => {
  /** Validación del Token */
  if (token == null) {
    console.log("Sin acceso");
  }

  /** Envío del formulario en el modal */
  document.getElementById("mainForm").addEventListener("submit", (e) => {
    e.preventDefault();
    const serialized = $(e.currentTarget).serializeArray();
    const jsonData = {};
    serialized.forEach((item) => (jsonData[item.name] = item.value));
    save_row(jsonData, jsonData.id);
  });

  /** Limpieza del formulario al cerrar modal */
  document
    .getElementById("row-form-modal")
    .addEventListener("hidden.bs.modal", function (e) {
      clearForm("mainForm"); // Reinicia los campos visibles del formulario
      $("#col-id").val(""); // Limpia el campo oculto del Id
    });


  async function create_collection(entity, selectedRows) {
    return axiosInstance
      .post("collections", {
        entity: entity,
        refs: selectedRows,
      })
  }

  async function mass_delete(colection_id) {
    const url = `${window.location.origin}/api/v1/collections/${colection_id}?entity=${viewData.entity}`;
    const headers = new Headers();

    headers.append("X-TENANT-ID", viewData.tenantid);
    headers.append("Authorization", `Bearer ${token}`);
    headers.append("Accept", "application/json");

    var requestOptions = {
      method: "DELETE",
      mode: "cors", // no-cors, *cors, same-origin
      headers: headers,
    };

    return await fetch(url, requestOptions)
      .then((response) => {
        return response.json();
      })
      .catch((error) => {
        console.log("error", error);
        Promise.reject(error);
      });
  }

  /** Botón de eliminación masiva */
  document.getElementById("btn-multiple-delete").onclick = function () {
    // checked
    const selectedRows = table.getSelectedData().map((item) => item.id);

    // checked_count
    const checked_count = selectedRows.length

    if (checked_count < 0) {
      return;
    }

    if (!confirm(`Está por borrar ${checked_count} registros. Está seguro?`)) {
      return;
    }

    console.log("Procedo a borrar .... !", selectedRows);

    const col_res = create_collection(viewData.entity, selectedRows);

    col_res
    .then((res) => {
      return res.data;
    })
    .then((res) => {
      let col_id = res.data.id;   
      let resp   = mass_delete(col_id);

      resp.then(res => {

        table.setData(viewData.api_url)
          .catch(function (error) {
            //handle error loading data
            console.log('error loading data');
          });
      })
    });
  };


  /** Definición del DataGrid */

  /**
   * Columns toggler
   * https://tabulator.info/docs/5.4/menu#overview
   */
  const genColumnsToggler = (datagrid) => {
    var menu = [];
    var columns = datagrid.getColumns();

    for (let column of columns) {

      if (column.getDefinition().title === undefined) continue;

      //create checkbox element using font awesome icons
      let icon = document.createElement("i");
      icon.classList.add("fas");
      icon.classList.add(column.isVisible() ? "fa-check-square" : "fa-square");

      //build label
      let label = document.createElement("span");
      let title = document.createElement("span");

      title.textContent = " " + column.getDefinition().title;

      label.appendChild(icon);
      label.appendChild(title);
      label.addEventListener('click', (e) => {
        e.stopPropagation();

        //toggle current column visibility
        column.toggle();

        //change menu item icon
        if (column.isVisible()) {
          icon.classList.remove("fa-square");
          icon.classList.add("fa-check-square");
        } else {
          icon.classList.remove("fa-check-square");
          icon.classList.add("fa-square");
        }
      })

      //create menu item
      menu.push({
        label
      });
    }

    return menu;
  };

  columns.push({
    formatter: "rowSelection",
    // titleFormatter: "rowSelection", // TODO Cambiar por un formatter personalizado para que solo seleccione los registros autorizados
    // titleFormatterParams: {},
    headerHozAlign: "center",
    hozAlign: "center",
    headerSort: false,
    maxWidth: 40,
    width: 40,
  });

  for (var field in viewData.defs) {
    let obj = {};
    
    let def             = viewData.defs[field];
    obj.field           = field;
    obj.title           = def.name === undefined ? ucfirst(field) : def.name;
    obj.formatter       = getFormatter(def.type),
    obj.formatterParams = getFormatterParams(def.type, field)
    obj.editor          = viewData.defs[field].fillable != 0;
    obj.hozAlign        = getAlignment(def.type)
    obj.vertAlign       = "middle"; // TODO: Ajustar según el tipo de campo

    columns.push(obj);
  }

  columns.push({
    //column definition in the columns array
    formatter: function (cell) {
      const { id } = cell.getRow().getData();
      const belongs_to = cell.getRow().getData().belongs_to;
      const deleteAllowed = allowDelete(belongs_to, viewData.userId, viewData.roles)

      const del_btn = `<button type="button" onclick="deleteBtn(${id})" class="btn btn-danger tabulator-btn" ><i class="fa fa-trash text-white"></i></button>`
      const edt_btn = `<button type="button" onclick='editBtn(${id})' class="btn btn-success tabulator-btn" ><i class="fa fa-pen text-black"></i></button>`;

      return `<div class="d-flex gap-2 justify-content-center">${edt_btn}${del_btn}</div>`;
    },
    maxWidth: 70,
    width: 70,
    hozAlign: "right",
    frozen: true
  });

  table = new Tabulator("#example-table", {
    /** AJAX */
    ajaxURL: `${window.location.origin}/api/v1/${viewData.entity}`,
    ajaxParams: {
      tenantid: viewData.tenantid,
      token,
    },
    ajaxResponse: function (url, params, response) {
      return {
        data: response.data[viewData.entity],
        last_page: response.last_page,
      };
    },

    /** Paginación */
    paginationSize: 50, // <--- puede implicar modificar el height
    paginationMode: "remote",
    progressiveLoad: "scroll", // obligatorio?

    /** Columnas */
    columns: columns,

    /** Formato para registros eliminados con soft delete */
    rowFormatter: function (row) {
      var data = row.getData();
      if (data.deleted_at != null) {
        row.getElement().style.backgroundColor = "#ff3333";
      }
    },

    /** Datos reactivos */
    reactiveData: true,

    /** Renderización */
    height: "325px",
    layout: "fitDataFill",
    // responsiveLayout: "collapse",
    resizableColumnFit: true,
    placeholder: "No data",

    /** Filtro de selección: habilita / deshabilita la selección en base al propietario del registro */
    selectableCheck: function (row) {
      const belongs_to = row.getData().belongs_to;
      //return allowDelete(belongs_to, viewData.userId, viewData.roles)

      return true
    },
  })

  /** Edición de celdas del DataGrid */
  table.on("cellEdited", async (proxy) => {
    const { oldValue, value } = proxy._cell;
    const { field } = proxy.getColumn()._column;
    const row = proxy.getData();

    if (value === oldValue) {
      return;
    }

    const editedData = {};
    editedData[field] = value;
    save_row(editedData, row.id);
  });

  /** Descarga del DataGrid */
  document.getElementById("btn-download").addEventListener("click", () => {
    table.download("pdf", "tabulator.pdf", {
      orientation: "portrait", //set page orientation to portrait
      title: "", //add title to report
    });
  });

  table.on('tableBuilt', () => {

    // Se genera el menú para mostrar u ocultar las columnas de la tabla
    const columnsTogglerContainer = document.getElementById('tabulator-columns-toggler-container')
    const columnsTogglerItems = genColumnsToggler(table)
    columnsTogglerItems.forEach((item) => columnsTogglerContainer.append(item.label))

  })

  /** Termina la definición del DataGrid */
});

/** Devuelve el formateador de Tabulator */
const getFormatter = (fieldType) => {
  switch (fieldType) {
    case 'str':
      return 'plaintext'
    case 'email':
      return 'link'
    case 'bool':
      return 'tickCross'

    default:
      return 'plaintext'
  }
}

/** Devuelve el formateador de Tabulator */
const getAlignment = (fieldType) => {
  switch (fieldType) {
    case 'str':
      return 'left'
    case 'email':
      return 'left'
    case 'bool':
      return 'center'

    default:
      return 'plaintext'
  }
}

/** Devuleve los parámetros para el formateador de taulator */
const getFormatterParams = (fieldType, fieldName) => {
  switch (fieldType) {
    case 'str':
      return {}
    case 'email':
      return {
        labelField: fieldName,
        urlPrefix: "mailto://",
        target: "_blank",
      }

    default:
      return {}
  }
}

/**
 * * Determina si un registro puede ser eliminado o no
 * @param {any} belongsTo Id del usuario al que pertenece el registro
 * @param {any} userId Id del usuario actual
 * @param {any} userRole Rol del usuario actual // TODO Actualizar en caso de que se incluyan varios roles
 * @returns {any}
 */
const allowDelete = (belongsTo, userId, userRole) => {
  return Boolean((belongsTo && String(belongsTo) === String(userId)) || (userRole === 'superadmin') || (userRole === 'admin'))
}

/** Funciones para los botones del DataGrid */
const deleteBtn = (id) => {
  if (!confirm("Seguro de borrar?")) {
    // TODO: Agregar notificación de SweetAlert
    return;
  }
  axiosInstance
    .delete(`${viewData.entity}/${id}`)
    .then(() => table.deleteRow(id));
};

const editBtn = (id) => {
  axiosInstance
    .get(`${viewData.entity}/${id}`)
    .then(({ data }) => fillForm(data.data, "col-"))
    .then(() => showModal("row-form-modal"));
};

/** Funciones de CRUD */

async function save_row(jsonData, id = null) {
  delete jsonData.id;
  // TODO: Implementar setNotification(error)
  const uri = id
    ? `${window.location.origin}/api/v1/${viewData.entity}/${id}`
    : `${window.location.origin}/api/v1/${viewData.entity}`;
  axiosInstance
    .request({
      url: uri,
      method: id ? "PATCH" : "POST",
      headers: defaultHeaders,
      data: jsonData,
    })
    .then(({ data }) => {
      // TODO: Importante revisar, el data response devuelto en POST difiere mucho del devuelto en PATCH
      if (id) {
        const row = { ...data.data, id: id };
        table.updateData([row]);
      } else {
        const row = { ...data.data[viewData.entity], id: data.data.id };
        table.addData([row], true);
      }
      hideModal("row-form-modal");
    })
    .catch((error) => {

      const errors = error?.response?.data?.error?.detail; // Errores de validación

      if (errors) {

        let validations = {};
        for (let field in jsonData) {
          validations[field] = [{ error: false }]; // Inicializa con error false todos los campos
        }
        validations = { ...validations, ...errors }; // Incorpora los errores de validación
        setFormValidations(validations);

      } else {

        // TODO Incluir un sweet alert de error en el servidor
        console.log(error.toJSON())

      }
    });
}

