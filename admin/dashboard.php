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
$total_projetos     = 0;
$total_curriculos   = 0;
$total_fornecedores = 0;
$total_noticias     = 0;

try { $total_projetos     = db_count('SELECT COUNT(*) FROM tab_projetos WHERE ativo = 1'); } catch (Throwable $e) {}
try { $total_curriculos   = db_count('SELECT COUNT(*) FROM tab_curriculos'); } catch (Throwable $e) {}
try { $total_fornecedores = db_count('SELECT COUNT(*) FROM tab_fornecedores'); } catch (Throwable $e) {}
try { $total_noticias     = db_count('SELECT COUNT(*) FROM tab_noticias'); } catch (Throwable $e) {}

// ── Últimos 10 currículos ─────────────────────────────────────────────────────
$ultimos_curriculos = [];
try {
    $ultimos_curriculos = db_fetch_all(
        'SELECT c.id, c.nome, COALESCE(c.telefone_1, "") AS telefone, f.funcao, p.nome_projeto, c.created_at AS data_cadastro
         FROM tab_curriculos c
         LEFT JOIN tab_curriculos_funcao f ON c.id_funcao = f.id
         LEFT JOIN tab_projetos p ON c.id_projeto = p.id
         ORDER BY c.id DESC
         LIMIT 10'
    );
} catch (Throwable $e) {
    try {
        $ultimos_curriculos = db_fetch_all('SELECT id, nome, created_at AS data_cadastro FROM tab_curriculos ORDER BY id DESC LIMIT 10');
    } catch (Throwable $e2) {
        $ultimos_curriculos = [];
    }
}

// ── Últimos 10 acessos ────────────────────────────────────────────────────────
$ultimos_acessos = [];
try {
    $ultimos_acessos = db_fetch_all(
        'SELECT r.created_at AS data_hora, r.ip, COALESCE(l.nome_usuario, l.usuario, "Administrador") AS usuario
         FROM tab_login_registro r
         LEFT JOIN tab_login l ON r.usuario = l.id
         ORDER BY r.id DESC
         LIMIT 10'
    );
} catch (Throwable $e) {
    $ultimos_acessos = [];
}

// ── Usuário logado ────────────────────────────────────────────────────────────
$usuario_logado = auth_user();

ob_start();
?>
<!-- Header da Página -->
<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Visão geral e resumo das atividades do Instituto Atleta Para Sempre</p>
    </div>
</div>

<!-- Grid de Estatísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--adm-accent-glow); color: var(--adm-accent-light);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"></path></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Projetos Ativos</div>
            <div class="stat-value" style="color: var(--adm-accent-light);"><?= $total_projetos ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: var(--adm-success-bg); color: var(--adm-success);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Currículos</div>
            <div class="stat-value" style="color: var(--adm-success);"><?= $total_curriculos ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: var(--adm-warning-bg); color: var(--adm-warning);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Fornecedores</div>
            <div class="stat-value" style="color: var(--adm-warning);"><?= $total_fornecedores ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: var(--adm-purple-bg); color: var(--adm-purple);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Notícias</div>
            <div class="stat-value" style="color: var(--adm-purple);"><?= $total_noticias ?></div>
        </div>
    </div>
</div>

<!-- Tabela de Últimos Currículos -->
<div class="card mb-6">
    <div class="card-header">
        <h3 class="card-title">Últimos Currículos Cadastrados</h3>
        <a href="/admin/curriculos/listar.php" class="btn btn-outline btn-sm">
            <span>Ver Todos</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
        </a>
    </div>

    <?php if (empty($ultimos_curriculos)): ?>
        <div class="card-body text-center text-muted py-8">
            Nenhum currículo cadastrado até o momento.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Função</th>
                        <th>Projeto</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimos_curriculos as $c): ?>
                        <tr>
                            <td><strong><?= e($c['nome']) ?></strong></td>
                            <td><?= e($c['telefone'] ?: '—') ?></td>
                            <td><span class="badge badge-info"><?= e($c['funcao'] ?: 'Geral') ?></span></td>
                            <td><?= e(truncate($c['nome_projeto'] ?: '—', 35)) ?></td>
                            <td><span class="text-muted text-sm"><?= $c['data_cadastro'] ? format_date(substr($c['data_cadastro'], 0, 10)) : '—' ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
