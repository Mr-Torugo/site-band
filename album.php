<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Meu Álbum - O Bando</title>

    <!-- CONEXÕES DO PWA (APLICATIVO) -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* =========================================
           MENUS RESPONSIVOS (PADRÃO DO APP)
           ========================================= */
        body {
            background-color: #f0f2f5;
            padding-top: 70px;
            padding-bottom: 90px;
        }

        .mobile-top-bar {
            position: fixed;
            top: 0;
            width: 100%;
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1050;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: white;
            box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1050;
            padding-bottom: env(safe-area-inset-bottom, 10px);
        }

        .nav-item-mobile {
            text-align: center;
            color: #adb5bd;
            text-decoration: none;
            font-size: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 20%;
            transition: color 0.2s;
        }

        .nav-item-mobile.active {
            color: #0d6efd;
            font-weight: bold;
        }

        .nav-item-mobile.active i {
            color: #0d6efd !important;
        }

        @media (min-width: 768px) {
            body {
                padding-bottom: 30px;
                padding-top: 80px;
            }
        }

        /* =========================================
           ESTILOS DO ÁLBUM E CARTÕES
           ========================================= */
        .card-figurinha {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            height: 100%;
        }

        .card-figurinha:hover {
            transform: translateY(-5px);
        }

        .img-adesivo {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s ease;
        }    

        .img-selfie {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid white;
            position: absolute;
            top: 10px;
            right: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }

        .badge-codigo {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 1rem;
        }

        /* Efeito no link do nome do local */
        .link-mapa {
            text-decoration: none;
            color: #212529;
            transition: color 0.2s;
        }
        .link-mapa:hover {
            color: #0d6efd;
        }
    </style>
</head>

<body>

    <?php 
    $menuAtivo = 'album'; 
    require './includes/navbar.php'; 
    ?>

    <div class="container mb-5">
        <div class="text-center mb-4 mt-3">
            <h2 class="fw-bold d-none d-md-block">Coleção de Adesivos</h2>
            <p class="text-muted" id="contadorAdesivos">Carregando...</p>

            <div class="btn-group mt-1 w-100" style="max-width: 400px;" role="group">
                <button type="button" class="btn btn-primary fw-bold" id="btnAchados" onclick="mostrarAba('achados')">
                    🔍 Encontrados
                </button>
                <button type="button" class="btn btn-outline-primary fw-bold" id="btnColados" onclick="mostrarAba('colados')">
                    📍 Colados
                </button>
            </div>
        </div>

        <div class="row g-4" id="gridAchados">
            <div class="text-center text-muted w-100">Carregando seus achados...</div>
        </div>

        <div class="row g-4" id="gridColados" style="display: none;"></div>
    </div>

    <!-- MODAL DE IMAGEM AMPLIADA -->
    <div class="modal fade" id="modalImagemMaior" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <img id="imagemMaiorSrc" src="" alt="Foto Ampliada" style="max-width: 100%; max-height: 80vh; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const meuId = localStorage.getItem('bando_id');
        const meuApelido = localStorage.getItem('bando_apelido');

        if (!meuId) {
            window.location.href = 'login.php';
        } else {
            if (document.getElementById('nomeLogado')) document.getElementById('nomeLogado').innerText = meuApelido;
            if (document.getElementById('nomeLogadoMobile')) document.getElementById('nomeLogadoMobile').innerText = meuApelido;
        }

        fetch('api/listar.php?usuario_id=' + meuId).then(r => r.json()).then(data => {
            if (data.sucesso && data.is_admin) {
                if (document.getElementById('badgeAdmin')) document.getElementById('badgeAdmin').classList.remove('d-none');
                if (document.getElementById('badgeAdminMobile')) document.getElementById('badgeAdminMobile').classList.remove('d-none');
                if (document.getElementById('btnMenuAdmin')) document.getElementById('btnMenuAdmin').classList.remove('d-none');
                if (document.getElementById('btnMenuAdminMobile')) document.getElementById('btnMenuAdminMobile').classList.remove('d-none');
            }
        });

        let listaAchados = [];
        let listaColados = [];

        function determinarCorRaridade(raridade) {
            if (raridade === 'Raro') return 'bg-primary';
            if (raridade === 'Lendário') return 'bg-warning text-dark';
            if (raridade === 'Tesouro') return 'bg-danger text-white border border-warning border-2';
            return 'bg-secondary';
        }

        function getBadgeCategoria(categoria) {
            let catTratada = categoria || 'Urbano';
            switch(catTratada) {
                case 'Natureza': return `<span class="badge bg-success me-1">${catTratada}</span>`;
                case 'Urbano': return `<span class="badge bg-secondary me-1">${catTratada}</span>`;
                case 'Praia': return `<span class="badge bg-info text-dark me-1">${catTratada}</span>`;
                case 'Turísticos': return `<span class="badge bg-warning text-dark me-1">${catTratada}</span>`;
                case 'Estrada': return `<span class="badge bg-dark me-1">${catTratada}</span>`;
                case 'Móveis': return `<span class="badge bg-primary me-1">${catTratada}</span>`;
                case 'Estados': return `<span class="badge bg-danger me-1">${catTratada}</span>`;
                case 'Internacionais': return `<span class="badge me-1" style="background-color: #6f42c1; color: white;">${catTratada}</span>`;
                default: return `<span class="badge bg-light text-dark border me-1">${catTratada}</span>`;
            }
        }

        function mostrarAba(aba) {
            const btnAchados = document.getElementById('btnAchados');
            const btnColados = document.getElementById('btnColados');
            const gridAchados = document.getElementById('gridAchados');
            const gridColados = document.getElementById('gridColados');

            if (aba === 'achados') {
                btnAchados.className = 'btn btn-primary fw-bold';
                btnColados.className = 'btn btn-outline-primary fw-bold';
                gridAchados.style.display = 'flex';
                gridColados.style.display = 'none';
                document.getElementById('contadorAdesivos').innerText = `Você descobriu ${listaAchados.length} adesivo(s).`;
            } else {
                btnAchados.className = 'btn btn-outline-primary fw-bold';
                btnColados.className = 'btn btn-primary fw-bold';
                gridAchados.style.display = 'none';
                gridColados.style.display = 'flex';
                document.getElementById('contadorAdesivos').innerText = `Você espalhou ${listaColados.length} adesivo(s) no mapa.`;
            }
        }

        function abrirImagemMaior(caminho) {
            document.getElementById('imagemMaiorSrc').src = caminho;
            new bootstrap.Modal(document.getElementById('modalImagemMaior')).show();
        }

        function sairDoApp() {
            fetch('api/logout.php')
                .then(res => res.json())
                .then(() => {
                    localStorage.clear();
                    window.location.href = 'login.php';
                })
                .catch(() => {
                    localStorage.clear();
                    window.location.href = 'login.php';
                });
        }

        function carregarAlbum() {
            fetch(`api/meu_album.php?usuario_id=${meuId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        listaAchados = data.achados;
                        listaColados = data.colados;

                        const gridAchados = document.getElementById('gridAchados');
                        const gridColados = document.getElementById('gridColados');

                        // MONTA A GRADE DE ACHADOS
                        if (listaAchados.length === 0) {
                            gridAchados.innerHTML = `<div class="col-12 text-center text-muted mt-4"><h4>Nenhum adesivo encontrado ainda.</h4><p>Abra o mapa e comece a caçada!</p></div>`;
                        } else {
                            let htmlAchados = '';
                            listaAchados.forEach(item => {
                                const dataObj = new Date(item.data_descoberta);
                                const dataFormatada = dataObj.toLocaleDateString('pt-BR');
                                const selfieHtml = item.foto_selfie ? `<img src="${item.foto_selfie}" class="img-selfie" alt="Sua Selfie" onclick="abrirImagemMaior('${item.foto_selfie}')">` : '';
                                const comentarioHtml = item.comentario ? `<p class="card-text text-muted small mt-2"><i>"${item.comentario}"</i></p>` : '';
                                
                                const corBadge = determinarCorRaridade(item.raridade);
                                const badgeCategoriaHtml = getBadgeCategoria(item.categoria);

                                // 👇 ADICIONADO AQUI: O Título agora é um Link com o Ícone do Mapa 👇
                                htmlAchados += `
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card card-figurinha">
                                            <div class="position-relative">
                                                <span class="badge bg-dark badge-codigo">${item.codigo}</span>
                                                ${selfieHtml}
                                                <img src="${item.foto_original}" class="img-adesivo" alt="Adesivo" onclick="abrirImagemMaior('${item.foto_original}')">
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold mb-0">
                                                    <a href="index.php?adesivo=${item.id}" class="link-mapa">
                                                        <i class="bi bi-geo-alt-fill text-primary"></i> ${item.nome_local}
                                                    </a>
                                                </h5>
                                                <div class="mt-2">
                                                    ${badgeCategoriaHtml}
                                                    <span class="badge ${corBadge}">${item.raridade}</span>
                                                    <small class="text-muted ms-2 d-block mt-1"><i class="bi bi-calendar3"></i> Achado em: ${dataFormatada}</small>
                                                </div>
                                                ${comentarioHtml}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            gridAchados.innerHTML = htmlAchados;
                        }

                        // MONTA A GRADE DE COLADOS
                        if (listaColados.length === 0) {
                            gridColados.innerHTML = `<div class="col-12 text-center text-muted mt-4"><h4>Você ainda não colou nenhum adesivo.</h4><p>Clique no botão de + no mapa para espalhar sua arte!</p></div>`;
                        } else {
                            let htmlColados = '';
                            listaColados.forEach(item => {
                                const dataObj = new Date(item.data_criacao);
                                const dataFormatada = dataObj.toLocaleDateString('pt-BR');
                                
                                const corBadge = determinarCorRaridade(item.raridade);
                                const badgeCategoriaHtml = getBadgeCategoria(item.categoria);

                                // 👇 ADICIONADO AQUI: O Título agora é um Link com o Ícone do Mapa 👇
                                htmlColados += `
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card card-figurinha border border-2 border-primary">
                                            <div class="position-relative">
                                                <span class="badge bg-dark badge-codigo">${item.codigo}</span>
                                                <img src="${item.foto_original}" class="img-adesivo" alt="Adesivo" onclick="abrirImagemMaior('${item.foto_original}')">
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold mb-0">
                                                    <a href="index.php?adesivo=${item.id}" class="link-mapa">
                                                        <i class="bi bi-geo-alt-fill text-primary"></i> ${item.nome_local}
                                                    </a>
                                                </h5>
                                                <div class="mt-2 mb-3">
                                                    ${badgeCategoriaHtml}
                                                    <span class="badge ${corBadge}">${item.raridade}</span>
                                                    <small class="text-muted ms-2 d-block mt-1"><i class="bi bi-calendar3"></i> Colado em: ${dataFormatada}</small>
                                                </div>
                                                <span class="badge bg-success w-100"><i class="bi bi-check-circle"></i> Criado por você</span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                            gridColados.innerHTML = htmlColados;
                        }

                        mostrarAba('achados');

                    } else {
                        alert("Erro ao carregar o álbum: " + data.erro);
                    }
                })
                .catch(err => {
                    document.getElementById('contadorAdesivos').innerText = "Erro ao conectar com o servidor.";
                });
        }

        carregarAlbum();
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => { navigator.serviceWorker.register('sw.js'); });
        }
    </script>
</body>

</html>