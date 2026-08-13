<!-- =========================================
     MODAL GLOBAL: MISSÃO DA SEMANA
     ========================================= -->
<div class="modal fade" id="modalMissao" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-success" style="border-width: 3px;">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">🎯 Missão da Semana</h5><button type="button"
                    class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div id="missaoLoading" class="text-muted">Carregando missão...</div>
                <div id="missaoConteudo" class="d-none">
                    <h4 id="missaoTitulo" class="fw-bold text-success mb-2"></h4>
                    <p id="missaoDesc" class="text-muted mb-4"></p>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1"><small
                                class="fw-bold">Progresso</small><small id="missaoStatus"
                                class="fw-bold text-success">0/0</small></div>
                        <div class="progress" style="height: 25px;">
                            <div id="missaoBarra"
                                class="progress-bar bg-success progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%;"></div>
                        </div>
                    </div>
                    <div class="bg-light p-3 rounded-3 mb-3 border"><span class="fw-bold">Recompensa:</span> <span
                            class="badge bg-warning text-dark fs-6" id="missaoXP"></span></div><small
                        class="text-muted d-block mb-3">Encerra no domingo: <b id="missaoPrazo"></b></small>
                    <button id="btnResgatarMissao" class="btn btn-success fw-bold w-100 py-2 d-none"
                        onclick="resgatarMissao()">🎁 Resgatar Recompensa</button>
                    <button id="btnMissaoCompleta" class="btn btn-secondary fw-bold w-100 py-2 d-none" disabled>✅ Já Resgatada</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function abrirMissao() { 
        const idUsuario = localStorage.getItem('bando_id');
        if (!idUsuario) return;

        new bootstrap.Modal(document.getElementById('modalMissao')).show(); 
        document.getElementById('missaoLoading').classList.remove('d-none'); 
        document.getElementById('missaoConteudo').classList.add('d-none'); 
        
        fetch('api/missao.php?usuario_id=' + idUsuario)
            .then(r => r.json())
            .then(data => { 
                if (data.sucesso) { 
                    document.getElementById('missaoLoading').classList.add('d-none'); 
                    document.getElementById('missaoConteudo').classList.remove('d-none'); 
                    document.getElementById('missaoTitulo').innerText = data.missao.titulo; 
                    document.getElementById('missaoDesc').innerText = data.missao.desc; 
                    document.getElementById('missaoXP').innerText = '+ ' + data.missao.xp + ' XP'; 
                    document.getElementById('missaoPrazo').innerText = data.prazo; 
                    const pct = (data.progresso / data.missao.meta) * 100; 
                    document.getElementById('missaoBarra').style.width = pct + '%'; 
                    document.getElementById('missaoStatus').innerText = data.progresso + '/' + data.missao.meta; 
                    
                    const btnResgatar = document.getElementById('btnResgatarMissao'); 
                    const btnCompleta = document.getElementById('btnMissaoCompleta'); 
                    btnResgatar.classList.add('d-none'); 
                    btnCompleta.classList.add('d-none'); 
                    
                    if (data.ja_resgatou) { 
                        btnCompleta.classList.remove('d-none'); 
                    } else if (data.concluida) { 
                        btnResgatar.classList.remove('d-none'); 
                    } 
                } 
            }); 
    }

    function resgatarMissao() { 
        const idUsuario = localStorage.getItem('bando_id');
        const formData = new FormData(); 
        formData.append('usuario_id', idUsuario); 
        formData.append('acao', 'resgatar'); 
        
        fetch('api/missao.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => { 
                if (data.sucesso) { 
                    alert(data.mensagem); 
                    abrirMissao(); 
                } else { 
                    alert('Erro: ' + data.erro); 
                } 
            }); 
    }
</script>