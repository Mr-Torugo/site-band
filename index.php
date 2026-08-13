<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Bando Map</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #f8f9fa;
        }

        #map {
            height: 100vh;
            width: 100vw;
            z-index: 1;
        }

        /* =========================================
           NOVAS CLASSES RESPONSIVAS (MOBILE)
           ========================================= */
        .mobile-top-bar {
            position: fixed;
            top: 0;
            width: 100%;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1050;
            padding: 10px 15px;
            align-items: center;
            justify-content: space-between;
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: white;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.08);
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1050;
            padding-bottom: env(safe-area-inset-bottom, 10px);
        }

        .nav-item-mobile {
            text-align: center;
            color: #adb5bd;
            /* Cinza por padrão para itens inativos */
            text-decoration: none;
            font-size: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 20%;
            transition: color 0.2s;
        }

        /* Apenas a página ativa fica colorida */
        .nav-item-mobile.active {
            color: #0d6efd;
            font-weight: bold;
        }

        .nav-item-mobile.active i {
            color: #0d6efd !important;
        }

        /* Ajustes dos botões e Leaflet no Mobile */
        .btn-flutuante {
            position: fixed;
            bottom: 90px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            font-size: 24px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .leaflet-top {
            top: 60px !important;
        }

        @media (min-width: 768px) {
            .btn-flutuante {
                bottom: 30px;
            }

            .leaflet-top {
                top: 0px !important;
            }
        }

        /* =========================================
           RESTANTE DO CSS INTACTO
           ========================================= */
        .modal-content {
            border-radius: 15px;
        }

        #preview-container {
            display: none;
            margin-top: 15px;
            text-align: center;
        }

        #preview-foto {
            max-width: 100%;
            max-height: 250px;
            border-radius: 8px;
            object-fit: cover;
        }

        .box-destaque {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }

        .registro-opcao {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: 0.2s;
            background: white;
        }

        .registro-opcao:hover {
            border-color: #0d6efd;
        }

        @keyframes pulse-gold {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.8);
            }

            70% {
                box-shadow: 0 0 0 15px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }

        .badge-tesouro {
            background-color: #dc3545;
            color: white;
            animation: pulse-gold 2s infinite;
            border: 2px solid #ffc107;
            font-weight: bold;
        }

        .pin-tesouro-container {
            position: relative;
            width: 55px;
            height: 65px;
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }

        .pin-tesouro-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 45px;
            height: 45px;
            transform: translate(-50%, -50%);
            background-color: rgba(255, 193, 7, 0.6);
            border-radius: 50%;
            animation: pin-pulse 1.5s infinite;
            z-index: -1;
        }

        .pin-tesouro-img {
            width: 55px !important;
            height: 65px !important;
            object-fit: contain;
            animation: pin-bounce 0.8s infinite alternate;
            filter: drop-shadow(0px 6px 5px rgba(0, 0, 0, 0.6));
        }

        @keyframes pin-pulse {
            0% {
                transform: translate(-50%, -50%) scale(0.6);
                opacity: 1;
            }

            100% {
                transform: translate(-50%, -50%) scale(1.8);
                opacity: 0;
            }
        }

        @keyframes pin-bounce {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-15px);
            }
        }

        .custom-div-icon {
            background: transparent;
            border: none;
        }
    </style>
</head>

<body>

    <?php 
    // 1. Avisa para o menu que estamos na tela do Mapa
    $menuAtivo = 'mapa'; 
    
    // 2. Importa todo o código do menu
    require './includes/navbar.php'; 
    ?>

    <div id="map"></div>

    <button class="btn btn-primary btn-flutuante" data-bs-toggle="modal" data-bs-target="#modalUpload"><i
            class="bi bi-plus-lg"></i></button>
    <button class="btn btn-dark btn-flutuante" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasFiltros" style="right: auto; left: 30px;"><i class="bi bi-funnel-fill"></i></button>

    <!-- Restante do código dos Modais (Filtros, Missão, Upload, Ampliar, Descoberta, Mural, Editar)... -->
    <!-- (Mantido exatamente como o seu original abaixo) -->

    <!-- Menu Lateral de Filtros -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasFiltros" style="width: 320px; z-index: 1060;">
        <div class="offcanvas-header bg-dark text-white">
            <h5 class="offcanvas-title"><i class="bi bi-funnel-fill"></i> Filtros do Mapa</h5><button type="button"
                class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="mb-4"><label class="form-label fw-bold">Status da Descoberta</label><select
                    class="form-select border-dark" id="filtroStatus" onchange="aplicarFiltros()">
                    <option value="todos">Mostrar Todos</option>
                    <option value="nao_encontrado">🔴 Apenas os que NÃO encontrei</option>
                    <option value="conquistado">👑 Apenas meus Conquistados</option>
                </select></div>
            <div class="mb-4"><label class="form-label fw-bold">Categoria</label><select class="form-select border-info"
                    id="filtroCategoria" onchange="aplicarFiltros()">
                    <option value="todas">Todas as Categorias</option>
                    <option value="Natureza">Natureza</option>
                    <option value="Urbano">Urbano</option>
                    <option value="Praia">Praia</option>
                    <option value="Turísticos">Turísticos</option>
                    <option value="Estrada">Estrada</option>
                    <option value="Móveis">Móveis</option>
                    <option value="Estados">Estados</option>
                    <option value="Internacionais">Internacionais</option>
                </select></div>
            <div class="mb-4"><label class="form-label fw-bold">Raridade</label><select
                    class="form-select border-warning" id="filtroRaridade" onchange="aplicarFiltros()">
                    <option value="todas">Todas as Raridades</option>
                    <option value="Comum">Comum</option>
                    <option value="Raro">Raro</option>
                    <option value="Lendário">Lendário</option>
                    <option value="Tesouro">🌟 Apenas Eventos (Tesouro)</option>
                </select></div>
            <button class="btn btn-outline-danger w-100 mt-2"
                onclick="document.getElementById('filtroStatus').value='todos'; document.getElementById('filtroCategoria').value='todas'; document.getElementById('filtroRaridade').value='todas'; aplicarFiltros();"
                data-bs-dismiss="offcanvas">Limpar Filtros</button>
        </div>
    </div>


    <!-- MODAL 1: Upload (COM CAIXA SECRETA DO ADMIN) -->
    <div class="modal fade" id="modalUpload" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Novo Adesivo</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="formAdesivo">
                        <div class="mb-3 box-destaque"><label class="form-label fw-bold">Imagem do Adesivo</label>
                            <div class="d-flex gap-2"><button type="button" class="btn btn-outline-primary flex-fill"
                                    onclick="document.getElementById('fotoCamera').click()"><i class="bi bi-camera"></i>
                                    Câmera</button><button type="button" class="btn btn-outline-secondary flex-fill"
                                    onclick="document.getElementById('fotoGaleria').click()"><i class="bi bi-image"></i>
                                    Arquivo</button></div><input type="file" accept="image/*" capture="environment"
                                id="fotoCamera" style="display: none;"><input type="file" accept="image/*"
                                id="fotoGaleria" style="display: none;">
                        </div>
                        <div id="preview-container"><img id="preview-foto" src="" alt="Preview"></div>
                        <div class="mb-3 mt-3"><label class="form-label">Nome do Local (Ex: Poste da Rua
                                X)</label><input type="text" class="form-control" id="nomeLocal" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Categoria</label><select class="form-select"
                                id="categoriaAdesivo">
                                <option value="Natureza">Natureza</option>
                                <option value="Urbano" selected>Urbano</option>
                                <option value="Praia">Praia</option>
                                <option value="Turísticos">Turísticos</option>
                                <option value="Estrada">Estrada</option>
                                <option value="Móveis">Móveis</option>
                                <option value="Estados">Estados</option>
                                <option value="Internacionais">Internacionais</option>
                            </select></div>

                        <div id="boxEventoAdmin" class="mb-3 d-none p-2 border border-warning rounded"
                            style="background-color: #fff3cd;">
                            <div class="form-check"><input class="form-check-input" type="checkbox"
                                    id="checkEvento"><label class="form-check-label fw-bold text-warning-emphasis"
                                    for="checkEvento">🌟 Marcar como Evento (Tesouro - 500 XP)</label></div><small
                                class="text-muted" style="font-size: 0.75rem;">Isso ignora o GPS e cria um adesivo
                                dourado pulsante no mapa!</small>
                        </div>

                        <div class="mb-3 mt-3 box-destaque"><label class="form-label fw-bold">Onde o adesivo foi
                                colado?</label>
                            <div class="form-check mb-2"><input class="form-check-input" type="radio" name="modoLocal"
                                    id="modoGps" value="gps" checked><label class="form-check-label" for="modoGps">Usar
                                    meu GPS agora</label></div>
                            <div class="form-check mb-2"><input class="form-check-input" type="radio" name="modoLocal"
                                    id="modoMapa" value="mapa"><label class="form-check-label" for="modoMapa">Usar local
                                    selecionado no mapa</label></div>
                            <div class="form-check"><input class="form-check-input" type="radio" name="modoLocal"
                                    id="modoManual" value="manual"><label class="form-check-label"
                                    for="modoManual">Procurar por endereço/cidade</label></div>
                            <div id="boxEndereco" class="mt-3" style="display: none;">
                                <div class="input-group"><input type="text" class="form-control" id="inputEndereco"
                                        placeholder="Ex: Torre Eiffel"><button class="btn btn-outline-secondary"
                                        type="button" id="btnBuscarEnd">Buscar</button></div><small id="statusBusca"
                                    class="form-text text-primary mt-1"></small>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-primary"
                        id="btnSalvar">Salvar no Mapa</button></div>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Ampliar -->
    <div class="modal fade" id="modalImagemMaior" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0"><button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal" aria-label="Fechar"></button></div>
                <div class="modal-body text-center pt-0"><img id="imagemMaiorSrc" src="" alt="Adesivo Ampliado"
                        style="max-width: 100%; max-height: 80vh; border-radius: 8px;"></div>
            </div>
        </div>
    </div>

    <!-- MODAL 3: Descoberta -->
    <div class="modal fade" id="modalDescoberta" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">🎉 Registrar Descoberta</h5><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center mb-3">Você está no <b id="descNomeLocal"></b>!</p>
                    <form id="formDescoberta"><input type="hidden" id="descAdesivoId">
                        <div class="mb-3"><label class="form-label fw-bold">Como você quer registrar?</label><label
                                class="d-block registro-opcao" id="optAvistado"><input class="form-check-input me-2"
                                    type="radio" name="tipoRegistro" value="avistado" onchange="toggleFotoReq()"
                                    checked>👁️ <b>Avistado:</b> Só vi o adesivo (0 XP)</label><label
                                class="d-block registro-opcao" id="optEncontrado"><input class="form-check-input me-2"
                                    type="radio" name="tipoRegistro" value="encontrado" onchange="toggleFotoReq()">📸
                                <b>Encontrado:</b> Foto do adesivo (50% XP)</label><label class="d-block registro-opcao"
                                id="optConquistado"><input class="form-check-input me-2" type="radio"
                                    name="tipoRegistro" value="conquistado" onchange="toggleFotoReq()">🤳
                                <b>Conquistado:</b> Selfie (100% XP + Álbum)</label></div>
                        <div class="mb-3 box-destaque" id="boxFotoDesc" style="display: none;"><label
                                class="form-label fw-bold">Anexe a Foto Obrigatória</label><input class="form-control"
                                type="file" accept="image/*" capture="environment" id="descSelfie"></div>
                        <div class="mb-3"><label class="form-label fw-bold">Deixe um comentário</label><textarea
                                class="form-control" id="descComentario" rows="2"
                                placeholder="O que achou do local?"></textarea></div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancelar</button><button type="button" class="btn btn-success fw-bold"
                        id="btnSalvarDescoberta">Registrar Ação</button></div>
            </div>
        </div>
    </div>

    <!-- MODAL 4: Mural -->
    <div class="modal fade" id="modalMural" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content bg-light">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-chat-quote"></i> Mural: <span id="muralNomeLocal"></span>
                    </h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body" style="background-color: #e9ecef;">
                    <div id="conteudoMural" class="d-flex flex-column gap-3">
                        <div class="text-center text-muted">Carregando...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 5: Editar -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">✏️ Editar Adesivo</h5><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditar"><input type="hidden" id="editAdesivoId">
                        <div class="mb-3"><label class="form-label fw-bold">Nome do Local</label><input type="text"
                                class="form-control" id="editNomeLocal" required></div>
                        <div class="row">
                            <div class="col-6 mb-3"><label class="form-label fw-bold">Categoria</label><select
                                    class="form-select" id="editCategoria">
                                    <option value="Natureza">Natureza</option>
                                    <option value="Urbano">Urbano</option>
                                    <option value="Praia">Praia</option>
                                    <option value="Turísticos">Turísticos</option>
                                    <option value="Estrada">Estrada</option>
                                    <option value="Móveis">Móveis</option>
                                    <option value="Estados">Estados</option>
                                    <option value="Internacionais">Internacionais</option>
                                </select></div>
                            <div class="col-6 mb-3"><label class="form-label fw-bold">Raridade</label><select
                                    class="form-select" id="editRaridade">
                                    <option value="Comum">Comum</option>
                                    <option value="Raro">Raro</option>
                                    <option value="Lendário">Lendário</option>
                                    <option value="Tesouro">Tesouro (Evento)</option>
                                </select></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancelar</button><button type="button"
                        class="btn btn-info fw-bold text-white" id="btnSalvarEdicao">Salvar Alterações</button></div>
            </div>
        </div>
    </div>

    <!-- JS Scripts e Lógica -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <script>
        const meuId = localStorage.getItem('bando_id');
        const meuApelido = localStorage.getItem('bando_apelido');
        if (!meuId) {
            window.location.href = 'login.php';
        } else {
            document.getElementById('nomeLogado').innerText = meuApelido;
            if (document.getElementById('nomeLogadoMobile')) document.getElementById('nomeLogadoMobile').innerText = meuApelido;
        }
        function sairDoApp() { localStorage.clear(); window.location.href = 'login.php'; }

        const iconeBando = L.icon({ iconUrl: 'pin-bando.png', iconSize: [45, 55], iconAnchor: [22, 55], popupAnchor: [0, -55] });
        const iconeTesouro = L.divIcon({
            className: 'custom-div-icon',
            html: `<div class="pin-tesouro-container"><div class="pin-tesouro-glow"></div><img src="pin-tesouro.png" class="pin-tesouro-img" alt="Tesouro!"></div>`,
            iconSize: [55, 65],
            iconAnchor: [27, 65],
            popupAnchor: [0, -60]
        });

        function toggleFotoReq() { const tipo = document.querySelector('input[name="tipoRegistro"]:checked').value; const boxFoto = document.getElementById('boxFotoDesc'); if (tipo === 'avistado') { boxFoto.style.display = 'none'; } else { boxFoto.style.display = 'block'; } }

        const map = L.map('map').setView([-23.5505, -46.6333], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        let arquivoSelecionado = null;
        function processarFoto(event) { const file = event.target.files[0]; if (file) { arquivoSelecionado = file; const reader = new FileReader(); reader.onload = e => { document.getElementById('preview-foto').src = e.target.result; document.getElementById('preview-container').style.display = 'block'; }; reader.readAsDataURL(file); } }
        document.getElementById('fotoCamera').addEventListener('change', processarFoto); document.getElementById('fotoGaleria').addEventListener('change', processarFoto);

        const modoGps = document.getElementById('modoGps'); const modoMapa = document.getElementById('modoMapa'); const modoManual = document.getElementById('modoManual'); const boxEndereco = document.getElementById('boxEndereco');
        modoGps.addEventListener('change', () => boxEndereco.style.display = 'none'); modoMapa.addEventListener('change', () => boxEndereco.style.display = 'none'); modoManual.addEventListener('change', () => boxEndereco.style.display = 'block');

        let latMapa = null; let lngMapa = null; let marcadorTemp = null;
        map.on('click', function (e) { latMapa = e.latlng.lat; lngMapa = e.latlng.lng; if (marcadorTemp) map.removeLayer(marcadorTemp); marcadorTemp = L.marker([latMapa, lngMapa], { icon: iconeBando }).addTo(map); marcadorTemp.bindPopup(`<div style="text-align:center;"><strong>Colar aqui?</strong><br><button class="btn btn-primary btn-sm mt-2" onclick="abrirModalPeloMapa()">Sim</button></div>`).openPopup(); });
        function abrirModalPeloMapa() { document.getElementById('modoMapa').checked = true; boxEndereco.style.display = 'none'; new bootstrap.Modal(document.getElementById('modalUpload')).show(); if (marcadorTemp) marcadorTemp.closePopup(); }

        let latManual = null; let lngManual = null;
        document.getElementById('btnBuscarEnd').addEventListener('click', function () { const endereco = document.getElementById('inputEndereco').value; const status = document.getElementById('statusBusca'); if (!endereco) return; fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(endereco)}`).then(res => res.json()).then(data => { if (data.length > 0) { latManual = data[0].lat; lngManual = data[0].lon; status.innerHTML = `✅ Encontrado: ${data[0].display_name}`; status.style.color = "green"; } else { status.innerText = "❌ Local não encontrado."; } }); });

        document.getElementById('btnSalvar').addEventListener('click', function () {
            const nomeLocal = document.getElementById('nomeLocal').value;
            const categoria = document.getElementById('categoriaAdesivo').value;
            if (!nomeLocal || !arquivoSelecionado) { alert("Preencha o nome e foto."); return; }

            const modoSelecionado = document.querySelector('input[name="modoLocal"]:checked').value;
            const btn = this; const textoOriginal = btn.innerHTML; btn.innerHTML = 'Salvando...'; btn.disabled = true;

            if (modoSelecionado === 'manual' && latManual) { enviarParaServidor(latManual, lngManual, arquivoSelecionado, nomeLocal, categoria, btn, textoOriginal); }
            else if (modoSelecionado === 'mapa' && latMapa) { enviarParaServidor(latMapa, lngMapa, arquivoSelecionado, nomeLocal, categoria, btn, textoOriginal); }
            else { navigator.geolocation.getCurrentPosition(pos => { enviarParaServidor(pos.coords.latitude, pos.coords.longitude, arquivoSelecionado, nomeLocal, categoria, btn, textoOriginal); }, () => { alert('Erro no GPS.'); btn.innerHTML = textoOriginal; btn.disabled = false; }, { enableHighAccuracy: true }); }
        });

        function enviarParaServidor(lat, lng, arquivoFoto, nomeLocal, categoria, btn, textoOriginal) {
            const formData = new FormData();
            formData.append('foto', arquivoFoto); formData.append('nomeLocal', nomeLocal);
            formData.append('lat', lat); formData.append('lng', lng);
            formData.append('criador_id', meuId); formData.append('categoria', categoria);

            const checkEvento = document.getElementById('checkEvento');
            formData.append('is_evento', (checkEvento && checkEvento.checked) ? 'true' : 'false');

            fetch('api/salvar.php', { method: 'POST', body: formData }).then(r => r.json()).then(data => { if (data.sucesso) location.reload(); else alert('Erro: ' + data.erro); }).finally(() => { btn.innerHTML = textoOriginal; btn.disabled = false; });
        }

        let todosAdesivos = []; let usuarioAdmin = false; const marcadoresCluster = L.markerClusterGroup(); map.addLayer(marcadoresCluster);
        function aplicarFiltros() { renderizarMapa(); }

        function carregarAdesivos() {
            fetch('api/listar.php?usuario_id=' + meuId).then(r => r.json()).then(data => {
                if (data.sucesso) {
                    if (data.is_admin) {
                        document.getElementById('badgeAdmin').classList.remove('d-none');
                        document.getElementById('btnMenuAdmin').classList.remove('d-none');
                        document.getElementById('boxEventoAdmin').classList.remove('d-none');

                        if (document.getElementById('badgeAdminMobile')) document.getElementById('badgeAdminMobile').classList.remove('d-none');
                        if (document.getElementById('btnMenuAdminMobile')) document.getElementById('btnMenuAdminMobile').classList.remove('d-none');

                        usuarioAdmin = true;
                    }
                    todosAdesivos = data.dados; renderizarMapa();
                }
            });
        }

        function renderizarMapa() {
            marcadoresCluster.clearLayers();
            const fStatus = document.getElementById('filtroStatus').value; 
            const fCategoria = document.getElementById('filtroCategoria').value; 
            const fRaridade = document.getElementById('filtroRaridade').value;

            // Memória para guardar os pinos pelo ID
            let marcadoresSalvos = {}; 

            todosAdesivos.forEach(adesivo => {
                let historicoHtml = ''; let botaoMuralHtml = ''; let meuNivel = 0;
                
                if (adesivo.descobertas && adesivo.descobertas.length > 0) {
                    historicoHtml = '<div style="margin-top: 12px; padding-top: 10px; border-top: 1px solid #ddd; text-align: left;"><small style="color: #666; display: block; margin-bottom: 5px;"><b>Já registraram:</b></small><div style="max-height: 60px; overflow-y: auto; font-size: 0.8rem;">';
                    adesivo.descobertas.forEach(d => { 
                        let icone = d.tipo_registro === 'conquistado' ? '👑' : (d.tipo_registro === 'encontrado' ? '📸' : '👁️'); 
                        historicoHtml += `<div style="margin-bottom: 3px;">${icone} <b>${d.apelido}</b></div>`; 
                        if (d.apelido === meuApelido) { 
                            if (d.tipo_registro === 'conquistado') meuNivel = 3; 
                            else if (d.tipo_registro === 'encontrado') meuNivel = 2; 
                            else meuNivel = 1; 
                        } 
                    });
                    historicoHtml += '</div></div>'; 
                    botaoMuralHtml = `<button onclick="abrirMural(${adesivo.id}, '${adesivo.nome_local}')" class="btn btn-outline-primary btn-sm mt-2 fw-bold w-100"><i class="bi bi-chat-quote"></i> Ver Mural</button>`;
                } else { 
                    historicoHtml = '<div style="margin-top: 12px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 0.8rem; color: #888;">Seja o primeiro a encontrar! 🏆</div>'; 
                }

                // 👇 A CORREÇÃO DE LOGICA DO FILTRO ENTRA AQUI 👇
                // Se o filtro for "Não encontrei", pula se eu já achei (meuNivel > 0) OU se o adesivo for meu (criador_id == meuId)
                if (fStatus === 'nao_encontrado' && (meuNivel > 0 || adesivo.criador_id == meuId)) return; 
                // 👆 ----------------------------------------- 👆
                
                if (fStatus === 'conquistado' && meuNivel !== 3) return; 
                if (fCategoria !== 'todas' && adesivo.categoria !== fCategoria) return; 
                if (fRaridade !== 'todas' && adesivo.raridade !== fRaridade) return;

                let iconeAtual = (adesivo.raridade === 'Tesouro') ? iconeTesouro : iconeBando;
                const marker = L.marker([adesivo.lat, adesivo.lng], { icon: iconeAtual });

                let botaoAcaoHtml = '';
                if (adesivo.criador_id != meuId) {
                    if (meuNivel === 3) botaoAcaoHtml = `<button class="btn btn-secondary btn-sm mt-2 fw-bold w-100" disabled>👑 Conquistado (Álbum + 100% XP)</button>`; 
                    else if (meuNivel === 2) botaoAcaoHtml = `<button onclick="abrirModalDescoberta(${adesivo.id}, '${adesivo.nome_local}', 2)" class="btn btn-warning text-dark btn-sm mt-2 fw-bold w-100">⬆️ Evoluir para Conquistado</button>`; 
                    else if (meuNivel === 1) botaoAcaoHtml = `<button onclick="abrirModalDescoberta(${adesivo.id}, '${adesivo.nome_local}', 1)" class="btn btn-info text-white btn-sm mt-2 fw-bold w-100">⬆️ Evoluir Registro</button>`; 
                    else botaoAcaoHtml = `<button onclick="abrirModalDescoberta(${adesivo.id}, '${adesivo.nome_local}', 0)" class="btn btn-success btn-sm mt-2 fw-bold w-100">🎉 Fazer Registro!</button>`;
                }

                let boxAdminHtml = '';
                if (adesivo.criador_id == meuId || usuarioAdmin) { 
                    boxAdminHtml = `<div class="d-flex gap-2 mt-2"><button onclick="abrirModalEditar(${adesivo.id}, '${adesivo.nome_local.replace(/'/g, "\\'")}', '${adesivo.categoria}', '${adesivo.raridade}')" class="btn btn-outline-info btn-sm flex-fill"><i class="bi bi-pencil"></i></button><button onclick="deletarAdesivo(${adesivo.id})" class="btn btn-outline-danger btn-sm flex-fill"><i class="bi bi-trash"></i></button></div>`; 
                }

                let corRaridade = adesivo.raridade === 'Raro' ? 'bg-primary' : (adesivo.raridade === 'Lendário' ? 'bg-warning text-dark' : (adesivo.raridade === 'Tesouro' ? 'badge-tesouro' : 'bg-secondary'));

                let catTratada = adesivo.categoria || 'Urbano';
                let badgeCategoria = '';
                
                switch(catTratada) {
                    case 'Natureza': badgeCategoria = `<span class="badge bg-success mb-1">${catTratada}</span>`; break;
                    case 'Urbano': badgeCategoria = `<span class="badge bg-secondary mb-1">${catTratada}</span>`; break;
                    case 'Praia': badgeCategoria = `<span class="badge bg-info text-dark mb-1">${catTratada}</span>`; break;
                    case 'Turísticos': badgeCategoria = `<span class="badge bg-warning text-dark mb-1">${catTratada}</span>`; break;
                    case 'Estrada': badgeCategoria = `<span class="badge bg-dark mb-1">${catTratada}</span>`; break;
                    case 'Móveis': badgeCategoria = `<span class="badge bg-primary mb-1">${catTratada}</span>`; break;
                    case 'Estados': badgeCategoria = `<span class="badge bg-danger mb-1">${catTratada}</span>`; break;
                    case 'Internacionais': badgeCategoria = `<span class="badge mb-1" style="background-color: #6f42c1; color: white;">${catTratada}</span>`; break;
                    default: badgeCategoria = `<span class="badge bg-light text-dark border mb-1">${catTratada}</span>`;
                }
                
                const popupContent = `<div style="text-align: center; font-family: sans-serif; min-width: 160px;"><span class="badge bg-dark mb-1">#${adesivo.codigo || '00'}</span> ${badgeCategoria} <span class="badge ${corRaridade} mb-1">${adesivo.raridade}</span><br><strong>${adesivo.nome_local}</strong><br><small style="color: #666;">Colado por: <b>${adesivo.quem_colou}</b></small><br><img src="${adesivo.foto_caminho}" alt="Adesivo" onclick="abrirImagemMaior('${adesivo.foto_caminho}')" style="width: 150px; height: auto; margin-top: 8px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); cursor: pointer;"><br>${historicoHtml}${botaoAcaoHtml}${botaoMuralHtml}${boxAdminHtml}</div>`;
                
                marker.bindPopup(popupContent); 
                marcadoresCluster.addLayer(marker);

                // Salva o pino na memória usando o ID dele 
                marcadoresSalvos[adesivo.id] = marker; 
            });

            // A MÁGICA DO VOO 
            // Verifica se veio algum "?adesivo=123" no link lá do Feed
            const urlParams = new URLSearchParams(window.location.search);
            const adesivoFocoId = urlParams.get('adesivo');
            
            if (adesivoFocoId && marcadoresSalvos[adesivoFocoId]) {
                let marcadorFoco = marcadoresSalvos[adesivoFocoId];
                
                // Dá zoom sozinho até a rua do adesivo e abre a janelinha (popup)
                marcadoresCluster.zoomToShowLayer(marcadorFoco, function() {
                    marcadorFoco.openPopup();
                });
            }
        }

        function abrirModalEditar(id, nomeLocal, categoria, raridade) { 
            document.getElementById('editAdesivoId').value = id; 
            document.getElementById('editNomeLocal').value = nomeLocal; 
            document.getElementById('editCategoria').value = categoria || 'Urbano'; 
            document.getElementById('editRaridade').value = raridade || 'Comum'; 
            
            document.getElementById('editRaridade').disabled = !usuarioAdmin;

            new bootstrap.Modal(document.getElementById('modalEditar')).show(); 
        }
        document.getElementById('btnSalvarEdicao').addEventListener('click', function () { const formData = new FormData(); formData.append('id', document.getElementById('editAdesivoId').value); formData.append('usuario_id', meuId); formData.append('nomeLocal', document.getElementById('editNomeLocal').value); formData.append('categoria', document.getElementById('editCategoria').value); formData.append('raridade', document.getElementById('editRaridade').value); const btn = this; const textoOriginal = btn.innerHTML; btn.innerHTML = 'Salvando...'; btn.disabled = true; fetch('api/editar_adesivo.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => { if (data.sucesso) location.reload(); else alert('Erro: ' + data.erro); }).finally(() => { btn.innerHTML = textoOriginal; btn.disabled = false; }); });
        function abrirModalDescoberta(idAdesivo, nomeLocal, nivelAtual) { document.getElementById('descAdesivoId').value = idAdesivo; document.getElementById('descNomeLocal').innerText = nomeLocal; document.getElementById('descSelfie').value = ''; document.getElementById('descComentario').value = ''; document.getElementById('optAvistado').style.display = nivelAtual >= 1 ? 'none' : 'block'; document.getElementById('optEncontrado').style.display = nivelAtual >= 2 ? 'none' : 'block'; if (nivelAtual === 2) document.querySelector('input[value="conquistado"]').checked = true; else if (nivelAtual === 1) document.querySelector('input[value="encontrado"]').checked = true; else document.querySelector('input[value="avistado"]').checked = true; toggleFotoReq(); new bootstrap.Modal(document.getElementById('modalDescoberta')).show(); }
        document.getElementById('btnSalvarDescoberta').addEventListener('click', function () { const adesivoId = document.getElementById('descAdesivoId').value; const tipo = document.querySelector('input[name="tipoRegistro"]:checked').value; const formData = new FormData(); formData.append('adesivo_id', adesivoId); formData.append('descobridor_id', meuId); formData.append('comentario', document.getElementById('descComentario').value); formData.append('tipo_registro', tipo); if (document.getElementById('descSelfie').files[0]) { formData.append('selfie', document.getElementById('descSelfie').files[0]); } const btn = this; const textoOriginal = btn.innerHTML; btn.innerHTML = 'Enviando...'; btn.disabled = true; fetch('api/descobrir.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => { if (data.sucesso) { alert(data.mensagem); location.reload(); } else { alert('Aviso: ' + data.erro); } }).finally(() => { btn.innerHTML = textoOriginal; btn.disabled = false; }); });
        function abrirMural(idAdesivo, nomeLocal) { document.getElementById('muralNomeLocal').innerText = nomeLocal; document.getElementById('conteudoMural').innerHTML = '<div class="text-center">Carregando...</div>'; new bootstrap.Modal(document.getElementById('modalMural')).show(); fetch(`api/mural.php?adesivo_id=${idAdesivo}`).then(r => r.json()).then(data => { if (data.sucesso) { let html = ''; data.dados.forEach(item => { const dataF = new Date(item.data_descoberta).toLocaleString('pt-BR'); const selfieHtml = item.foto_selfie ? `<img src="${item.foto_selfie}" class="img-fluid rounded mt-2 mb-2" style="max-height: 250px; width: 100%; object-fit: cover;">` : ''; let iconeNivel = item.tipo_registro === 'conquistado' ? '👑' : (item.tipo_registro === 'encontrado' ? '📸' : '👁️'); html += `<div class="card border-0 shadow-sm rounded-4 mb-3"><div class="card-body py-2 px-3"><div class="d-flex justify-content-between align-items-center"><strong class="text-primary">${iconeNivel} ${item.apelido}</strong><small class="text-muted" style="font-size: 0.7rem;">${dataF}</small></div><p class="mb-1 mt-1">"${item.comentario || 'Passou por aqui!'}"</p>${selfieHtml}</div></div>`; }); document.getElementById('conteudoMural').innerHTML = html; } }); }
        function deletarAdesivo(id) { if (confirm("Tem certeza que deseja apagar este adesivo?")) { fetch('api/excluir.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: id, usuario_id: meuId }) }).then(r => r.json()).then(d => { if (d.sucesso) location.reload(); else alert("Erro: " + d.erro); }); } }
        function abrirImagemMaior(caminho) { document.getElementById('imagemMaiorSrc').src = caminho; new bootstrap.Modal(document.getElementById('modalImagemMaior')).show(); }

        carregarAdesivos();
    </script>
    <script>if ('serviceWorker' in navigator) { window.addEventListener('load', () => { navigator.serviceWorker.register('sw.js'); }); }</script>
</body>

</html>