<?php
// admin/dashboard.php — Painel principal com estatísticas e atalhos
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';

auth_require();

$page_title = 'Admin — Dashboard';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '']
];

// ── Estatísticas ──────────────────────────────────────────────────────────────
$total_projetos   = db_count('SELECT COUNT(*) FROM tab_projetos WHERE ativo = 1');
$total_curriculos = db_count('SELECT COUNT(*) FROM tab_curriculos');
$total_fornecedores = db_count('SELECT COUNT(*) FROM tab_fornecedores');
$total_noticias   = db_count('SELECT COUNT(*) FROM tab_noticias');

// ── Últimos 10 currículos ─────────────────────────────────────────────────────
$ultimos_curriculos = db_fetch_all(
    'SELECT c.id, c.nome, c.telefone, f.funcao, p.nome_projeto, c.data_cadastro
     FROM tab_curriculos c
     LEFT JOIN tab_curriculos_funcao f ON c.id_funcao = f.id
     LEFT JOIN tab_projetos p ON c.id_projeto = p.id
     ORDER BY c.data_cadastro DESC
     LIMIT 10'
);

// ── Últimos 10 acessos ────────────────────────────────────────────────────────
$ultimos_acessos = db_fetch_all(
    'SELECT r.data_hora, r.ip, u.nome AS usuario
     FROM tab_login_registro r
     LEFT JOIN tab_usuario u ON r.cod_usuario = u.cod_usuario
     ORDER BY r.data_hora DESC
     LIMIT 10'
);

// ── Usuário logado ────────────────────────────────────────────────────────────
$usuario_logado = auth_user();

ob_start();
?>
<!-- Cards de Estatísticas -->
<div class="page-header mb-6">
    <h2 class="page-title">Dashboard</h2>
    <p class="text-muted">Bem-vindo, <?= e($usuario_logado['nome'] ?? 'Administrador') ?>. Aqui está o resumo do sistema.</p>
</div>

<div class="stats-grid mb-8" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">

    <!-- Projetos Ativos -->
    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(31,111,235,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1f6feb" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            </svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1;"><?= $total_projetos ?></div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">Projetos Ativos</div>
        </div>
    </div>

    <!-- Currículos -->
    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(63,185,80,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3fb950" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1;"><?= $total_curriculos ?></div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">Currículos Recebidos</div>
        </div>
    </div>

    <!-- Fornecedores -->
    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(210,153,34,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d29922" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
            </svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1;"><?= $total_fornecedores ?></div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">Fornecedores</div>
        </div>
    </div>

    <!-- Notícias -->
    <div class="stat-card" style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; padding: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(248,81,73,0.15); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f85149" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
        </div>
        <div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary); line-height: 1;"><?= $total_noticias ?></div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.2rem;">Notícias</div>
        </div>
    </div>

</div>

<!-- Ações Rápidas -->
<div class="mb-8">
    <h3 style="color: var(--text-primary); font-size: 1rem; font-weight: 600; margin-bottom: 1rem;">Ações Rápidas</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
        <a href="/admin/projetos/criar.php" class="btn btn-primary">+ Novo Projeto</a>
        <a href="/admin/noticias/criar.php" class="btn btn-primary">+ Nova Notícia</a>
        <a href="/admin/curriculos/listar.php" class="btn btn-secondary">Ver Currículos</a>
        <a href="/admin/transparencia/financeiro.php" class="btn btn-secondary">Upload Financeiro</a>
        <a href="/admin/configuracoes/organizacao.php" class="btn btn-secondary">Configurações</a>
    </div>
</div>

<!-- Grid de tabelas -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

    <!-- Últimos Currículos -->
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 class="card-title">Últimos Currículos</h3>
            <a href="/admin/curriculos/listar.php" class="btn btn-sm btn-secondary">Ver todos</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimos_curriculos)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Nenhum currículo encontrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimos_curriculos as $curr): ?>
                            <tr>
                                <td><?= e($curr['nome']) ?></td>
                                <td><?= e($curr['funcao'] ?? '—') ?></td>
                                <td style="white-space: nowrap; font-size: 0.8rem;"><?= e(format_date($curr['data_cadastro'] ?? '', 'd/m/Y')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Últimos Acessos -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Últimos Acessos ao Sistema</h3>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuário</th>
                        <th>IP</th>
                        <th>Data/Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ultimos_acessos)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Nenhum acesso registrado.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ultimos_acessos as $acesso): ?>
                            <tr>
                                <td><?= e($acesso['usuario'] ?? '—') ?></td>
                                <td style="font-family: monospace; font-size: 0.85rem;"><?= e($acesso['ip']) ?></td>
                                <td style="white-space: nowrap; font-size: 0.8rem;"><?= e(format_date($acesso['data_hora'] ?? '', 'd/m/Y H:i')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
