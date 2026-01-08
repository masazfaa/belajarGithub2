<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="<?= base_url() ?>favicon.ico" type="image/x-icon">
  <title>WebGIS Monitoring Jalan Rusak - <?= $title ?>
  </title>
  <!-- Leaflet -->
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/1.3.0/leaflet.css" />
  <script src="<?= base_url() ?>leaflet/1.3.0/leaflet.js"></script>
  <!-- Search Engine -->
  <script src="<?= base_url() ?>leaflet/leafletsearchmaster/src/leaflet-search.js"></script>
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/leafletsearchmaster/src/leaflet-search.css" />
  <!-- locate me -->
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/locateme/dist/L.Control.Locate.min.css" />
  <script src="<?= base_url() ?>leaflet/locateme/dist/L.Control.Locate.min.js" charset="utf-8"></script>
  <!-- Label Gun -->
  <script src="<?= base_url() ?>leaflet/rbush.min.js"></script>
  <script src="<?= base_url() ?>leaflet/labelgun.min.js"></script>
  <script src="<?= base_url() ?>leaflet/labels.js"></script>
  <!-- Panel Layers -->
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/leaflet-panel-layers-master/src/leaflet-panel-layerss.css" />
  <script src="<?= base_url() ?>leaflet/leaflet-panel-layers-master/src/leaflet-panel-layers.js"></script>
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/leaflet-panel-layers-master/examples/icons.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Basemap Switcher -->
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/leaflet-bmswitcher-main/src/leaflet-bmswitcher.css" />
  <script src="<?= base_url() ?>leaflet/leaflet-bmswitcher-main/src/leaflet-bmswitcher.js"></script>
  <!-- Scale Bar -->
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/leaflet-betterscale-master/L.Control.BetterScale.css" />
  <script src="<?= base_url() ?>leaflet/leaflet-betterscale-master/L.Control.BetterScale.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <!-- Measure -->
  <script src="<?= base_url() ?>leaflet/leaflet-measure.js"></script>
  <link rel="stylesheet" href="<?= base_url() ?>leaflet/leaflet-measure.css" />
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/css/splide.min.css">
  <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.3/dist/js/splide.min.js"></script>

  
</head>

<body>

<div id="loading">
    <img id="loading-image" src="loading1.gif" alt="Loading..." />
  </div>

  <script>
$('form').submit(function(e) {
  e.preventDefault();
  $("#loading").fadeIn(500);
  $("#loading-image").fadeIn(500);
  setTimeout(function(){
    $("#loading").fadeOut(500);
    $("#loading-image").fadeOut(500);
  }, 10000); // Increased to 10 seconds
});

  </script>

<style>
#loading {
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  position: fixed;
  display: none;
  opacity: 1;
  background-color: #fff;
  z-index: 9999;
  text-align: center;
}

#loading-image {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 10000;
}
  </style>

<div id="map" style="height :100vh;position:absolute;"></div>

<!-- Logo Container -->
<div id="logo-container">
  <div id="inner-container">
    <span style="font-weight: bold; font-size: 1.3rem;">DEMO JALANAN RUSAK</span>
    <div id="arrow">▼</div>
  </div>
</div>

<div id="popup-logo-container" style="display: none; border-radius: 10px;">
  <ul>
    <li><a href="<?= base_url('home/data') ?>" style="color:black;">Login Admin</a></li>
  </ul>
</div>

<!-- Pencarian dan Filter -->
<div id="search-filter-container">
  <!-- Form Pencarian dan Tombol Minimize -->
  <div id="search-container">
    <input type="text" id="search-laporan" placeholder="Cari laporan..." class="form-control" />
    <button id="toggle-filter" class="toggle-button">▼</button>
  </div>

  <!-- Filter Range -->
  <div id="filter-container">
    <div class="filter-section">
      <label for="kerusakan-range">Tingkat Kerusakan (0-100%)</label>
      <input type="range" id="kerusakan-range" min="0" max="100" value="0" class="filter-range">
      <span id="kerusakan-value">0</span> %
    </div>
    <div class="filter-section">
      <label for="progress-range">Progress Perbaikan (0-100%)</label>
      <input type="range" id="progress-range" min="0" max="100" value="0" class="filter-range">
      <span id="progress-value">0</span> %
    </div>
  </div>
</div>


<!-- Location Lat Lng -->
<div id="coordinate-container" style="position: absolute; bottom: 10px; left: 10px; background-color: white; border-radius: 5px 5px 5px 5px; padding: 5px; z-index: 1000;">
  <a id="lat" style="padding: 0 10px; font-size: 0.8em;"></a>
  <a id="lng" style="padding: 0 10px; font-size: 0.8em;"></a>
</div>

<!-- Location Lat Lng -->


<script>
  // Add this to your JavaScript
$('form').submit(function(e) {
  $("#loading").show();
  setTimeout(function(){
    $("#loading").hide();
  }, 5000);
});

  //basemap
  var map = L.map('map', {
    zoom: 19,
    maxZoom: 22,
    center: L.latLng([-7.76537, 110.35884]),
  });
    // osmLayer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

  map.addControl(
    L.control.locate({
      locateOptions: {
        flyTo: true,
        minzoom: 15,
        initialZoomLevel: 17,
      }
    })
  );

  // L.control.rotate().addTo(map);
  
  
  
const dataLaporan = <?= json_encode($dataLaporan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

const layerTitik = L.layerGroup();
const layerPolygon = L.layerGroup();

// Fungsi untuk mendapatkan warna berdasarkan tingkat kerusakan
function getKerusakanColor(value) {
  const v = parseFloat(value);
  if (isNaN(v)) return 'gray'; // fallback jika nilainya tidak valid
  const r = Math.floor((v / 100) * 255);     // makin tinggi → makin merah
  const g = Math.floor(200 - (v / 100) * 200); // makin rendah → makin hijau
  return `rgb(${r}, ${g}, 0)`;
}

// Fungsi untuk menyaring data berdasarkan laporan dan filter
function filterData(searchQuery = '') {
  layerTitik.clearLayers();  // Hapus semua marker yang ada
  layerPolygon.clearLayers();  // Hapus poligon yang ada

  const kerusakanFilter = document.getElementById('kerusakan-range').value;
  const progressFilter = document.getElementById('progress-range').value;

  dataLaporan.forEach(row => {
    if (!row.data_koordinat) return;

    let koordinat;
    try {
      koordinat = JSON.parse(row.data_koordinat);
      if (!Array.isArray(koordinat) || koordinat.length === 0) return;
    } catch (e) {
      console.warn('Koordinat tidak valid:', row.data_koordinat);
      return;
    }

    const laporanMatch = row.laporan.toLowerCase().includes(searchQuery);
    const kerusakanMatch = parseFloat(row.tingkat_kerusakan) >= kerusakanFilter;
    const progressMatch = parseFloat(row.progress_perbaikan) >= progressFilter;

    if (laporanMatch && kerusakanMatch && progressMatch) {
      koordinat.forEach((point, index) => {
        const marker = L.marker([point.lat, point.lng]);

        // Pastikan bindPopup dipanggil untuk setiap marker
        marker.bindPopup(() => {
          const sliderId = `slider-${row.id}-${index}`;
          const fotoHtml = (Array.isArray(row.fotos) && row.fotos.length > 0)
            ? `
            <div id="${sliderId}" class="splide" style="width:100%;margin-bottom:8px;">
              <div class="splide__track">
                <ul class="splide__list">
                  ${row.fotos.map(f =>
                    `<li class="splide__slide">
                      <img src="<?= base_url(); ?>/${f.url_foto}" style="width:100%;border-radius:5px;cursor:zoom-in;" onclick="zoomImage(this.src)">
                    </li>`).join('')}
                </ul>
              </div>
            </div>` : '<div class="text-muted mb-2">Tidak ada foto</div>';

          setTimeout(() => {
            new Splide(`#${sliderId}`, {
              type: 'loop',
              heightRatio: 0.6,
              pagination: true,
              arrows: true,
            }).mount();
          }, 100);

          return `
            <div style="min-width:180px;max-width:300px;">
              ${fotoHtml}
              <div><strong>Laporan:</strong><br>${row.laporan}</div>
              <div class="mt-1"><strong>Tingkat Kerusakan:</strong> ${row.tingkat_kerusakan}%</div>
              <div><strong>Progress Perbaikan:</strong> ${row.progress_perbaikan}%</div>
              <div><strong>Koordinat:</strong><br><code>${point.lat.toFixed(5)}, ${point.lng.toFixed(5)}</code></div>
            </div>`;
        }, { autoPan: false });

        marker.addTo(layerTitik);
      });

      // Filter poligon berdasarkan tingkat kerusakan
      if (koordinat.length >= 3) {
        const color = getKerusakanColor(parseFloat(row.tingkat_kerusakan)); // penting: pastikan angka
        const polygon = L.polygon(koordinat, {
          color: color,
          fillColor: color,
          fillOpacity: 0.4
        });
        polygon.addTo(layerPolygon);
      }
    }
  });

  // Menambahkan layerTitik dan layerPolygon ke peta
  layerTitik.addTo(map);
  layerPolygon.addTo(map);
}

// Set filter dalam keadaan minimize secara default saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
  // Tampilkan semua data saat pertama kali halaman dimuat
  filterData();  // Panggil tanpa argumen untuk menampilkan semua data

  // Pastikan filter container dalam keadaan tersembunyi (minimized)
  var filterContainer = document.getElementById('filter-container');
  filterContainer.style.display = 'none';  // Mulai dengan filter tersembunyi
  filterContainer.style.opacity = '0';  // Opacity dimulai dari 0
  document.getElementById('toggle-filter').innerHTML = '▲';  // Tombol minimize menjadi panah atas
});


  L.control.measure({
    position: 'topleft',
    primaryLengthUnit: 'meters',
    secondaryLengthUnit: undefined,
    primaryAreaUnit: 'sqmeters',
    secondaryAreaUnit: undefined,
  }).addTo(map);

  function updateLatLng(e) {
  document.getElementById('lat').textContent = 'Lat: ' + e.latlng.lat.toFixed(5);
  document.getElementById('lng').textContent = 'Long: ' + e.latlng.lng.toFixed(5);
}

map.on('mousemove', updateLatLng);

L.control.scale({position: 'bottomright', metric: true}).addTo(map);

const bmList = [
  {
		layer :  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",{attribution:"&copy; <a href=\\\"https://www.openstreetmap.org/copyright\\\">OpenStreetMap</a> contributors", crossOrigin: true}),
		name : "Open Street Map",
		icon : "<?=base_url()?>leaflet/leaflet-bmswitcher-main/example/assets/osm.png"
    },
	{
		layer :  L.tileLayer("http://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}.png",{attribution:"&copy; <a href=\\\"https://www.openstreetmap.org/copyright\\\">OpenStreetMap</a> contributors", crossOrigin: true}),
		name : "ArcGIS Online",
		icon : "<?=base_url()?>leaflet/leaflet-bmswitcher-main/example/assets/arcgis-online.png"
    },
	{
		layer :  L.tileLayer("http://services.arcgisonline.com/arcgis/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}",{attribution:"&copy; <a href=\\\"https://www.openstreetmap.org/copyright\\\">OpenStreetMap</a> contributors", crossOrigin: true}),
		name : "ESRI Light Gray",
		icon : "<?=base_url()?>leaflet/leaflet-bmswitcher-main/example/assets/esri-light-gray.png"
    },
	{
		layer :  L.tileLayer("http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}", {maxZoom: 22,subdomains: ['mt0', 'mt1', 'mt2', 'mt3']},{attribution:"&copy; <a href=\\\"https://www.openstreetmap.org/copyright\\\">OpenStreetMap</a> contributors", crossOrigin: true}).addTo(map),
		name : "Google Satellite",
		icon : "<?=base_url()?>leaflet/leaflet-bmswitcher-main/example/assets/google.png"
    },
];
new L.bmSwitcher(bmList).addTo(map);

setTimeout(function () {
  map.invalidateSize();
}, 500);

var returncenter = L.Control.extend({
    options: {
        position: 'topleft'
    },

    onAdd: function (map) {
        var container = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');

        // Style the container for the button
        container.style.backgroundColor = 'white'; 
        container.style.width = '34px'; // Button width
        container.style.height = '35px'; // Button height
        container.style.marginBottom = '10px'; // Margin from bottom
        container.style.display = 'flex'; // Use flexbox for centering
        container.style.justifyContent = 'center'; // Horizontally center
        container.style.alignItems = 'center'; // Vertically center

        // Add SVG image inside the button
        container.innerHTML = '<img src="<?=base_url()?>pin-location-svgrepo-com.svg" width="25" height="25">';

        // Reset map view when button is clicked
        container.onclick = function() {
            map.setView([-7.76537, 110.35884], 19); // Set map view to a specific location
        };

        return container;
    },
});

map.addControl(new returncenter());


var baseLayers = [
    // {
    //   active: false,
    //   name: "Open Street Map",
    //   layer: osmLayer
    // },
    // {
    //   active:true,
    //   name: "Satellite",
    //   layer: L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    //     maxZoom: 20,
    //     subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    //   })
    // },
    // {
    //   active:true,
    //   name: "ESRI Light Gray",
    //   layer: L.tileLayer('http://services.arcgisonline.com/arcgis/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
    //   maxZoom: 22
    // })},
  ];
  // panel layer
  var overLayers = [
{
    group: "Data Jalan Rusak",
    layers: [
      {
        active: true,
        name: "Titik Laporan",
        // icon: '<i class="icon" style="background-color:blue;"></i>',
        layer: layerTitik
      },
      {
        active: true,
    name: `
<div>
  <span type="button" data-bs-toggle="collapse" data-bs-target="#kondisipagar" aria-expanded="true" aria-controls="collapseOne">
    Kondisi Kerusakan Jalan
  </span>
  <div id="kondisipagar" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
    <div class="accordion-body">
      <div style="background-color: #fff; padding: 10px; border-radius: 3px;">
        <!-- Legenda dengan gradient -->
        <div style="background: linear-gradient(to right, #28a745, #ffc107, #dc3545); height: 10px; width: 100%; border-radius: 5px;">
        </div>
        <div style="margin-top: 5px; display: flex; justify-content: space-between; align-items: center;">
          <div style="display: flex; flex-direction: column; align-items: center;">
            0
          </div>
          <div style="display: flex; flex-direction: column; align-items: center;">
            50
          </div>
          <div style="display: flex; flex-direction: column; align-items: center;">
            100
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

    `,
        layer: layerPolygon
      }
    ]
  },
  ];

  // var isMobile = window.innerWidth <= 480; // Adjust as needed

var panelbutton = L.Control.extend({
    options: {
        position: 'topright'
    },

    onAdd: function (map) {
        var container = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');

        // Style the container for the button
        container.style.backgroundColor = 'white'; 
        container.style.width = '40px'; // Button width
        container.style.height = '40px'; // Button height
        container.style.marginBottom = '10px'; // Margin from bottom
        container.style.display = 'flex'; // Use flexbox for centering
        container.style.justifyContent = 'center'; // Horizontally center
        container.style.alignItems = 'center'; // Vertically center

        // Add SVG image inside the button
        container.innerHTML = '<img src="<?=base_url()?>assets/stack.svg" width="25" height="25">';

        // Toggle panel on button click
        container.onclick = function(){
            var panel = $(panelLayers.getContainer());
            if(panel.css('display') === "none") {
                panel.animate({width: 'toggle'}); // Slide panel in
            } else {
                panel.animate({width: 'toggle'}); // Slide panel out
            }
        };

        return container;
    },
});

map.addControl(new panelbutton());


var panelLayers = new L.Control.PanelLayers(baseLayers, overLayers, {
    selectorGroup: true,
    collapsibleGroups: true,
    collapsed: false // Panel layer selalu dalam keadaan collapsed
});

map.addControl(panelLayers);

</script>



<!-- Di akhir body -->
<div id="zoomModal" class="modal" tabindex="-1" style="display:none;position:fixed;z-index:2000;background:rgba(0,0,0,0.8);top:0;left:0;right:0;bottom:0;justify-content:center;align-items:center;">
  <img id="zoomedImg" src="" style="max-width:90%;max-height:90%;border:8px solid white;border-radius:8px;">
</div>

<script>
  function zoomImage(src) {
    const modal = document.getElementById('zoomModal');
    document.getElementById('zoomedImg').src = src;
    modal.style.display = 'flex';
    modal.onclick = () => modal.style.display = 'none';
  }
  
  
    document.getElementById('arrow').addEventListener('click', function() {
  var popupLogoContainer = document.getElementById('popup-logo-container');
  if (popupLogoContainer.style.display == "none") {
    popupLogoContainer.style.display = "block";
    setTimeout(function(){ popupLogoContainer.style.opacity = "1"; }, 50);
  } else {
    popupLogoContainer.style.opacity = "0";
    setTimeout(function(){ popupLogoContainer.style.display = "none"; }, 500);
  }
});


// Adjust map height on page load and window resize
function setMapHeight() {
  const windowHeight = window.innerHeight;
  const mapElement = document.getElementById('map');
  
  // Set the map height to be the full window height
  mapElement.style.height = `${windowHeight}px`;
}

// Set the map height on page load
setMapHeight();

// Update the map height when the window is resized
window.addEventListener('resize', function() {
  setMapHeight();
});


// Pencarian berdasarkan laporan
document.getElementById('search-laporan').addEventListener('input', function () {
  const searchQuery = this.value.toLowerCase();
  filterData(searchQuery);
});

// Update nilai filter kerusakan dan progress
document.getElementById('kerusakan-range').addEventListener('input', function () {
  const value = this.value;
  document.getElementById('kerusakan-value').textContent = value;
  filterData();
});

document.getElementById('progress-range').addEventListener('input', function () {
  const value = this.value;
  document.getElementById('progress-value').textContent = value;
  filterData();
});

document.getElementById('toggle-filter').addEventListener('click', function() {
  var filterContainer = document.getElementById('filter-container');
  if (filterContainer.style.display === "none" || filterContainer.style.display === "") {
    filterContainer.style.display = "block";
    setTimeout(function() {
      filterContainer.style.opacity = "1";  // Menampilkan filter dengan transisi opacity
    }, 50);
    this.innerHTML = '▼';  // Mengganti tombol menjadi panah bawah saat filter ditampilkan
  } else {
    filterContainer.style.opacity = "0";  // Menyembunyikan filter dengan transisi opacity
    setTimeout(function() {
      filterContainer.style.display = "none";
    }, 500);  // Menunggu animasi selesai sebelum menyembunyikan
    this.innerHTML = '▲';  // Mengganti tombol menjadi panah atas saat filter disembunyikan
  }
});

// Set filter dalam keadaan minimize secara default saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
  var filterContainer = document.getElementById('filter-container');
  filterContainer.style.display = 'none';  // Mulai dengan filter tersembunyi
  filterContainer.style.opacity = '0';  // Opacity dimulai dari 0
  document.getElementById('toggle-filter').innerHTML = '▲';  // Tombol minimize menjadi panah atas
});


</script>

<style>
html, body, * {
  font-family: Arial, sans-serif !important;
}

html, body {
  height: 100%;
  margin: 0; /* Remove margin to avoid overflow */
}

    #map {
      width: 100%;
      height: 100%;
      position: absolute;
      top: 0;
      left: 0;
    }

    .leaflet-panel-layers.expanded{
    display:none;
  }
  .leaflet-panel-layers-base{
    display:none;
  }

  .leaflet-panel-layers-separator{
    display:none;
  }

  .leaflet-left{
    top:12vh;
  }

  #popup-logo-container ul li {
  margin-bottom: 10px;
  margin-top:15px;
  }

  #logo-container {
    margin-top:1vh;
    margin-left:1vh;
    background-color: white;
    border-radius: 10px;
    width: 35vh;
    height: 10vh;
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 0;
    left: 0;
    z-index: 1000;
  }

  #inner-container {
    background-color: white;
    border: 2px solid gray;
    border-radius: 10px;
    width: 32vh;
    height: 8vh;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  #inner-container img {
    max-width: 100%;
    max-height: 100%;
  }
  #arrow {
    cursor: pointer;
    margin-left: 10px;
  }

#popup-logo-container {
  position: absolute;
  top: 12vh;
  left: 6vh;
  background-color: white;
  border-radius: 8px;
  padding: 0px 18px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
  z-index: 1001;
  transition: opacity 0.3s ease;
  opacity: 0;
  display: none;
  width: auto;
}

#popup-logo-container ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

#popup-logo-container ul li a {
  color: #333;
  text-decoration: none;
  font-size: 0.95rem;
  font-weight: 500;
}

#popup-logo-container ul li a:hover {
  text-decoration: underline;
}

#popup-logo-container ul li {
  padding: 6px 10px;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

#popup-logo-container ul li:hover {
  background-color: #f0f0f0;
}


.popup-visible {
  display: block !important;
  opacity: 1 !important;
}

.popup-hidden {
  display: none !important;
  opacity: 0 !important;
}

  .leaflet-panel-layers-list{
    height:50vh;
  }

  #sidebar {
    position: absolute;
    z-index: 9000;
    /* This will make the sidebar appear above the map */
    width: 20%;
    /* Adjust as needed */
  }

  .leaflet-panel-layers.expanded {
  border-bottom-left-radius: 10px;
  border-top-left-radius: 10px;
  top:5vh;
  }

  .leaflet-control-attribution {
    display: none;
  }

  .title-page {
    display:none;
  }

  .text-subtitle {
    display:none;
  }
  #main .main-content {
    padding:0;
  }

  .select-css {
    background-image:
    url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="6"><path fill="black" d="M0 0h12L6 6z"/></svg>');
    background-repeat: no-repeat;
    background-position: right .7em top 50%, 0 0;
    background-size: .65em auto, 100%;
}

.select-css option {
    transition: background-color 0.5s ease;
}

.select-css option:hover {
    background-color: #f2f2f2;
}

/* Logo Container */
#logo-container {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: white;
  border-radius: 10px;
  padding: 10px;
  z-index: 1000;
}

/* Pencarian dan Filter */
#search-filter-container {
  position: absolute;
  top: 19px;
  left: 37vh; /* Letakkan di samping kanan logo */
  z-index: 1000;
  display: flex;
  flex-direction: column;
  width: 320px; /* Atur lebar form */
  margin-left: 20px; /* Jarak kiri */
  margin-right: 20px; /* Jarak kanan */
}

#search-container {
  display: flex; /* Gunakan flexbox agar input dan tombol bersebelahan */
  width: 100%;
  margin-bottom: 10px;
}

#search-container input {
  flex-grow: 1; /* Input akan mengambil ruang yang tersisa */
  padding: 10px;
  font-size: 1rem;
  border-radius: 5px;
  border: 1px solid #ccc;
}

#toggle-filter {
  background-color: white;
  border: none;
  font-size: 20px;
  padding: 0 10px;
  cursor: pointer;
  margin-left: 10px; /* Memberikan jarak antara input dan tombol 
  display: flex;
  align-items: center;
  justify-content: center;
  height: 40px; /* Ukuran tombol */
  width: 40px;  /* Ukuran tombol */
  border-radius: 50%; /* Membuat tombol menjadi bulat */
}

#toggle-filter:hover {
  background-color: #f0f0f0;
}

#filter-container {
  display: none; /* Mulai dengan filter tersembunyi */
  background-color: white;
  padding: 10px;
  border-radius: 5px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  opacity: 0; /* Mulai dengan opacity 0 */
  transition: opacity 0.5s ease; /* Animasi transisi */
}

.filter-section {
  margin-bottom: 15px;
}

.filter-range {
  width: 100%;
}

#kerusakan-value, #progress-value {
  font-weight: bold;
  margin-left: 10px;
}

/* Media Query untuk perangkat dengan ukuran layar lebih kecil seperti iPad atau ponsel Android */
@media (max-width: 768px) {
  #search-filter-container {
    position: relative;
    top: 1vh; /* Menghapus posisi absolute */
    left: 4vh;
    width: 31vh; /* Form mengisi lebar layar */
    margin-top: 10px; /* Memberikan jarak dengan logo */
  }

  #logo-container {
    position: relative; /* Ubah posisi logo ke relative agar elemen berikutnya bisa berada di bawah */
  }

  #search-container {
    flex-direction: row; /* Menempatkan input dan tombol di bawah satu sama lain */
  }
  .leaflet-panel-layers.expanded {
  /*border-bottom-left-radius: 10px;*/
  /*border-top-left-radius: 10px;*/
  top: 12vh;
  }
}

</style>

</div>
</div>

<div class="inner dark">
	<div class="container">
		<div class="row text-center">

		</div>
	</div>
</div>
</div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</script>

</body>

</html>