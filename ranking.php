<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Ranking - Bando Map</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
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

        .ranking-item {
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .ranking-item:hover {
            border-color: #ffc107;
            transform: translateY(-2px);
        }

        .ranking-pos {
            font-size: 1.5rem;
            font-weight: bold;
            color: #adb5bd;
            width: 40px;
            text-align: center;
        }

        .pos-1 {
            color: #ffc107;
            font-size: 2rem;
        }

        .pos-2 {
            color: #c0c0c0;
            font-size: 1.8rem;
        }

        .pos-3 {
            color: #cd7f32;
            font-size: 1.6rem;
        }

        .medalha-box {
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .medalha-bloqueada {
            filter: grayscale(100%);
            opacity: 0.4;
        }

        .medalha-icone {
            font-size: 2.5rem;
            margin-bottom: 5px;
        }

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


        .badge-codigo {
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 1rem;
        }

        .badge-ranking-adesivo {
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

        .medalha-ranking-lista {
            font-size: 1rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 2px 6px;
            border-radius: 6px;
            display: inline-block;
        }

        /* 👇 Efeito no link do nome do local 👇 */
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
    // 1. Avisa para o menu que estamos na tela do Ranking
    $menuAtivo = 'mais'; 
    
    // 2. Importa todo o código do menu
    require './includes/navbar.php'; 
    ?>

    <!-- CONTEÚDO PRINCIPAL (TABS) -->
    <div class="container ranking-container mb-5">
        <div class="text-center mb-4 mt-3">
            <h2 class="fw-bold text-dark d-none d-md-block">Estatísticas do Bando</h2>
            <div class="btn-group mt-2 w-100" style="max-width: 400px;" role="group">
                <button type="button" class="btn btn-primary fw-bold" id="btnAbaCacadores"
                    onclick="mostrarAba('cacadores')">Melhores caçadores</button>
                <button type="button" class="btn btn-outline-primary fw-bold" id="btnAbaAdesivos"
                    onclick="mostrarAba('adesivos')">Melhores adesivos</button>
            </div>
        </div>

        <div id="abaCacadores" style="display: block; max-width: 650px; margin: 0 auto;">
            <div id="listaRanking" class="d-flex flex-column gap-3">
                <div class="text-center text-muted">Aguardando dados...</div>
            </div>
        </div>
        <div id="abaAdesivos" style="display: none;">
            <div class="row g-4" id="gridRankingAdesivos">
                <div class="text-center text-muted mt-5 w-100">Carregando locais...</div>
            </div>
        </div>
    </div>

    <!-- MODAIS (Perfil e Imagem) -->
    <div class="modal fade" id="modalPerfil" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-warning" style="border-width: 3px;">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">🪪 Cartão de Caçador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <div id="perfilLoading" class="text-muted">Procurando informações...</div>
                    <div id="perfilConteudo" class="d-none">
                        <h2 id="perfilNome" class="fw-bold mb-0"></h2>
                        <span id="perfilTitulo" class="badge bg-dark mb-3 fs-6"></span>
                        <h4 class="text-success fw-bold mb-4"><span id="perfilXP"></span> XP</h4>
                        <h6 class="fw-bold text-start border-bottom pb-2 mb-3">🏅 Quadro de Medalhas</h6>
                        <div class="row g-2" id="perfilMedalhas"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImagemMaior" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0"><button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-center pt-0"><img id="imagemMaiorSrc" src="" alt="Foto Ampliada"
                        style="max-width: 100%; max-height: 80vh; border-radius: 8px;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const meuId = localStorage.getItem('bando_id');
        const meuApelido = localStorage.getItem('bando_apelido');
        if (!meuId) window.location.href = 'login.php';
        if (document.getElementById('nomeLogado')) document.getElementById('nomeLogado').innerText = meuApelido;
        if (document.getElementById('nomeLogadoMobile')) document.getElementById('nomeLogadoMobile').innerText = meuApelido;

        // Verifica se é admin para mostrar os botões
        fetch('api/listar.php?usuario_id=' + meuId).then(r => r.json()).then(data => {
            if (data.sucesso && data.is_admin) {
                document.getElementById('badgeAdmin').classList.remove('d-none');
                document.getElementById('badgeAdminMobile').classList.remove('d-none');
                document.getElementById('btnMenuAdmin').classList.remove('d-none');
                document.getElementById('btnMenuAdminMobile').classList.remove('d-none');
            }
        });

        function sairDoApp() { localStorage.clear(); window.location.href = 'login.php'; }

        function mostrarAba(aba) {
            document.getElementById('btnAbaCacadores').className = aba === 'cacadores' ? 'btn btn-primary fw-bold' : 'btn btn-outline-primary fw-bold';
            document.getElementById('btnAbaAdesivos').className = aba === 'adesivos' ? 'btn btn-primary fw-bold' : 'btn btn-outline-primary fw-bold';
            document.getElementById('abaCacadores').style.display = aba === 'cacadores' ? 'block' : 'none';
            document.getElementById('abaAdesivos').style.display = aba === 'adesivos' ? 'block' : 'none';
        }

        function carregarRankingCacadores() {
            fetch('api/ranking.php').then(res => res.json()).then(data => {
                if (data.sucesso) {
                    let html = '';
                    data.dados.forEach((user, index) => {
                        let posClass = index === 0 ? 'pos-1' : (index === 1 ? 'pos-2' : (index === 2 ? 'pos-3' : ''));
                        let posText = index < 3 ? '🏆' : (index + 1) + 'º';
                        
                        let medalhasHtml = '';
                        if (user.medalhas && user.medalhas.length > 0) {
                            medalhasHtml = `<div class="d-flex flex-wrap gap-1 mt-1">${user.medalhas.map(icone => `<span class="medalha-ranking-lista">${icone}</span>`).join('')}</div>`;
                        }

                        html += `
                            <div class="ranking-item d-flex align-items-center" onclick="abrirPerfil(${user.id})">
                                <div class="ranking-pos ${posClass} me-3">${posText}</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <h5 class="mb-0 fw-bold text-dark">${user.apelido}</h5>
                                        <small class="badge bg-secondary">${user.titulo}</small>
                                    </div>
                                    ${medalhasHtml}
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-0 fw-bold text-success">${user.xp_total} XP</h5>
                                </div>
                            </div>
                        `;
                    });
                    document.getElementById('listaRanking').innerHTML = html;
                }
            });
        }

        function carregarRankingAdesivos() {
            fetch('api/ranking_adesivos.php').then(res => res.json()).then(data => {
                if (data.sucesso) {
                    const grid = document.getElementById('gridRankingAdesivos');
                    if (data.dados.length === 0) { grid.innerHTML = `<div class="col-12 text-center text-muted mt-5"><h4>Nenhum adesivo no mapa ainda!</h4></div>`; return; }
                    let html = '';
                    data.dados.forEach((adesivo, index) => {
                        let corMedalha = index === 0 ? 'bg-warning text-dark' : (index === 1 ? 'bg-light text-dark' : (index === 2 ? 'bg-dark text-white' : 'bg-secondary text-white'));
                        html += `
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card card-figurinha">
                                <div class="position-relative">
                                    <span class="badge ${corMedalha} badge-ranking-adesivo">#${index + 1}</span>
                                    <img src="${adesivo.foto_original}" class="img-adesivo" onclick="abrirImagemMaior('${adesivo.foto_original}')">
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-1">
                                        <a href="index.php?adesivo=${adesivo.id}" class="link-mapa">
                                            <i class="bi bi-geo-alt-fill text-primary"></i> ${adesivo.nome_local}
                                        </a>
                                    </h5>
                                    <small class="text-muted d-block mb-3">Colado por: <b>${adesivo.quem_colou}</b></small>
                                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded">
                                        <span class="text-muted fw-bold">Descobertas:</span>
                                        <span class="info-achados">${adesivo.total_achados}</span>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                    grid.innerHTML = html;
                }
            });
        }

        function abrirPerfil(id) {
            new bootstrap.Modal(document.getElementById('modalPerfil')).show(); document.getElementById('perfilLoading').classList.remove('d-none'); document.getElementById('perfilConteudo').classList.add('d-none');
            fetch('api/perfil.php?id=' + id).then(r => r.json()).then(data => {
                if (data.sucesso) {
                    document.getElementById('perfilLoading').classList.add('d-none'); document.getElementById('perfilConteudo').classList.remove('d-none');
                    document.getElementById('perfilNome').innerText = data.perfil.apelido; document.getElementById('perfilTitulo').innerText = data.perfil.titulo; document.getElementById('perfilXP').innerText = data.perfil.xp;
                    let mHtml = ''; data.perfil.medalhas.forEach(m => { mHtml += `<div class="col-6"><div class="medalha-box h-100 ${m.desbloqueada ? '' : 'medalha-bloqueada bg-light'}"><div class="medalha-icone">${m.icone}</div><h6 class="fw-bold ${m.desbloqueada ? 'text-dark' : 'text-muted'} mb-1" style="font-size: 0.9rem;">${m.nome}</h6><small class="text-muted" style="font-size: 0.75rem;">${m.desc}</small></div></div>`; });
                    document.getElementById('perfilMedalhas').innerHTML = mHtml;
                }
            });
        }

        function abrirImagemMaior(caminho) { document.getElementById('imagemMaiorSrc').src = caminho; new bootstrap.Modal(document.getElementById('modalImagemMaior')).show(); }

        carregarRankingCacadores();
        carregarRankingAdesivos();
    </script>
</body>

</html>