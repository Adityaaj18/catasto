var map = undefined;
var infoWindow = undefined
var markers = [];
const mapDefs = {}

document.addEventListener('DOMContentLoaded', () => {
  /** Inicialización de las definiciones para el mapa */
  const map64Defs = var_decode('gmap_props')
  for (const key in map64Defs) {
    mapDefs[key] = map64Defs[key]
  }
})

const validateLimits = (data) => {
  if (
    data.lat < mapDefs.limits.lat[0] ||
    data.lat > mapDefs.limits.lat[1] ||
    data.lng < mapDefs.limits.lng[0] ||
    data.lng > mapDefs.limits.lng[1]
  ) {
    console.error(
      `Coordenada para marcador fuera del mapa para (${data.lat},${data.lng})`
    );
    return false;
  }

  return true;
};

window.addEventListener("DOMContentLoaded", () => {
  /**
   * * Carga inicial de datos en el mapa
   * * dataProcessed: event triggered after data has been
   * * processed and the table has been rendered
   */
  table.on("dataProcessed", (data) => {
    const mapData = data.map((record) => {
      const tmp = { ...record, lng: record.lon }; // El campo lon debe corresponder con lng
      delete tmp.lon;
      return tmp;
    });

    if (mapData.length > 0) {
      initMap(mapData, mapData[0]);
    }
  });

  table.on("rowAdded", (row) => {
    const data = {
      ...row.getData(),
      lng: row.getData().lon,
      title: row.getData().nombre,
    };

    if (!validateLimits(data)) {
      return;
    }

    addMarker(data);
  });

  table.on("rowUpdated", (row) => {
    const data = {
      ...row.getData(),
      lng: row.getData().lon,
      title: row.getData().nombre,
    };

    if (!validateLimits(data)) {
      return;
    }

    updateMarker(data);
  });
});

/**
 * Inicializa el mapa
 * @param {any} mapData Datos del mapa
 * @param {any} {} Coordenas centrales
 * @returns {void}
 */
const initMap = (mapData, { lat, lng } = {}) => {
  const mapProps = {
    center: new google.maps.LatLng(
      Number.parseFloat(mapDefs.center.lat),
      Number.parseFloat(mapDefs.center.lng)
    ),
    zoom: 10,
  };

  // Global map
  map = new google.maps.Map(document.getElementById("googleMap"), mapProps);

  // Global infoWindow
  infoWindow = new google.maps.InfoWindow()

  mapData.forEach((el, i) => {
    const data = {
      id: el.id,
      title: el.nombre,
      lat: el.lat,
      lng: el.lng,
      magnitud: el.magnitud,
    };

    if (validateLimits(data)) {
      addMarker(data);
    }

  });
};

const updateMarker = ({ id, title, lat, lng, magnitud }) => {
  const i = markers.findIndex((mark) => {
    return mark.id === id;
  });

  const newLatLng = new google.maps.LatLng(lat, lng);

  markers[i].setTitle(title);
  markers[i].setPosition(newLatLng);

  markers[i].addListener('click', () => {
    infoWindow.setContent(`Maginitud: ${magnitud}`)
    infoWindow.open({
      ariaLabel: title,
      anchor: markers[i],
      map,
      shouldFocus: true
    })
  })

  return markers[i];
};

const addMarker = ({ id, title, lat, lng, magnitud }) => {
  const marker = new google.maps.Marker({
    position: { lat: Number.parseFloat(lat), lng: Number.parseFloat(lng) },
    map: map, // Global
    title,
    id,
  });

  markers.push(marker);

  marker.addListener('click', () => {
    infoWindow.setContent(`Maginitud: ${magnitud}`)
    infoWindow.open({
      ariaLabel: title,
      anchor: marker,
      map,
      shouldFocus: true
    })
  })

  return marker;
};
