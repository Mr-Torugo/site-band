<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Feed de Atividades - O Bando</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <link rel="apple-touch-icon" href="icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        /* =========================================
           ESTILOS BASE E RESPONSIVIDADE (PADRÃO APP)
           ========================================= */
        body {
            background-color: #f0f2f5;
            padding-top: 70px;
            padding-bottom: 90px;
        }

        .mobile-top-bar { position: fixed; top: 0; width: 100%; background: white; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); z-index: 1050; padding: 10px 15px; display: flex; align-items: center; justify-content: space-between; }
        .mobile-bottom-nav { position: fixed; bottom: 0; width: 100%; background: white; box-shadow: 0 -2px 15px rgba(0, 0, 0, 0.08); display: flex; justify-content: space-around; padding: 10px 0; z-index: 1050; padding-bottom: env(safe-area-inset-bottom, 10px); }
        .nav-item-mobile { text-align: center; color: #adb5bd; text-decoration: none; font-size: 0.75rem; display: flex; flex-direction: column; align-items: center; width: 20%; transition: color 0.2s; }
        .nav-item-mobile.active { color: #0d6efd; font-weight: bold; }
        .nav-item-mobile.active i { color: #0d6efd !important; }

        /* Limitar a largura do Feed no PC */
        .feed-container { max-width: 650px; margin: 0 auto; }

        @media (min-width: 768px) {
            body { padding-bottom: 30px; padding-top: 80px; }
        }

        /* =========================================
           ESTILOS DO FEED
           ========================================= */
        .feed-item { border-left: 4px solid #dee2e6; margin-left: 15px; padding-left: 20px; position: relative; margin-bottom: 25px; }
        .feed-icone { position: absolute; left: -22px; top: 0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: white; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); }
        .icone-colou { background-color: #0d6efd; }
        .icone-achou { background-color: #212529; }
        .feed-card { background: white; border-radius: 12px; padding: 15px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }
        .feed-img { width: 100%; height: 450px; object-fit: cover; border-radius: 8px; margin-top: 12px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15); cursor: pointer; transition: transform 0.2s ease; }
        .feed-img:hover { transform: scale(1.02); }
        .curtidas-box { margin-top: 12px; padding-top: 10px; border-top: 1px solid #f0f2f5; display: flex; align-items: center; }
        .btn-curtir { border: none; background: none; font-size: 1.2rem; padding: 0 5px; color: #6c757d; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 6px; font-weight: bold; }
        .btn-curtir.curtido { color: #e63946; }
        .btn-curtir:hover { transform: scale(1.1); }
        .btn-curtir:active { transform: scale(0.9); }
        
        .chat-balao { background-color: #e9ecef; padding: 10px 14px; border-radius: 15px; border-bottom-left-radius: 4px; display: inline-block; max-width: 90%; }
        .chat-nome { font-size: 0.8rem; font-weight: bold; color: #0d6efd; margin-bottom: 2px; }
        .chat-texto { font-size: 0.95rem; margin-bottom: 0; }
        .chat-data { font-size: 0.7rem; color: #adb5bd; margin-top: 2px; text-align: right; }

        @keyframes pulse-gold {
            0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.8); }
            70% { box-shadow: 0 0 0 15px rgba(255, 193, 7, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
        }
        .badge-tesouro { background-color: #dc3545; color: white; animation: pulse-gold 2s infinite; border: 2px solid #ffc107; font-weight: bold; }

        /* Estilo para os Links de Perfil */
        .link-perfil { color: #212529; text-decoration: none; font-weight: bold; transition: color 0.2s; }
        .link-perfil:hover { color: #0d6efd; text-decoration: underline; }
    </style>
</head>

<body>

    <?php 
    $menuAtivo = 'radar'; 
    require './includes/navbar.php'; 
    ?>

    <!-- CONTEÚDO DO FEED -->
    <div class="container feed-container mb-2">
        <div class="text-center mb-4 mt-3 d-none d-md-block">
            <h2 class="fw-bold">Radar do Bando</h2>
            <p class="text-muted">A linha do tempo do mapa!</p>
        </div>

        <div id="listaFeed" class="mt-4">
            <div class="text-center text-muted">Carregando atividades...</div>
        </div>
    </div>

    <!-- MODAL DE IMAGEM AMPLIADA -->
    <div class="modal fade" id="modalImagemMaior" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center pt-0">
                    <img id="imagemMaiorSrc" src="" alt="Foto Ampliada" style="max-width: 100%; max-height: 80vh; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL DE COMENTÁRIOS -->
    <div class="modal fade" id="modalComentarios" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content" style="height: 80vh;">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-chat-text"></i> Comentários</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="background-color: #f8f9fa;">
                    <div id="listaDeComentarios" class="d-flex flex-column gap-3">
                        <div class="text-center text-muted">Carregando comentários...</div>
                    </div>
                </div>
                <div class="modal-footer d-block bg-white p-2">
                    <input type="hidden" id="comentTipoAcao">
                    <input type="hidden" id="comentAcaoId">
                    <div class="input-group">
                        <input type="text" class="form-control" id="inputNovoComentario" placeholder="Escreva um comentário..." autocomplete="off">
                        <button class="btn btn-primary fw-bold" id="btnEnviarComentario" onclick="enviarComentario()">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const meuId = localStorage.getItem('bando_id');
        const meuApelido = localStorage.getItem('bando_apelido');
        if (!meuId) { window.location.href = 'login.php'; }

        if (document.getElementById('nomeLogado')) document.getElementById('nomeLogado').innerText = meuApelido;
        if (document.getElementById('nomeLogadoMobile')) document.getElementById('nomeLogadoMobile').innerText = meuApelido;

        // Verifica admin
        fetch('api/listar.php?usuario_id=' + meuId).then(r => r.json()).then(data => {
            if (data.sucesso && data.is_admin) {
                if (document.getElementById('badgeAdmin')) document.getElementById('badgeAdmin').classList.remove('d-none');
                if (document.getElementById('badgeAdminMobile')) document.getElementById('badgeAdminMobile').classList.remove('d-none');
                if (document.getElementById('btnMenuAdmin')) document.getElementById('btnMenuAdmin').classList.remove('d-none');
                if (document.getElementById('btnMenuAdminMobile')) document.getElementById('btnMenuAdminMobile').classList.remove('d-none');
            }
        });

        function carregarFeed() {
            fetch('api/feed.php?usuario_id=' + meuId + '&nocache=' + new Date().getTime())
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        const lista = document.getElementById('listaFeed');
                        if (data.dados.length === 0) { lista.innerHTML = `<div class="text-center text-muted mt-5"><h4>O mapa está quieto.</h4></div>`; return; }

                        let html = '';
                        data.dados.forEach(item => {
                            const dataObj = new Date(item.data_acao);
                            const dataFormatada = dataObj.toLocaleDateString('pt-BR') + ' às ' + dataObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });

                            let corRaridade = item.raridade === 'Raro' ? 'bg-primary' : (item.raridade === 'Lendário' ? 'bg-warning text-dark' : (item.raridade === 'Tesouro' ? 'badge-tesouro' : 'bg-secondary'));

                            let catTratada = item.categoria || 'Urbano';
                            let badgeCategoria = '';
                            switch(catTratada) {
                                case 'Natureza': badgeCategoria = `<span class="badge bg-success me-1">${catTratada}</span>`; break;
                                case 'Urbano': badgeCategoria = `<span class="badge bg-secondary me-1">${catTratada}</span>`; break;
                                case 'Praia': badgeCategoria = `<span class="badge bg-info text-dark me-1">${catTratada}</span>`; break;
                                case 'Turísticos': badgeCategoria = `<span class="badge bg-warning text-dark me-1">${catTratada}</span>`; break;
                                case 'Estrada': badgeCategoria = `<span class="badge bg-dark me-1">${catTratada}</span>`; break;
                                case 'Móveis': badgeCategoria = `<span class="badge bg-primary me-1">${catTratada}</span>`; break;
                                case 'Estados': badgeCategoria = `<span class="badge bg-danger me-1">${catTratada}</span>`; break;
                                case 'Internacionais': badgeCategoria = `<span class="badge me-1" style="background-color: #6f42c1; color: white;">${catTratada}</span>`; break;
                                default: badgeCategoria = `<span class="badge bg-light text-dark border me-1">${catTratada}</span>`;
                            }

                            const fotoParaExibir = item.foto_registro || item.foto_adesivo || item.foto;
                            const fotoSafe = fotoParaExibir ? fotoParaExibir.replace(/'/g, "\\'") : '';
                            const imagemHtml = fotoSafe ? `<img src="${fotoSafe}" class="feed-img" alt="Foto da ação" onclick="abrirImagemMaior('${fotoSafe}')">` : '';

                            let iconeCoracao = item.curtido_por_mim || item.curtiu_mim ? 'bi-heart-fill' : 'bi-heart';
                            let classeCurtido = item.curtido_por_mim || item.curtiu_mim ? 'curtido' : '';
                            
                            let idAcaoTratado = item.acao_id || item.id_acao;
                            let tipoAcaoTratado = item.tipo || item.tipo_acao;

                            let htmlAcoes = `
                            <div class="curtidas-box">
                                <button class="btn-curtir ${classeCurtido}" onclick="toggleCurtida('${tipoAcaoTratado}', ${idAcaoTratado}, this)">
                                    <i class="bi ${iconeCoracao}"></i> <span class="contador">${item.total_curtidas}</span>
                                </button>
                                <button class="btn-curtir ms-4 text-dark" onclick="abrirModalComentarios('${tipoAcaoTratado}', ${idAcaoTratado})">
                                    <i class="bi bi-chat-text"></i> <span class="contador" id="contador-coment-${tipoAcaoTratado}-${idAcaoTratado}">${item.total_comentarios}</span>
                                </button>
                            </div>`;

                            // 👇 GERA O LINK CLICÁVEL COM O ID DE QUEM FEZ A AÇÃO 👇
                            let htmlQuemAgiu = `<a href="perfil_cacador.php?id=${item.usuario_id}" class="link-perfil">${item.nome_usuario}</a>`;

                            // 1. Verifica se é novo adesivo (colou)
                            if (tipoAcaoTratado === 'colou' || tipoAcaoTratado === 'novo_adesivo') {
                                let nomeLocal = item.local || item.nome_local;
                                
                                html += `
                                <div class="feed-item">
                                    <div class="feed-icone icone-colou"><i class="bi bi-pin-angle-fill"></i></div>
                                    <div class="feed-card">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">${dataFormatada}</small>
                                            <div>${badgeCategoria} <span class="badge ${corRaridade}">${item.raridade || 'Comum'}</span></div>
                                        </div>
                                        <p class="mb-0">${htmlQuemAgiu} colou um novo adesivo em <a href="index.php?adesivo=${item.adesivo_id}" class="text-decoration-none fw-bold" style="color: #0d6efd;"><i class="bi bi-geo-alt-fill"></i> ${nomeLocal}</a>.</p>
                                        ${imagemHtml}
                                        ${htmlAcoes}
                                    </div>
                                </div>`;
                            } 
                            // 2. Verifica se é descoberta (achou)
                            else if (tipoAcaoTratado === 'achou' || tipoAcaoTratado === 'descoberta') {
                                let nomeLocal = item.local || item.nome_local;
                                
                                // 👇 GERA O LINK CLICÁVEL DO DONO DO ADESIVO 👇
                                let donoAdesivo = item.criador_id 
                                    ? `<a href="perfil_cacador.php?id=${item.criador_id}" class="link-perfil">${item.criador_adesivo}</a>` 
                                    : `<span class="fw-bold">um caçador</span>`;
                                
                                let iconeAcao = '👁️'; let textoAcao = 'avistou o adesivo de';
                                if (item.tipo_registro === 'conquistado') { iconeAcao = '👑'; textoAcao = 'conquistou o adesivo de'; }
                                else if (item.tipo_registro === 'encontrado') { iconeAcao = '📸'; textoAcao = 'encontrou o adesivo de'; }

                                html += `
                                <div class="feed-item">
                                    <div class="feed-icone icone-achou">${iconeAcao}</div>
                                    <div class="feed-card">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">${dataFormatada}</small>
                                            <div>${badgeCategoria} <span class="badge ${corRaridade}">${item.raridade || 'Comum'}</span></div>
                                        </div>
                                        <p class="mb-0">${iconeAcao} ${htmlQuemAgiu} ${textoAcao} ${donoAdesivo} em <a href="index.php?adesivo=${item.adesivo_id}" class="text-decoration-none fw-bold text-success"><i class="bi bi-geo-alt-fill"></i> ${nomeLocal}</a>!</p>
                                        ${imagemHtml}
                                        ${htmlAcoes}
                                    </div>
                                </div>`;
                            }
                            // 3. Verifica se é Conquista
                            else if (tipoAcaoTratado === 'conquista') {
                                let nomeMedalha = item.local || item.nome_local;
                                let descMedalha = item.comentario || 'Alcançou um novo marco!';
                                let iconeMedalha = item.foto || item.foto_registro || '🏆';
                                
                                let displayIcone = iconeMedalha.includes('.') || iconeMedalha.includes('http') 
                                    ? `<img src="${iconeMedalha}" style="width: 80px; height: 80px; object-fit: contain;">` 
                                    : `<div style="font-size: 4rem; line-height: 1;">${iconeMedalha}</div>`;

                                html += `
                                <div class="feed-item">
                                    <div class="feed-icone bg-warning text-dark"><i class="bi bi-trophy-fill"></i></div>
                                    <div class="feed-card border border-warning border-2" style="background-color: #fffdf5;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">${dataFormatada}</small>
                                            <span class="badge bg-warning text-dark me-1">🏆 Conquista Desbloqueada</span>
                                        </div>
                                        <div class="text-center my-3">
                                            ${displayIcone}
                                            <h4 class="fw-bold text-dark mt-2">${nomeMedalha}</h4>
                                            <p class="text-muted small mb-0">"${descMedalha}"</p>
                                        </div>
                                        <p class="mb-0 text-center">Parabéns ${htmlQuemAgiu}, você entrou para a história!</p>
                                        ${htmlAcoes}
                                    </div>
                                </div>`;
                            }
                            // 4. Verifica se é Missão Concluída
                            else if (tipoAcaoTratado === 'missao') {
                                let nomeMissao = item.local || item.nome_local || 'Missão da Semana';
                                let textoRecompensa = item.comentario || 'Completou a missão e ganhou XP!';
                                
                                html += `
                                <div class="feed-item">
                                    <div class="feed-icone bg-success text-white"><i class="bi bi-bullseye"></i></div>
                                    <div class="feed-card border border-success border-2" style="background-color: #f8fff9;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <small class="text-muted">${dataFormatada}</small>
                                            <span class="badge bg-success text-white me-1">🎯 Missão Concluída</span>
                                        </div>
                                        <div class="text-center my-3">
                                            <div style="font-size: 4rem; line-height: 1;">🎯</div>
                                            <h4 class="fw-bold text-success mt-2">${nomeMissao}</h4>
                                            <p class="badge bg-warning text-dark fs-6 mt-1 mb-0">${textoRecompensa}</p>
                                        </div>
                                        <p class="mb-0 text-center">${htmlQuemAgiu} cumpriu o dever e garantiu a recompensa!</p>
                                        ${htmlAcoes}
                                    </div>
                                </div>`;
                            }
                        }); 
                        
                        lista.innerHTML = html;
                    }
                })
                .catch(err => console.error("Erro no Feed:", err));
        }

        // FUNÇÕES PADRÕES...
        function abrirImagemMaior(caminho) {
            document.getElementById('imagemMaiorSrc').src = caminho;
            new bootstrap.Modal(document.getElementById('modalImagemMaior')).show();
        }

        function toggleCurtida(tipoAcao, acaoId, btnElement) {
            const formData = new FormData(); formData.append('usuario_id', meuId); formData.append('tipo_acao', tipoAcao); formData.append('acao_id', acaoId);
            let icon = btnElement.querySelector('i'); let countSpan = btnElement.querySelector('.contador'); let curCount = parseInt(countSpan.innerText) || 0;
            if (btnElement.classList.contains('curtido')) {
                btnElement.classList.remove('curtido'); icon.classList.remove('bi-heart-fill'); icon.classList.add('bi-heart'); countSpan.innerText = Math.max(0, curCount - 1);
            } else {
                btnElement.classList.add('curtido'); icon.classList.remove('bi-heart'); icon.classList.add('bi-heart-fill'); countSpan.innerText = curCount + 1;
            }
            fetch('api/curtir.php', { method: 'POST', body: formData }).then(r => r.json()).catch(e => console.log(e));
        }

        let modalComentariosObj;
        function abrirModalComentarios(tipoAcao, acaoId) {
            if (!modalComentariosObj) { modalComentariosObj = new bootstrap.Modal(document.getElementById('modalComentarios')); }
            document.getElementById('comentTipoAcao').value = tipoAcao; document.getElementById('comentAcaoId').value = acaoId;
            document.getElementById('inputNovoComentario').value = ''; document.getElementById('listaDeComentarios').innerHTML = '<div class="text-center text-muted mt-3">Carregando comentários...</div>';
            modalComentariosObj.show(); carregarComentariosDaAcao(tipoAcao, acaoId);
        }

        function carregarComentariosDaAcao(tipoAcao, acaoId) {
            fetch(`api/feed_comentarios_lista.php?tipo_acao=${tipoAcao}&acao_id=${acaoId}&nocache=` + new Date().getTime())
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        const lista = document.getElementById('listaDeComentarios');
                        if (data.dados.length === 0) { lista.innerHTML = '<div class="text-center text-muted mt-4">Nenhum comentário ainda. Seja o primeiro!</div>'; return; }
                        let html = '';
                        data.dados.forEach(c => {
                            const dataObj = new Date(c.data_comentario);
                            const strData = dataObj.toLocaleDateString('pt-BR') + ' ' + dataObj.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                            html += `
                            <div class="d-flex flex-column align-items-start">
                                <div class="chat-balao shadow-sm">
                                    <div class="chat-nome">${c.apelido}</div>
                                    <p class="chat-texto">${c.comentario}</p>
                                </div>
                                <div class="chat-data w-100 ps-1" style="text-align: left;">${strData}</div>
                            </div>`;
                        });
                        lista.innerHTML = html;
                        const modalBody = document.querySelector('#modalComentarios .modal-body');
                        modalBody.scrollTop = modalBody.scrollHeight;
                    }
                });
        }

        function enviarComentario() {
            const input = document.getElementById('inputNovoComentario'); const texto = input.value.trim(); if (texto === '') return;
            const tipoAcao = document.getElementById('comentTipoAcao').value; const acaoId = document.getElementById('comentAcaoId').value;
            const btn = document.getElementById('btnEnviarComentario'); btn.disabled = true;
            const formData = new FormData(); formData.append('usuario_id', meuId); formData.append('tipo_acao', tipoAcao); formData.append('acao_id', acaoId); formData.append('comentario', texto);
            fetch('api/feed_comentar.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.sucesso) {
                        input.value = ''; carregarComentariosDaAcao(tipoAcao, acaoId);
                        let contadorSpan = document.getElementById(`contador-coment-${tipoAcao}-${acaoId}`);
                        if (contadorSpan) { contadorSpan.innerText = parseInt(contadorSpan.innerText) + 1; }
                    } else { alert('Erro ao enviar comentário.'); }
                })
                .finally(() => { btn.disabled = false; });
        }

        document.getElementById('inputNovoComentario').addEventListener('keypress', function (e) { if (e.key === 'Enter') enviarComentario(); });
        function sairDoApp() { fetch('api/logout.php').then(res => res.json()).then(() => { localStorage.clear(); window.location.href = 'login.php'; }).catch(() => { localStorage.clear(); window.location.href = 'login.php'; }); }

        carregarFeed();
    </script>
    <script>if ('serviceWorker' in navigator) { window.addEventListener('load', () => { navigator.serviceWorker.register('sw.js'); }); }</script>
</body>

</html>