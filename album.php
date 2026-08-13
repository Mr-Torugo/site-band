<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Meu Perfil - O Bando</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="icon-192.png">

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

        /* Estilos do Perfil / Álbum */
        .foto-perfil-container { position: relative; display: inline-block; }
        .foto-perfil { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid #0d6efd; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background-color: white;}
        .btn-edit-foto { position: absolute; bottom: 0; right: 0; background: #ffc107; color: #000; border: 2px solid white; border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .btn-edit-foto:hover { transform: scale(1.1); }
        
        .card-figurinha { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); transition: transform 0.2s; height: 100%; }
        .card-figurinha:hover { transform: translateY(-5px); }
        .img-adesivo { width: 100%; height: 200px; object-fit: cover; cursor: pointer; transition: transform 0.2s ease; }    
        .img-selfie { width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 3px solid white; position: absolute; top: 10px; right: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3); cursor: pointer; }
        .badge-codigo { position: absolute; top: 10px; left: 10px; font-size: 1rem; }
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

    <?php $menuAtivo = 'album'; require './includes/navbar.php'; ?>

    <div class="container mb-5">
        
        <!-- CABEÇALHO DO PERFIL COM FOTO -->
        <div class="text-center mb-4 mt-3 bg-white p-4 rounded-4 shadow-sm border-top border-4 border-primary">
            <div class="foto-perfil-container mb-3">
                <img src="assets/loading.gif" id="imgMeuPerfil" class="foto-perfil" alt="Sua Foto de Perfil">
                <label for="inputFotoPerfil" class="btn-edit-foto" title="Trocar foto">
                    <i class="bi bi-camera-fill"></i>
                </label>
                <input type="file" id="inputFotoPerfil" class="d-none" accept="image/*" onchange="uploadFotoPerfil(this)">
            </div>
            
            <h3 class="fw-bold mb-0" id="nomeTopPerfil">Carregando...</h3>
            <span class="badge bg-dark mb-2 mt-1" id="tituloTopPerfil">...</span>
            <h5 class="text-success fw-bold"><span id="xpTopPerfil">0</span> XP</h5>
            <div id="medalhasTopPerfil" class="d-flex flex-wrap justify-content-center gap-1 mt-2"></div>
            <small class="text-muted d-block mt-2" style="font-size: 0.7rem;"><i class="bi bi-hand-index-thumb"></i> Toque nas medalhas para ver detalhes</small>
        </div>

        <div class="text-center mb-4 mt-3">
            <h4 class="fw-bold d-none d-md-block">Coleção de Adesivos</h4>
            <p class="text-muted" id="contadorAdesivos">Carregando...</p>
            <div class="btn-group mt-1 w-100" style="max-width: 400px;" role="group">
                <button type="button" class="btn btn-primary fw-bold" id="btnAchados" onclick="mostrarAba('achados')">🔍 Encontrados</button>
                <button type="button" class="btn btn-outline-primary fw-bold" id="btnColados" onclick="mostrarAba('colados')">📍 Colados</button>
            </div>
        </div>

        <div class="row g-4" id="gridAchados"><div class="text-center text-muted w-100">Carregando seus achados...</div></div>
        <div class="row g-4" id="gridColados" style="display: none;"></div>
    </div>

    <!-- 👇 MODAL DO QUADRO DE MEDALHAS 👇 -->
    <div class="modal fade" id="modalMedalhas" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-warning" style="border-width: 3px;">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">🏅 Suas Conquistas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4" style="background-color: #fffdf5;">
                    <div class="row g-2" id="conteudoModalMedalhas"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL IMAGEM AMPLIADA -->
    <div class="modal fade" id="modalImagemMaior" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0"><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
                <div class="modal-body text-center pt-0"><img id="imagemMaiorSrc" src="" alt="Foto Ampliada" style="max-width: 100%; max-height: 80vh; border-radius: 8px;"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const meuId = localStorage.getItem('bando_id');
        const meuApelido = localStorage.getItem('bando_apelido');

        if (!meuId) { window.location.href = 'login.php'; }
        else {
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

        function abrirModalMedalhas() {
            new bootstrap.Modal(document.getElementById('modalMedalhas')).show();
        }

        // CARREGAR DADOS DO PERFIL
        function carregarDadosMeuPerfil() {
            fetch(`api/perfil.php?id=${meuId}`)
            .then(r => r.json())
            .then(data => {
                if (data.sucesso) {
                    document.getElementById('nomeTopPerfil').innerText = data.perfil.apelido;
                    document.getElementById('tituloTopPerfil').innerText = data.perfil.titulo;
                    document.getElementById('xpTopPerfil').innerText = data.perfil.xp;
                    
                    if (data.perfil.foto_perfil) {
                        document.getElementById('imgMeuPerfil').src = data.perfil.foto_perfil;
                    } else {
                        document.getElementById('imgMeuPerfil').src = `https://ui-avatars.com/api/?name=${data.perfil.apelido}&background=0d6efd&color=fff&size=256&bold=true`;
                    }

                    // Renderiza as medalhinhas clicáveis e o conteúdo do Modal Detalhado
                    let mHtml = '';
                    let modalHtml = '';
                    data.perfil.medalhas.forEach(m => {
                        if(m.desbloqueada) { 
                            mHtml += `<span class="medalha-mini" title="${m.nome}" onclick="abrirModalMedalhas()">${m.icone}</span>`; 
                            modalHtml += `
                                <div class="col-6">
                                    <div class="medalha-box h-100">
                                        <div class="medalha-icone">${m.icone}</div>
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">${m.nome}</h6>
                                        <small class="text-muted" style="font-size: 0.75rem;">${m.desc}</small>
                                    </div>
                                </div>`;
                        }
                    });

                    if(mHtml === '') {
                        mHtml = '<small class="text-muted">Ainda não possui medalhas.</small>';
                        modalHtml = '<div class="col-12 text-muted mt-2">Você ainda não conquistou nenhuma medalha. Continue caçando!</div>';
                    }

                    document.getElementById('medalhasTopPerfil').innerHTML = mHtml;
                    document.getElementById('conteudoModalMedalhas').innerHTML = modalHtml;
                }
            });
        }

        function uploadFotoPerfil(input) {
            if (!input.files || input.files.length === 0) return;
            const file = input.files[0];
            const formData = new FormData();
            formData.append('usuario_id', meuId); formData.append('foto_perfil', file);

            const reader = new FileReader();
            reader.onload = function(e) { document.getElementById('imgMeuPerfil').src = e.target.result; }
            reader.readAsDataURL(file);

            fetch('api/upload_perfil.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.sucesso) { document.getElementById('imgMeuPerfil').src = data.foto_perfil + '?t=' + new Date().getTime(); } 
                else { alert("Erro ao trocar foto: " + data.erro); }
            }).catch(err => alert("Falha na conexão ao enviar a foto."));
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
            const btnAchados = document.getElementById('btnAchados'); const btnColados = document.getElementById('btnColados');
            const gridAchados = document.getElementById('gridAchados'); const gridColados = document.getElementById('gridColados');

            if (aba === 'achados') {
                btnAchados.className = 'btn btn-primary fw-bold'; btnColados.className = 'btn btn-outline-primary fw-bold';
                gridAchados.style.display = 'flex'; gridColados.style.display = 'none';
                document.getElementById('contadorAdesivos').innerText = `Você descobriu ${listaAchados.length} adesivo(s).`;
            } else {
                btnAchados.className = 'btn btn-outline-primary fw-bold'; btnColados.className = 'btn btn-primary fw-bold';
                gridAchados.style.display = 'none'; gridColados.style.display = 'flex';
                document.getElementById('contadorAdesivos').innerText = `Você espalhou ${listaColados.length} adesivo(s) no mapa.`;
            }
        }

        function abrirImagemMaior(caminho) {
            document.getElementById('imagemMaiorSrc').src = caminho;
            new bootstrap.Modal(document.getElementById('modalImagemMaior')).show();
        }

        function carregarAlbum() {
            fetch(`api/meu_album.php?usuario_id=${meuId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        listaAchados = data.achados; listaColados = data.colados;
                        const gridAchados = document.getElementById('gridAchados'); const gridColados = document.getElementById('gridColados');

                        if (listaAchados.length === 0) {
                            gridAchados.innerHTML = `<div class="col-12 text-center text-muted mt-4"><h4>Nenhum adesivo encontrado ainda.</h4><p>Abra o mapa e comece a caçada!</p></div>`;
                        } else {
                            let htmlAchados = '';
                            listaAchados.forEach(item => {
                                const dataFormatada = new Date(item.data_descoberta).toLocaleDateString('pt-BR');
                                const selfieHtml = item.foto_selfie ? `<img src="${item.foto_selfie}" class="img-selfie" alt="Sua Selfie" onclick="abrirImagemMaior('${item.foto_selfie}')">` : '';
                                const comentarioHtml = item.comentario ? `<p class="card-text text-muted small mt-2"><i>"${item.comentario}"</i></p>` : '';
                                
                                htmlAchados += `
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card card-figurinha">
                                            <div class="position-relative">
                                                <span class="badge bg-dark badge-codigo">${item.codigo}</span>
                                                ${selfieHtml}
                                                <img src="${item.foto_original}" class="img-adesivo" alt="Adesivo" onclick="abrirImagemMaior('${item.foto_original}')">
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold mb-0"><a href="index.php?adesivo=${item.id}" class="link-mapa"><i class="bi bi-geo-alt-fill text-primary"></i> ${item.nome_local}</a></h5>
                                                <div class="mt-2">${getBadgeCategoria(item.categoria)}<span class="badge ${determinarCorRaridade(item.raridade)}">${item.raridade}</span><small class="text-muted ms-2 d-block mt-1"><i class="bi bi-calendar3"></i> Achado em: ${dataFormatada}</small></div>
                                                ${comentarioHtml}
                                            </div>
                                        </div>
                                    </div>`;
                            });
                            gridAchados.innerHTML = htmlAchados;
                        }

                        if (listaColados.length === 0) {
                            gridColados.innerHTML = `<div class="col-12 text-center text-muted mt-4"><h4>Você ainda não colou nenhum adesivo.</h4><p>Clique no botão de + no mapa para espalhar sua arte!</p></div>`;
                        } else {
                            let htmlColados = '';
                            listaColados.forEach(item => {
                                const dataFormatada = new Date(item.data_criacao).toLocaleDateString('pt-BR');
                                htmlColados += `
                                    <div class="col-12 col-md-6 col-lg-4">
                                        <div class="card card-figurinha border border-2 border-primary">
                                            <div class="position-relative">
                                                <span class="badge bg-dark badge-codigo">${item.codigo}</span>
                                                <img src="${item.foto_original}" class="img-adesivo" alt="Adesivo" onclick="abrirImagemMaior('${item.foto_original}')">
                                            </div>
                                            <div class="card-body">
                                                <h5 class="card-title fw-bold mb-0"><a href="index.php?adesivo=${item.id}" class="link-mapa"><i class="bi bi-geo-alt-fill text-primary"></i> ${item.nome_local}</a></h5>
                                                <div class="mt-2 mb-3">${getBadgeCategoria(item.categoria)}<span class="badge ${determinarCorRaridade(item.raridade)}">${item.raridade}</span><small class="text-muted ms-2 d-block mt-1"><i class="bi bi-calendar3"></i> Colado em: ${dataFormatada}</small></div>
                                                <span class="badge bg-success w-100"><i class="bi bi-check-circle"></i> Criado por você</span>
                                            </div>
                                        </div>
                                    </div>`;
                            });
                            gridColados.innerHTML = htmlColados;
                        }
                        mostrarAba('achados');
                    }
                });
        }

        carregarDadosMeuPerfil();
        carregarAlbum();
    </script>
</body>
</html>