<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Adesivos Populares - O Bando</title>
    <!-- CONEXÕES DO PWA (APLICATIVO) -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* =========================================
           ESTILOS BASE E RESPONSIVIDADE
           ========================================= */
        body {
            background-color: #f0f2f5;
            padding-top: 70px;
            padding-bottom: 90px;
            /* Espaço para o menu inferior no celular */
        }

        /* Topo Celular */
        .mobile-top-bar {
            position: fixed;
            top: 0;
            width: 100%;
            background: #0d6efd;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            padding: 15px 20px;
            align-items: center;
            justify-content: center;
        }

        /* Menu Inferior Celular */
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
            text-decoration: none;
            font-size: 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 20%;
        }

        .nav-item-mobile.active {
            color: #0d6efd;
            font-weight: bold;
        }

        /* Container centralizado para PC */
        .ranking-container {
            max-width: 900px;
            margin: 0 auto;
        }

        @media (min-width: 768px) {
            body {
                padding-bottom: 30px;
                padding-top: 80px;
            }
        }

        /* =========================================
           ESTILOS DOS ADESIVOS
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

        .img-adesivo:hover {
            transform: scale(1.02);
        }

        .badge-codigo {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 1rem;
        }

        .badge-ranking {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 1.2rem;
            background-color: #FFD700;
            color: #000;
            border: 2px solid #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .info-achados {
            font-size: 1.5rem;
            font-weight: 900;
            color: #198754;
        }
    </style>
</head>

<body>

    <?php 
    // 1. Avisa para o menu que estamos na tela do Ranking de Adesivos
    $menuAtivo = 'mais'; 
    
    // 2. Importa todo o código do menu
    require './includes/navbar.php'; 
    ?>

    <!-- =========================================
         MODAL DE IMAGEM AMPLIADA
         ========================================= -->
    <div class="modal fade" id="modalImagemMaior" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <img id="imagemMaiorSrc" src="" alt="Foto Ampliada"
                        style="max-width: 100%; max-height: 80vh; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        if (!localStorage.getItem('bando_id')) {
            window.location.href = 'login.php';
        }

        // ===================================
        // FUNÇÃO DE AMPLIAR IMAGEM
        // ===================================
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

        function carregarRankingAdesivos() {
            fetch('api/ranking_adesivos.php')
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        const grid = document.getElementById('gridRankingAdesivos');
                        const adesivos = data.dados;

                        if (adesivos.length === 0) {
                            grid.innerHTML = `<div class="col-12 text-center text-muted mt-5"><h4>Nenhum adesivo no mapa ainda!</h4></div>`;
                            return;
                        }

                        let html = '';
                        adesivos.forEach((adesivo, index) => {
                            let corMedalha = 'bg-secondary text-white';
                            if (index === 0) corMedalha = 'bg-warning text-dark'; // Ouro
                            else if (index === 1) corMedalha = 'bg-light text-dark'; // Prata
                            else if (index === 2) corMedalha = 'bg-dark text-white'; // Bronze 

                            html += `
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="card card-figurinha">
                                        <div class="position-relative">
                                            <span class="badge bg-primary badge-codigo">#${adesivo.codigo}</span>
                                            <span class="badge ${corMedalha} badge-ranking">#${index + 1}</span>
                                            <img src="${adesivo.foto_original}" class="img-adesivo" alt="Adesivo" onclick="abrirImagemMaior('${adesivo.foto_original}')">
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title fw-bold mb-1">${adesivo.nome_local}</h5>
                                            <small class="text-muted d-block mb-3">Colado por: <b>${adesivo.quem_colou}</b></small>
                                            
                                            <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                                <span class="text-muted fw-bold">Total de Descobertas:</span>
                                                <span class="info-achados">${adesivo.total_achados}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        grid.innerHTML = html;
                    } else {
                        alert("Erro ao carregar o ranking de adesivos: " + data.erro);
                    }
                })
                .catch(err => {
                    document.getElementById('gridRankingAdesivos').innerHTML = '<div class="alert alert-danger mt-3">Erro de conexão com o servidor.</div>';
                });
        }

        carregarRankingAdesivos();
    </script>

    <!-- REGISTRO DO SERVICE WORKER (PWA) -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js');
            });
        }
    </script>
</body>

</html>