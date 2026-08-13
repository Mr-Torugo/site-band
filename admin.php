<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Painel Admin - Bando Map</title>

    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#dc3545">
    <link rel="apple-touch-icon" href="icon-192.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
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

        .admin-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        @media (min-width: 768px) {
            body {
                padding-bottom: 30px;
                padding-top: 80px;
            }

            .admin-container {
                padding: 30px;
                margin-top: 20px;
            }
        }

        .table-responsive {
            margin-top: 20px;
            border-radius: 8px;
        }

        .botoes-acao {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
    </style>
</head>

<body>
    <?php 
    // 1. Avisa para o menu que estamos na tela do Radar
    $menuAtivo = 'mais'; 
    
    // 2. Importa todo o código do menu
    require './includes/navbar.php'; 
    ?>

    <!-- CONTEÚDO PRINCIPAL -->
    <div class="container admin-container mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h3 class="fw-bold mb-0 text-dark">Gerenciamento de Caçadores</h3>
            <button class="btn btn-primary fw-bold shadow-sm" onclick="carregarUsuarios()"><i
                    class="bi bi-arrow-clockwise me-1"></i> Atualizar Lista</button>
        </div>
        <div class="table-responsive shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th scope="col" class="ps-3">ID</th>
                        <th scope="col">Apelido</th>
                        <th scope="col">Cargo</th>
                        <th scope="col" class="text-end pe-3">Ações</th>
                    </tr>
                </thead>
                <tbody id="tabelaUsuarios">
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Carregando usuários...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const meuId = localStorage.getItem('bando_id');
        const meuApelido = localStorage.getItem('bando_apelido');
        if (!meuId) window.location.href = 'login.html';
        if (document.getElementById('nomeLogado')) document.getElementById('nomeLogado').innerText = meuApelido;
        if (document.getElementById('nomeLogadoMobile')) document.getElementById('nomeLogadoMobile').innerText = meuApelido;

        // Verifica admin
        fetch('api/listar.php?usuario_id=' + meuId).then(r => r.json()).then(data => {
            if (data.sucesso && data.is_admin) {
                document.getElementById('badgeAdmin').classList.remove('d-none');
                document.getElementById('badgeAdminMobile').classList.remove('d-none');
                document.getElementById('btnMenuAdmin').classList.remove('d-none');
                document.getElementById('btnMenuAdminMobile').classList.remove('d-none');
            }
        });

        document.addEventListener("DOMContentLoaded", carregarUsuarios);

        function carregarUsuarios() {
            fetch('api/admin_usuarios.php').then(res => res.json()).then(data => {
                if (data.sucesso) renderizarTabela(data.dados);
                else { alert("Bloqueado: " + data.erro); if (data.erro.includes("negado")) window.location.href = 'index.html'; }
            }).catch(e => document.getElementById('tabelaUsuarios').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-4 fw-bold">Erro ao carregar dados.</td></tr>');
        }

        function renderizarTabela(usuarios) {
            const tbody = document.getElementById('tabelaUsuarios'); tbody.innerHTML = '';
            usuarios.forEach(u => {
                const isMe = (u.id == meuId);
                const badge = u.is_admin == 1 ? '<span class="badge bg-danger"><i class="bi bi-shield-lock-fill me-1"></i>Admin</span>' : '<span class="badge bg-secondary">Caçador</span>';
                let botoes = '<div class="botoes-acao">';
                botoes += `<button class="btn btn-sm btn-info fw-bold text-white" onclick="resetarSenha(${u.id}, '${u.apelido}')"><i class="bi bi-key-fill"></i> <span class="d-none d-md-inline">Senha</span></button>`;
                if (u.is_admin == 1) botoes += `<button class="btn btn-sm btn-warning fw-bold" onclick="executarAcao('rebaixar', ${u.id})" ${isMe ? 'disabled' : ''}><i class="bi bi-arrow-down-circle"></i> <span class="d-none d-md-inline">Rebaixar</span></button>`;
                else botoes += `<button class="btn btn-sm btn-success fw-bold" onclick="executarAcao('promover', ${u.id})"><i class="bi bi-arrow-up-circle"></i> <span class="d-none d-md-inline">Promover</span></button>`;
                botoes += `<button class="btn btn-sm btn-outline-danger fw-bold" onclick="executarAcao('excluir', ${u.id})" ${isMe ? 'disabled' : ''}><i class="bi bi-trash-fill"></i></button></div>`;
                tbody.innerHTML += `<tr><td class="text-muted fw-bold ps-3">#${u.id}</td><td class="fw-bold text-dark fs-6">${u.apelido} ${isMe ? '<br><span class="badge bg-success" style="font-size:0.65rem;">VOCÊ</span>' : ''}</td><td>${badge}</td><td class="pe-3">${botoes}</td></tr>`;
            });
        }

        function executarAcao(acao, alvo_id) {
            if (acao === 'excluir' && !confirm("CUIDADO: Isso vai excluir o caçador permanentemente! Tem certeza?")) return;
            if (acao === 'rebaixar' && !confirm("Deseja remover os poderes de administrador?")) return;
            const formData = new FormData(); formData.append('acao', acao); formData.append('alvo_id', alvo_id);
            fetch('api/admin_usuarios.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => {
                if (data.sucesso) carregarUsuarios(); else alert("Erro: " + data.erro);
            });
        }

        function resetarSenha(id, apelido) {
            const novaSenha = prompt(`Digite a nova senha temporária para ${apelido}:`);
            if (!novaSenha || novaSenha.trim() === '') return;
            const formData = new FormData(); formData.append('acao', 'resetar_senha'); formData.append('alvo_id', id); formData.append('nova_senha', novaSenha);
            fetch('api/admin_usuarios.php', { method: 'POST', body: formData }).then(res => res.json()).then(data => {
                if (data.sucesso) alert(`Feito! Senha de ${apelido}: ${novaSenha}`); else alert("Erro: " + data.erro);
            });
        }

        function sairDoApp() { localStorage.clear(); window.location.href = 'login.php'; }
    </script>
</body>

</html>