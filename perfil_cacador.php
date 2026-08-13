<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Perfil do Caçador - O Bando</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body { background-color: #f0f2f5; padding-top: 70px; padding-bottom: 90px; }
        .mobile-top-bar { position: fixed; top: 0; width: 100%; background: white; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); z-index: 1050; padding: 10px 15px; display: flex; align-items: center; justify-content: space-between; }
        .mobile-bottom-nav { position: fixed; bottom: 0; width: 100%; background: white; box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.08); display: flex; justify-content: space-around; padding: 10px 0; z-index: 1050; padding-bottom: env(safe-area-inset-bottom, 10px); }
        .nav-item-mobile { text-align: center; color: #adb5bd; text-decoration: none; font-size: 0.75rem; display: flex; flex-direction: column; align-items: center; width: 20%; transition: color 0.2s; }
        .nav-item-mobile.active { color: #0d6efd; font-weight: bold; }
        .nav-item-mobile.active i { color: #0d6efd !important; }
        @media (min-width: 768px) { body { padding-bottom: 30px; padding-top: 80px; } }

        /* Estilos do Perfil e Álbum */
        .perfil-header { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); text-align: center; margin-bottom: 25px; border-top: 5px solid #ffc107; }
        .card-figurinha { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); transition: transform 0.2s; height: 100%; }
        .img-adesivo { width: 100%; height: 200px; object-fit: cover; cursor: pointer; }
        .img-selfie { width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid white; position: absolute; top: 10px; right: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); cursor: pointer; }
        .badge-codigo { position: absolute; top: 10px; left: 10px; font-size: 0.9rem; }
        .link-mapa { text-decoration: none; color: #212529; transition: color 0.2s; }
        .link-mapa:hover { color: #0d6efd; }

        /* Estilos das Medalhas Clicáveis */
        .medalha-mini { font-size: 1.5rem; background: #f8f9fa; border: 1px solid #e9ecef; padding: 5px 10px; border-radius: 8px; display: inline-block; margin: 3px; cursor: pointer; transition: transform 0.2s, border-color 0.2s; }
        .medalha-mini:hover { transform: scale(1.1); border-color: #ffc107; box-shadow: 0 2px 5px rgba(255, 193, 7, 0.3); }
        .medalha-box { border-radius: 12px; padding: 15px; text-align: center; border: 1px solid #dee2e6; background-color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.05); height: 100%; }
        .medalha-icone { font-size: 2.5rem; margin-bottom: 5px; }
    </style>
</head>

<body>
    <?php $menuAtivo = 'mais'; require './includes/navbar.php'; ?>

    <div class="container mb-5" style="max-width: 900px;">
        <button onclick="history.back()" class="btn btn-light shadow-sm mb-3 fw-bold text-primary">
            <i class="bi bi-arrow-left"></i> Voltar
        </button>

        <!-- CABEÇALHO DO PERFIL -->
        <div class="perfil-header" id="perfilLoading">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Buscando informações do caçador...</p>
        </div>

        <div class="perfil-header d-none" id="perfilConteudo">
            <img src="assets/loading.gif" id="perfilFoto" class="rounded-circle border border-3 border-primary mb-3 bg-white" style="width: 100px; height: 100px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
            <h2 id="perfilNome" class="fw-bold mb-1">Nome</h2>
            <span id="perfilTitulo" class="badge bg-dark mb-3 fs-6">Título</span>
            <h4 class="text-success fw-bold mb-4"><span id="perfilXP">0</span> XP</h4>
            <div id="perfilMedalhas" class="d-flex flex-wrap justify-content-center gap-1"></div>
            <small class="text-muted d-block mt-2" style="font-size: 0.7rem;"><i class="bi bi-hand-index-thumb"></i> Toque nas medalhas para ver detalhes</small>
        </div>

        <!-- ÁLBUM DO CAÇADOR -->
        <div class="text-center mb-4">
            <div class="btn-group mt-1 w-100" style="max-width: 400px;" role="group">
                <button type="button" class="btn btn-primary fw-bold" id="btnAchados" onclick="mostrarAba('achados')">🔍 Encontrados</button>
                <button type="button" class="btn btn-outline-primary fw-bold" id="btnColados" onclick="mostrarAba('colados')">📍 Colados</button>
            </div>
            <p class="text-muted mt-2 small" id="contadorAdesivos"></p>
        </div>

        <div class="row g-4" id="gridAchados"></div>
        <div class="row g-4" id="gridColados" style="display: none;"></div>
    </div>

    <!-- 👇 MODAL DO QUADRO DE MEDALHAS 👇 -->
    <div class="modal fade" id="modalMedalhas" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-warning" style="border-width: 3px;">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">🏅 Conquistas de <span id="nomeModalMedalhas"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4" style="background-color: #fffdf5;">
                    <div class="row g-2" id="conteudoModalMedalhas"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE IMAGEM AMPLIADA -->
    <div class="modal fade" id="modalImagemMaior" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0"><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-center pt-0"><img id="imagemMaiorSrc" src="" alt="Foto" style="max-width: 100%; max-height: 80vh; border-radius: 8px;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const alvoId = urlParams.get('id');

        if (!alvoId) {
            alert("Caçador não especificado!");
            window.location.href = 'ranking.php';
        }

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
            if (aba === 'achados') {
                document.getElementById('btnAchados').className = 'btn btn-primary fw-bold';
                document.getElementById('btnColados').className = 'btn btn-outline-primary fw-bold';
                document.getElementById('gridAchados').style.display = 'flex';
                document.getElementById('gridColados').style.display = 'none';
                document.getElementById('contadorAdesivos').innerText = `Encontrou ${listaAchados.length} adesivo(s).`;
            } else {
                document.getElementById('btnAchados').className = 'btn btn-outline-primary fw-bold';
                document.getElementById('btnColados').className = 'btn btn-primary fw-bold';
                document.getElementById('gridAchados').style.display = 'none';
                document.getElementById('gridColados').style.display = 'flex';
                document.getElementById('contadorAdesivos').innerText = `Espalhou ${listaColados.length} adesivo(s).`;
            }
        }

        function abrirImagemMaior(caminho) {
            document.getElementById('imagemMaiorSrc').src = caminho;
            new bootstrap.Modal(document.getElementById('modalImagemMaior')).show();
        }

        function abrirModalMedalhas() {
            new bootstrap.Modal(document.getElementById('modalMedalhas')).show();
        }

        // CARREGAR OS DADOS DO TOPO (PERFIL)
        fetch(`api/perfil.php?id=${alvoId}`)
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    document.getElementById('perfilLoading').classList.add('d-none');
                    document.getElementById('perfilConteudo').classList.remove('d-none');
                    
                    document.getElementById('perfilNome').innerText = data.perfil.apelido;
                    document.getElementById('nomeModalMedalhas').innerText = data.perfil.apelido;
                    document.getElementById('perfilTitulo').innerText = data.perfil.titulo;
                    document.getElementById('perfilXP').innerText = data.perfil.xp;

                    if (data.perfil.foto_perfil) {
                        document.getElementById('perfilFoto').src = data.perfil.foto_perfil;
                    } else {
                        document.getElementById('perfilFoto').src = `https://ui-avatars.com/api/?name=${data.perfil.apelido}&background=0d6efd&color=fff&size=256&bold=true`;
                    }
                    
                    // Renderiza as medalhinhas e também o conteúdo do Modal Detalhado
                    let mHtml = '';
                    let modalHtml = '';
                    data.perfil.medalhas.forEach(m => {
                        if(m.desbloqueada) {
                            mHtml += `<span class="medalha-mini" title="${m.nome}" onclick="abrirModalMedalhas()">${m.icone}</span>`;
                            modalHtml += `
                                <div class="col-6">
                                    <div class="medalha-box">
                                        <div class="medalha-icone">${m.icone}</div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">${m.nome}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">${m.desc}</small>
                                    </div>
                                </div>`;
                        }
                    });
                    
                    if(mHtml === '') {
                        mHtml = '<small class="text-muted">Ainda não possui medalhas.</small>';
                        modalHtml = '<div class="col-12 text-muted mt-2">Nenhuma medalha conquistada ainda.</div>';
                    }
                    
                    document.getElementById('perfilMedalhas').innerHTML = mHtml;
                    document.getElementById('conteudoModalMedalhas').innerHTML = modalHtml;
                }
            });

        // CARREGAR O ÁLBUM DA PESSOA
        fetch(`api/meu_album.php?usuario_id=${alvoId}`)
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) {
                    listaAchados = data.achados;
                    listaColados = data.colados;

                    const gridAchados = document.getElementById('gridAchados');
                    const gridColados = document.getElementById('gridColados');

                    if (listaAchados.length === 0) {
                        gridAchados.innerHTML = `<div class="col-12 text-center text-muted mt-4">Nenhum adesivo encontrado.</div>`;
                    } else {
                        let htmlAchados = '';
                        listaAchados.forEach(item => {
                            const selfieHtml = item.foto_selfie ? `<img src="${item.foto_selfie}" class="img-selfie" onclick="abrirImagemMaior('${item.foto_selfie}')">` : '';
                            htmlAchados += `
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card card-figurinha">
                                        <div class="position-relative">
                                            <span class="badge bg-dark badge-codigo">${item.codigo}</span>
                                            ${selfieHtml}
                                            <img src="${item.foto_original}" class="img-adesivo" onclick="abrirImagemMaior('${item.foto_original}')">
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            <h6 class="fw-bold mb-1 text-truncate"><a href="index.php?adesivo=${item.id}" class="link-mapa"><i class="bi bi-geo-alt-fill text-primary"></i> ${item.nome_local}</a></h6>
                                            <div class="mb-1">${getBadgeCategoria(item.categoria)}</div>
                                            <span class="badge ${determinarCorRaridade(item.raridade)}" style="font-size: 0.7rem;">${item.raridade}</span>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        gridAchados.innerHTML = htmlAchados;
                    }

                    if (listaColados.length === 0) {
                        gridColados.innerHTML = `<div class="col-12 text-center text-muted mt-4">Nenhum adesivo colado.</div>`;
                    } else {
                        let htmlColados = '';
                        listaColados.forEach(item => {
                            htmlColados += `
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card card-figurinha border border-2 border-primary">
                                        <div class="position-relative">
                                            <span class="badge bg-dark badge-codigo">${item.codigo}</span>
                                            <img src="${item.foto_original}" class="img-adesivo" onclick="abrirImagemMaior('${item.foto_original}')">
                                        </div>
                                        <div class="card-body p-2 text-center">
                                            <h6 class="fw-bold mb-1 text-truncate"><a href="index.php?adesivo=${item.id}" class="link-mapa"><i class="bi bi-geo-alt-fill text-primary"></i> ${item.nome_local}</a></h6>
                                            <div class="mb-1">${getBadgeCategoria(item.categoria)}</div>
                                            <span class="badge ${determinarCorRaridade(item.raridade)}" style="font-size: 0.7rem;">${item.raridade}</span>
                                        </div>
                                    </div>
                                </div>`;
                        });
                        gridColados.innerHTML = htmlColados;
                    }

                    mostrarAba('achados');
                }
            });
    </script>
</body>
</html>