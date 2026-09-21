<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Prensa - Liga de Fútbol del Partido de la Costa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-liga { background-color: #003366; }
        .badge-habilitado { background-color: #198754; color: white; }
        .badge-suspendido { background-color: #dc3545; color: white; }
        .escudo-filtro {
            width: 45px;
            height: 45px;
            object-fit: contain;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
            opacity: 0.6;
            background: white;
            border-radius: 50%;
            padding: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .escudo-filtro:hover, .escudo-filtro.active {
            transform: scale(1.15);
            opacity: 1;
            border: 2px solid #004b87;
        }
        .escudos-container {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 8px;
        }

        /* CONTENEDOR DE CREDENCIALES */
        .credencial-wrapper {
            display: flex;
            gap: 25px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .credencial-card {
            width: 320px;
            height: 500px;
            position: relative;
            background-size: cover;
            background-position: center;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.25);
            font-family: 'Bebas Neue', sans-serif;
        }

        .credencial-frente {
            background-image: url('credencial/frente.png');
        }

        .credencial-dorso {
            background-image: url('credencial/dorso.png');
        }

        /* REGLA DE IMPRESIÓN OPTIMIZADA */
        @media print {
            body * {
                visibility: hidden;
            }
            #modalCredencial, #modalCredencial * {
                visibility: visible;
            }
            #modalCredencial {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background: white !important;
            }
            .modal-header, .modal-footer, .btn-close {
                display: none !important;
            }
            .credencial-wrapper {
                gap: 20px;
                justify-content: center;
            }
            .credencial-card {
                box-shadow: none !important;
                page-break-inside: avoid;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-liga px-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="escudos/LIGA BLANCO.png" alt="Logo" width="40" height="40" class="me-2" onerror="this.style.display='none'">
                <span>Prensa General - Liga de La Costa</span>
            </a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3 fs-6" id="userInfoLabel">Cargando...</span>
                <button class="btn btn-outline-light btn-sm" onclick="cerrarSesion()">
                    <i class="fas fa-sign-out-alt me-1"></i>Salir
                </button>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        
        <div id="bannerRestriccion" class="alert alert-info shadow-sm" role="alert">
            <i class="fas fa-info-circle me-2"></i><span id="textoRestriccion">Verificando...</span>
        </div>

        <!-- FORMULARIO ALTA (SOLO ADMIN) -->
        <div class="card shadow-sm mb-4" id="cardAdminAcciones" style="display: none;">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-id-card-alt me-2"></i>Emitir Credencial de Prensa</h5>
                <span class="badge bg-danger">Panel Administrativo</span>
            </div>
            <div class="card-body bg-light">
                <form id="formNuevaPrensa" class="row g-3" onsubmit="guardarPrensaRTDB(event)">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Nombre y Apellido</label>
                        <input type="text" class="form-control" id="nombrePrensa" required placeholder="Ej. Roberto Gómez">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">DNI</label>
                        <input type="number" class="form-control" id="dniPrensa" required placeholder="Ej. 32456789">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Rol / Función</label>
                        <select class="form-select" id="rolPrensa">
                            <option value="Fotógrafo Oficial">Fotógrafo Oficial</option>
                            <option value="Periodista / Redactor">Periodista / Redactor</option>
                            <option value="Camarógrafo TV">Camarógrafo TV</option>
                            <option value="Relator / Streamer">Relator / Streamer</option>
                            <option value="Medio Partidario">Medio Partidario</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Club o Medio</label>
                        <select class="form-select" id="clubPrensa">
                            <option value="Social Mar de Ajó">Social Mar de Ajó</option>
                            <option value="El Gran Porvenir">El Gran Porvenir</option>
                            <option value="Cosme Argerich">Cosme Argerich</option>
                            <option value="CADU">CADU</option>
                            <option value="Fomento San Bernardo">Fomento San Bernardo</option>
                            <option value="Social Las Toninas">Social Las Toninas</option>
                            <option value="Popular Lavalle">Popular Lavalle</option>
                            <option value="CAJU">CAJU</option>
                            <option value="Defensores de Villa Clelia">Defensores de Villa Clelia</option>
                            <option value="Mar del Tuyú">Mar del Tuyú</option>
                            <option value="Núcleo FC">Núcleo FC</option>
                            <option value="Las Quintas">Las Quintas</option>
                            <option value="All Boys">All Boys</option>
                            <option value="Social Santa Teresita">Social Santa Teresita</option>
                            <option value="Medio Independiente / Freelance">Medio Independiente / Freelance</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Foto Carnet</label>
                        <input type="file" class="form-control" id="fotoPrensa" accept="image/*">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success px-4" id="btnSubmitCredencial">
                            <i class="fas fa-save me-1"></i>Emitir Credencial
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECCIÓN FILTROS Y BUSCADOR -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center g-3">
                    <div class="col-lg-8">
                        <label class="form-label fw-bold small text-muted mb-1"><i class="fas fa-filter me-1"></i>Filtrar por Equipo / Escudo:</label>
                        <div class="escudos-container" id="contenedorFiltroEscudos">
                            <img src="escudos/LIGA BLANCO.png" title="Todos" class="escudo-filtro active" onclick="filtrarPorClub('TODOS', this)" style="background: #003366;">
                            <img src="escudos/social.png" title="Social Mar de Ajó" class="escudo-filtro" onclick="filtrarPorClub('Social Mar de Ajó', this)">
                            <img src="escudos/porvenir.png" title="El Gran Porvenir" class="escudo-filtro" onclick="filtrarPorClub('El Gran Porvenir', this)">
                            <img src="escudos/cosme.png" title="Cosme Argerich" class="escudo-filtro" onclick="filtrarPorClub('Cosme Argerich', this)">
                            <img src="escudos/cadu.png" title="CADU" class="escudo-filtro" onclick="filtrarPorClub('CADU', this)">
                            <img src="escudos/fomento.png" title="Fomento San Bernardo" class="escudo-filtro" onclick="filtrarPorClub('Fomento San Bernardo', this)">
                            <img src="escudos/lastoninas.png" title="Social Las Toninas" class="escudo-filtro" onclick="filtrarPorClub('Social Las Toninas', this)">
                            <img src="escudos/lavalle.png" title="Popular Lavalle" class="escudo-filtro" onclick="filtrarPorClub('Popular Lavalle', this)">
                            <img src="escudos/caju.png" title="CAJU" class="escudo-filtro" onclick="filtrarPorClub('CAJU', this)">
                            <img src="escudos/villa.png" title="Defensores de Villa Clelia" class="escudo-filtro" onclick="filtrarPorClub('Defensores de Villa Clelia', this)">
                            <img src="escudos/mardeltuyu.png" title="Mar del Tuyú" class="escudo-filtro" onclick="filtrarPorClub('Mar del Tuyú', this)">
                            <img src="escudos/nucleo.png" title="Núcleo FC" class="escudo-filtro" onclick="filtrarPorClub('Núcleo FC', this)">
                            <img src="escudos/lasquintas.png" title="Las Quintas" class="escudo-filtro" onclick="filtrarPorClub('Las Quintas', this)">
                            <img src="escudos/allboys.png" title="All Boys" class="escudo-filtro" onclick="filtrarPorClub('All Boys', this)">
                            <img src="escudos/santa.png" title="Social Santa Teresita" class="escudo-filtro" onclick="filtrarPorClub('Social Santa Teresita', this)">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label fw-bold small text-muted mb-1"><i class="fas fa-search me-1"></i>Buscador rápido:</label>
                        <input type="text" class="form-control" id="inputBuscador" placeholder="Buscar por nombre o DNI..." onkeyup="filtrarTablaTexto()">
                    </div>
                </div>
            </div>
        </div>

        <!-- LISTADO -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i>Personal de Prensa Acreditado</h5>
                <span id="badgeContador" class="badge bg-light text-dark">0 registros</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Escudo</th>
                                <th>Nombre y Apellido</th>
                                <th>DNI</th>
                                <th>Función</th>
                                <th>Entidad / Club</th>
                                <th>Estado</th>
                                <th>Acciones / QR</th>
                            </tr>
                        </thead>
                        <tbody id="tablaPrensa">
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Conectando a Realtime Database...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL VISTA PREVIA DOBLE FAZ -->
    <div class="modal fade" id="modalCredencial" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-dark text-white py-2">
                    <h5 class="modal-title fs-6"><i class="fas fa-id-card me-2"></i>Credencial Oficial Doble Faz (9cm x 14cm)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-center bg-light">
                    
                    <div class="credencial-wrapper">
                        <!-- FRENTE -->
                        <div class="credencial-card credencial-frente">
                            <!-- Número correlativo abajo a la izquierda -->
                            <div style="position: absolute; bottom: 8px; left: 45px; font-size: 48px; color: #000; letter-spacing: 1px;" id="lblNumeroFrente">001</div>
                            <!-- Escudo del club abajo -->
                            <div style="position: absolute; bottom: 0px; left: 135px; width: 55px; height: 95px; display: flex; align-items: center; justify-content: center;">
                                <img id="imgEscudoFrente" src="escudos/social.png" style="max-width: 48px; max-height: 48px; object-fit: contain;" onerror="this.style.display='none'">
                            </div>
                        </div>

                        <!-- DORSO -->
                        <div class="credencial-card credencial-dorso">
                            <!-- Foto carnet circular -->
                            <div style="position: absolute; top: 48px; left: 50%; transform: translateX(-50%); width: 105px; height: 105px; border-radius: 50%; overflow: hidden; border: 3px solid #fff; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                <img id="carnetFoto" src="https://via.placeholder.com/150" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/150'">
                            </div>

                            <!-- Código QR -->
                            <div style="position: absolute; top: 165px; left: 50%; transform: translateX(-50%); width: 85px; height: 85px; background: white; padding: 3px; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <div id="qrcodeContainer"></div>
                            </div>

                            <!-- Valor Nombre -->
                            <div style="position: absolute; top: 285px; left: 15px; right: 25px; text-align: left;">
                                <div style="font-size: 20px; color: #000; font-weight: bold; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="carnetNombre">FRANCISCO DEZILLIO</div>
                            </div>

                            <!-- Valor DNI -->
                            <div style="position: absolute; top: 340px; left: 15px; right: 25px; text-align: left;">
                                <div style="font-size: 22px; color: #000; font-weight: bold; line-height: 1.1;" id="carnetDni">43.656.413</div>
                            </div>

                            <!-- Número correlativo abajo a la izquierda -->
                            <div style="position: absolute; bottom: 8px; left: 45px; font-size: 48px; color: #000; letter-spacing: 1px;" id="lblNumeroDorso">001</div>
                            <!-- Escudo del club abajo -->
                            <div style="position: absolute; bottom: 0px; left: 135px; width: 55px; height: 95px; display: flex; align-items: center; justify-content: center;">
                                <img id="imgEscudoDorso" src="escudos/social.png" style="max-width: 48px; max-height: 48px; object-fit: contain;" onerror="this.style.display='none'">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer justify-content-between py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="window.print()"><i class="fas fa-print me-1"></i>Imprimir Credencial Doble Faz</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Firebase y Librerías -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>

    <script>
        const firebaseConfig = {
            databaseURL: "https://basemardeajo-default-rtdb.firebaseio.com"
        };

        let rtdb;
        try {
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
            rtdb = firebase.database();
        } catch (e) {
            console.error("Error inicializando Firebase RTDB:", e);
        }

        const rolActual = sessionStorage.getItem('liga_rol');
        const clubActual = sessionStorage.getItem('liga_club');

        if (!rolActual) { window.location.href = 'index.html'; }

        let listaPrensaGlobal = [];
        let filtroClubActivo = 'TODOS';

        document.addEventListener('DOMContentLoaded', () => {
            const labelUser = document.getElementById('userInfoLabel');
            const textoRestriccion = document.getElementById('textoRestriccion');
            const cardAdmin = document.getElementById('cardAdminAcciones');

            if (rolActual === 'admin') {
                labelUser.innerHTML = `<i class="fas fa-user-shield me-1"></i> <b>Admin Liga</b>`;
                textoRestriccion.innerHTML = `Perfil <b>Administrador</b>: Control total.`;
                cardAdmin.style.display = 'block';
            } else if (rolActual === 'arbitro') {
                labelUser.innerHTML = `<i class="fas fa-whistle me-1"></i> <b>Árbitro / Veedor</b>`;
                textoRestriccion.innerHTML = `Perfil <b>Árbitro</b>: Lectura general de acreditaciones de prensa.`;
            } else if (rolActual === 'club') {
                labelUser.innerHTML = `<i class="fas fa-shield-alt me-1"></i> <b>Club: ${clubActual}</b>`;
                textoRestriccion.innerHTML = `Perfil <b>Institución</b>: Visualización exclusiva del personal de prensa de ${clubActual}.`;
                filtroClubActivo = clubActual;
            }

            cargarDatosRTDB();
        });

        function cerrarSesion() {
            sessionStorage.clear();
            window.location.href = 'index.html';
        }

        function obtenerEscudoRuta(club) {
            const map = {
                'Social Mar de Ajó': 'escudos/social.png',
                'El Gran Porvenir': 'escudos/porvenir.png',
                'Cosme Argerich': 'escudos/cosme.png',
                'CADU': 'escudos/cadu.png',
                'Fomento San Bernardo': 'escudos/fomento.png',
                'Social Las Toninas': 'escudos/lastoninas.png',
                'Popular Lavalle': 'escudos/lavalle.png',
                'CAJU': 'escudos/caju.png',
                'Defensores de Villa Clelia': 'escudos/villa.png',
                'Mar del Tuyú': 'escudos/mardeltuyu.png',
                'Núcleo FC': 'escudos/nucleo.png',
                'Las Quintas': 'escudos/lasquintas.png',
                'All Boys': 'escudos/allboys.png',
                'Social Santa Teresita': 'escudos/santa.png'
            };
            return map[club] || 'escudos/LIGA A COLOR.png';
        }

        function convertirYComprimirImagen(fileInput, callback) {
            if (!fileInput.files || fileInput.files.length === 0) {
                callback('https://via.placeholder.com/150');
                return;
            }
            const file = fileInput.files[0];
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function (event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function () {
                    const canvas = document.createElement('canvas');
                    const MAX_SIZE = 200;
                    let width = img.width;
                    let height = img.height;

                    if (width > height) {
                        if (width > MAX_SIZE) {
                            height *= MAX_SIZE / width;
                            width = MAX_SIZE;
                        }
                    } else {
                        if (height > MAX_SIZE) {
                            width *= MAX_SIZE / height;
                            height = MAX_SIZE;
                        }
                    }

                    canvas.width = width;
                    canvas.height = height;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
                    callback(dataUrl);
                };
            };
        }

        function cargarDatosRTDB() {
            const tbody = document.getElementById('tablaPrensa');
            
            if (rtdb) {
                rtdb.ref('prensa_acreditaciones').on('value', (snapshot) => {
                    listaPrensaGlobal = [];
                    const data = snapshot.val();
                    if (data) {
                        Object.keys(data).forEach(key => {
                            listaPrensaGlobal.push({ id: key, ...data[key] });
                        });
                    }
                    renderizarTabla();
                }, (err) => {
                    console.error("Error al leer RTDB:", err);
                    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al conectar con Realtime Database.</td></tr>`;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">No se pudo inicializar Realtime Database.</td></tr>`;
            }
        }

        function guardarPrensaRTDB(e) {
            e.preventDefault();
            if (rolActual !== 'admin') {
                alert('Acción no permitida.');
                return;
            }

            const btnSubmit = document.getElementById('btnSubmitCredencial');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-1"></span>Procesando foto...`;

            const inputFoto = document.getElementById('fotoPrensa');

            convertirYComprimirImagen(inputFoto, function(fotoBase64) {
                const nuevoRegistro = {
                    nombre: document.getElementById('nombrePrensa').value,
                    dni: document.getElementById('dniPrensa').value,
                    rol: document.getElementById('rolPrensa').value,
                    club: document.getElementById('clubPrensa').value,
                    foto: fotoBase64,
                    estado: 'HABILITADO',
                    createdAt: new Date().toISOString()
                };

                rtdb.ref('prensa_acreditaciones').push(nuevoRegistro)
                  .then(() => {
                      alert('¡Credencial emitida y guardada con éxito!');
                      document.getElementById('formNuevaPrensa').reset();
                      btnSubmit.disabled = false;
                      btnSubmit.innerHTML = `<i class="fas fa-save me-1"></i>Emitir Credencial`;
                  })
                  .catch(err => {
                      btnSubmit.disabled = false;
                      btnSubmit.innerHTML = `<i class="fas fa-save me-1"></i>Emitir Credencial`;
                      alert('Error al guardar: ' + err.message);
                      console.error(err);
                  });
            });
        }

        function cambiarEstadoPrensa(id, nuevoEstado) {
            if (rolActual !== 'admin') { alert('Solo el admin puede sancionar.'); return; }
            if (confirm(`¿Confirma cambiar estado a ${nuevoEstado}?`)) {
                rtdb.ref('prensa_acreditaciones/' + id).update({ estado: nuevoEstado })
                  .catch(err => alert('Error al actualizar estado: ' + err.message));
            }
        }

        function eliminarPrensa(id, nombre) {
            if (rolActual !== 'admin') { 
                alert('Acción restringida solo para administradores.'); 
                return; 
            }

            const claveIngresada = prompt(`ADVERTENCIA: Está a punto de eliminar a "${nombre}" de forma permanente.\n\nIngrese la clave de administrador para confirmar:`);
            
            if (claveIngresada === null) return;

            if (claveIngresada === "admin123" || claveIngresada === "liga2026") {
                rtdb.ref('prensa_acreditaciones/' + id).remove()
                  .then(() => {
                      alert('Registro eliminado correctamente de la base de datos.');
                  })
                  .catch(err => {
                      alert('Error al eliminar: ' + err.message);
                  });
            } else {
                alert('Clave de administrador incorrecta. Operación cancelada.');
            }
        }

        function filtrarPorClub(club, elementoImg) {
            if (rolActual === 'club') return;
            
            document.querySelectorAll('.escudo-filtro').forEach(el => el.classList.remove('active'));
            if(elementoImg) elementoImg.classList.add('active');
            
            filtroClubActivo = club;
            renderizarTabla();
        }

        function filtrarTablaTexto() {
            renderizarTabla();
        }

        function renderizarTabla() {
            const tbody = document.getElementById('tablaPrensa');
            const textoBusqueda = document.getElementById('inputBuscador').value.toLowerCase();
            tbody.innerHTML = '';

            let filtrados = listaPrensaGlobal.filter(item => {
                if (rolActual === 'club' && item.club.toLowerCase() !== clubActual.toLowerCase()) {
                    return false;
                }
                if (rolActual !== 'club' && filtroClubActivo !== 'TODOS' && item.club !== filtroClubActivo) {
                    return false;
                }
                if (textoBusqueda && !item.nombre.toLowerCase().includes(textoBusqueda) && !item.dni.includes(textoBusqueda)) {
                    return false;
                }
                return true;
            });

            document.getElementById('badgeContador').textContent = `${filtrados.length} registros`;

            if (filtrados.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron acreditaciones cargadas.</td></tr>`;
                return;
            }

            filtrados.forEach((item) => {
                const badgeClase = item.estado === 'HABILITADO' ? 'badge-habilitado' : 'badge-suspendido';
                const escudoPath = obtenerEscudoRuta(item.club);

                let botones = `<button class="btn btn-outline-primary btn-sm" onclick='verCarnetQR(${JSON.stringify(item)})'><i class="fas fa-qrcode me-1"></i>Ver Carnet</button>`;
                
                if (rolActual === 'admin') {
                    const op = item.estado === 'HABILITADO' ? 'SUSPENDIDO' : 'HABILITADO';
                    const btnColor = item.estado === 'HABILITADO' ? 'btn-outline-danger' : 'btn-outline-success';
                    botones += ` <button class="btn ${btnColor} btn-sm ms-1" onclick="cambiarEstadoPrensa('${item.id}', '${op}')">${op === 'SUSPENDIDO' ? 'Sancionar' : 'Habilitar'}</button>`;
                    botones += ` <button class="btn btn-danger btn-sm ms-1" onclick="eliminarPrensa('${item.id}', '${item.nombre}')" title="Eliminar registro"><i class="fas fa-trash-alt"></i></button>`;
                }

                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><img src="${escudoPath}" width="35" height="35" class="object-fit-contain" onerror="this.src='escudos/LIGA A COLOR.png'"></td>
                    <td class="fw-bold">${item.nombre}</td>
                    <td>${item.dni}</td>
                    <td><span class="badge bg-info text-dark">${item.rol || 'Prensa'}</span></td>
                    <td>${item.club}</td>
                    <td><span class="badge ${badgeClase} px-2 py-1">${item.estado}</span></td>
                    <td>${botones}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function verCarnetQR(item) {
            // Filtrar registros del mismo club y calcular orden cronológico exacto
            const delMismoClub = listaPrensaGlobal.filter(p => p.club === item.club);
            const indexEnClub = delMismoClub.findIndex(p => p.id === item.id);
            const nroOrden = String(indexEnClub !== -1 ? indexEnClub + 1 : 1).padStart(3, '0');

            document.getElementById('carnetNombre').textContent = item.nombre;
            document.getElementById('carnetDni').textContent = item.dni;
            document.getElementById('lblNumeroFrente').textContent = nroOrden;
            document.getElementById('lblNumeroDorso').textContent = nroOrden;
            
            document.getElementById('carnetFoto').src = item.foto || 'https://via.placeholder.com/150';

            const escudoPath = obtenerEscudoRuta(item.club);
            document.getElementById('imgEscudoFrente').src = escudoPath;
            document.getElementById('imgEscudoDorso').src = escudoPath;

            const container = document.getElementById('qrcodeContainer');
            container.innerHTML = '';

            const urlValidacion = `http://localhost/prensaliga/verificar.php?dni=${item.dni}&nombre=${encodeURIComponent(item.nombre)}`;
            new QRCode(container, {
                text: urlValidacion,
                width: 75,
                height: 75
            });

            new bootstrap.Modal(document.getElementById('modalCredencial')).show();
        }
    </script>
</body>
</html>