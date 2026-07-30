<?php
/**
 * Admin: Visualizar Currículo
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/auth.php';
auth_require();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    flash('error', 'Currículo não especificado.');
    redirect(base_url('/admin/curriculos/listar.php'));
}

$curriculo = db_fetch(
    "SELECT c.*, f.funcao, p.nome_projeto, est.nome AS nome_estado, c.cidade AS nome_cidade
     FROM tab_curriculos c
     LEFT JOIN tab_curriculos_funcao f ON c.id_funcao = f.id
     LEFT JOIN tab_projetos p ON c.id_projeto = p.id
     LEFT JOIN estado est ON c.estado = est.idUf
     WHERE c.id = ?",
    [$id]
);

if (!$curriculo) {
    flash('error', 'Currículo não encontrado.');
    redirect(base_url('/admin/curriculos/listar.php'));
}

$page_title = 'Admin — Detalhes do Currículo';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Currículos', 'url' => base_url('/admin/curriculos/listar.php')],
    ['label' => 'Detalhes: ' . truncate($curriculo['nome'], 25)],
];

ob_start();
?>
<div class="page-section-header">
    <h2><?= e($curriculo['nome']) ?></h2>
    <a href="<?= base_url('/admin/curriculos/listar.php') ?>" class="btn btn-outline btn-sm">← Voltar para listagem</a>
</div>

<div class="admin-forms-grid" style="grid-template-columns: 1fr 1fr;">
    <!-- Dados Pessoais -->
    <div class="admin-card">
        <h3 class="admin-card-title">Dados Pessoais</h3>
        <table class="detail-table" style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted); width: 35%;">E-mail</td>
                <td style="padding: 0.75rem 0.5rem;"><?= e($curriculo['e_mail'] ?: 'Não informado') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Telefone Principal</td>
                <td style="padding: 0.75rem 0.5rem;"><?= e($curriculo['telefone_1'] ?: 'Não informado') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Telefone Recado</td>
                <td style="padding: 0.75rem 0.5rem;"><?= e($curriculo['telefone_2'] ?: 'Não informado') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Sexo</td>
                <td style="padding: 0.75rem 0.5rem;"><?= e($curriculo['sexo'] ?: 'Não informado') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Data Nascimento</td>
                <td style="padding: 0.75rem 0.5rem;"><?= e($curriculo['data_nascimento'] ?: 'Não informado') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Estado Civil</td>
                <td style="padding: 0.75rem 0.5rem;"><?= e($curriculo['estado_civil'] ?: 'Não informado') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Endereço</td>
                <td style="padding: 0.75rem 0.5rem;">
                    <?= e($curriculo['endereco'] ?: '') ?><br>
                    <?= e($curriculo['bairro'] ?: '') ?> - <?= e($curriculo['cep'] ?: '') ?><br>
                    <?= e($curriculo['nome_cidade'] ?: ($curriculo['cidade'] ?: '')) ?> / <?= e($curriculo['estado'] ?: '') ?>
                </td>
            </tr>
        </table>
    </div>

    <!-- Vaga & Formação -->
    <div class="admin-card">
        <h3 class="admin-card-title">Opção de Vaga & Formação</h3>
        <table class="detail-table" style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted); width: 35%;">Projeto</td>
                <td style="padding: 0.75rem 0.5rem;"><strong><?= e($curriculo['nome_projeto'] ?: 'Geral') ?></strong></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Função / Cargo</td>
                <td style="padding: 0.75rem 0.5rem;"><span class="badge badge-info"><?= e($curriculo['funcao'] ?? 'Geral') ?></span></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Escolaridade</td>
                <td style="padding: 0.75rem 0.5rem;"><?= e($curriculo['escolaridade'] ?: 'Não informada') ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Fala outro idioma?</td>
                <td style="padding: 0.75rem 0.5rem;">
                    <?= $curriculo['idioma_sim_nao'] ? 'Sim' : 'Não' ?>
                    <?php if (!empty($curriculo['idioma_quais'])): ?>
                        (<?= e($curriculo['idioma_quais']) ?>)
                    <?php endif; ?>
                </td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Informática?</td>
                <td style="padding: 0.75rem 0.5rem;"><?= $curriculo['informatica_sim_nao'] ? 'Sim' : 'Não' ?></td>
            </tr>
            <tr style="border-bottom: 1px solid var(--adm-border); padding: 0.5rem 0;">
                <td style="padding: 0.75rem 0.5rem; font-weight: 600; color: var(--text-muted);">Arquivo PDF</td>
                <td style="padding: 0.75rem 0.5rem;">
                    <?php $arq = !empty($curriculo['arquivo_curriculo']) ? $curriculo['arquivo_curriculo'] : ($curriculo['id'] . '.pdf'); ?>
                    <a href="/uploads/curriculos/<?= e($arq) ?>" target="_blank" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 0.4rem;">
                        📄 Visualizar Currículo PDF
                    </a>
                </td>
            </tr>
        </table>
    </div>

    <!-- Experiências Anteriores -->
    <div class="admin-card" style="grid-column: span 2;">
        <h3 class="admin-card-title">Histórico e Experiências Profissionais</h3>
        
        <?php if ($curriculo['experiencia_sim_nao']): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                    <?php if (!empty($curriculo["empresa_{$i}"])): ?>
                        <div style="padding: 1rem; border: 1px solid var(--adm-border); border-radius: var(--radius-md); background: rgba(255, 255, 255, 0.02);">
                            <h4 style="margin-top: 0; font-size: 0.95rem; font-weight: 700; color: var(--color-primary);"><?= e($curriculo["empresa_{$i}"]) ?></h4>
                            <p style="margin: 0.25rem 0; font-size: 0.85rem;"><span style="color:var(--text-muted);">Cargo:</span> <strong><?= e($curriculo["empresa_{$i}_funcao"] ?: '—') ?></strong></p>
                            <p style="margin: 0.25rem 0; font-size: 0.85rem;"><span style="color:var(--text-muted);">Período:</span> <?= e($curriculo["empresa_{$i}_periodo"] ?: '—') ?></p>
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>
        <?php else: ?>
            <p class="text-muted" style="margin-bottom: 1.5rem;">Candidato declarou não possuir experiências anteriores.</p>
        <?php endif; ?>

        <?php if (!empty($curriculo['cursos_relevantes'])): ?>
            <div class="mb-4">
                <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-muted);">Cursos Relevantes</h4>
                <div style="padding: 0.75rem 1rem; border: 1px solid var(--adm-border); border-radius: var(--radius-md); font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap; background: rgba(255, 255, 255, 0.01);"><?= e($curriculo['cursos_relevantes']) ?></div>
            </div>
        <?php endif; ?>

        <?php if (!empty($curriculo['experiencia_profissional'])): ?>
            <div>
                <h4 style="font-size: 0.95rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-muted);">Resumo da Experiência Profissional</h4>
                <div style="padding: 0.75rem 1rem; border: 1px solid var(--adm-border); border-radius: var(--radius-md); font-size: 0.9rem; line-height: 1.5; white-space: pre-wrap; background: rgba(255, 255, 255, 0.01);"><?= e($curriculo['experiencia_profissional']) ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
