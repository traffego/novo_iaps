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
<!-- Cards de Estatísticas -->
<div class="page-header mb-6">
    <h2 class="page-title">Dashboard</h2>
    <p class="text-muted" style="font-size:0.9rem;">Visão geral do sistema Instituto Atleta Para Sempre</p>
</div>

<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="card" style="padding: 1.25rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span class="text-muted" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Projetos Ativos</span>
                <h3 style="font-size:1.8rem; font-weight:700; color:var(--color-primary); margin-top:0.25rem;"><?= $total_projetos ?></h3>
            </div>
            <div style="width:42px; height:42px; border-radius:10px; background:var(--color-primary-alpha); color:var(--color-primary); display:flex; align-items:center; justify-content:center;">
                <i data-lucide="trophy" style="width:22px; height:22px;"></i>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span class="text-muted" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Currículos</span>
                <h3 style="font-size:1.8rem; font-weight:700; color:#10b981; margin-top:0.25rem;"><?= $total_curriculos ?></h3>
            </div>
            <div style="width:42px; height:42px; border-radius:10px; background:rgba(16,185,129,0.15); color:#10b981; display:flex; align-items:center; justify-content:center;">
                <i data-lucide="users" style="width:22px; height:22px;"></i>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span class="text-muted" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Fornecedores</span>
                <h3 style="font-size:1.8rem; font-weight:700; color:#f59e0b; margin-top:0.25rem;"><?= $total_fornecedores ?></h3>
            </div>
            <div style="width:42px; height:42px; border-radius:10px; background:rgba(245,158,11,0.15); color:#f59e0b; display:flex; align-items:center; justify-content:center;">
                <i data-lucide="truck" style="width:22px; height:22px;"></i>
            </div>
        </div>
    </div>

    <div class="card" style="padding: 1.25rem;">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span class="text-muted" style="font-size:0.8rem; font-weight:600; text-transform:uppercase;">Notícias</span>
                <h3 style="font-size:1.8rem; font-weight:700; color:#8b5cf6; margin-top:0.25rem;"><?= $total_noticias ?></h3>
            </div>
            <div style="width:42px; height:42px; border-radius:10px; background:rgba(139,92,246,0.15); color:#8b5cf6; display:flex; align-items:center; justify-content:center;">
                <i data-lucide="newspaper" style="width:22px; height:22px;"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Últimos Currículos -->
<div class="card mb-6" style="padding: 1.5rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
        <h3 style="font-size:1.1rem; font-weight:600; color:var(--text-color);">Últimos Currículos Cadastrados</h3>
        <a href="/admin/curriculos/index.php" class="btn btn-outline btn-sm"><i data-lucide="arrow-right"></i> Ver Todos</a>
    </div>

    <?php if (empty($ultimos_curriculos)): ?>
        <p class="text-muted" style="font-size:0.9rem; padding: 1rem 0;">Nenhum currículo cadastrado até o momento.</p>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="data-table" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; border-bottom:1px solid var(--border-color); font-size:0.85rem; color:var(--text-muted);">
                        <th style="padding:0.75rem;">Nome</th>
                        <th style="padding:0.75rem;">Telefone</th>
                        <th style="padding:0.75rem;">Função</th>
                        <th style="padding:0.75rem;">Projeto</th>
                        <th style="padding:0.75rem;">Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimos_curriculos as $c): ?>
                        <tr style="border-bottom:1px solid var(--border-color); font-size:0.9rem;">
                            <td style="padding:0.75rem; font-weight:600;"><?= e($c['nome']) ?></td>
                            <td style="padding:0.75rem;"><?= e($c['telefone'] ?? '-') ?></td>
                            <td style="padding:0.75rem;"><?= e($c['funcao'] ?? '-') ?></td>
                            <td style="padding:0.75rem;"><?= e($c['nome_projeto'] ?? '-') ?></td>
                            <td style="padding:0.75rem; color:var(--text-muted);"><?= format_date($c['data_cadastro'] ?? '') ?></td>
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
