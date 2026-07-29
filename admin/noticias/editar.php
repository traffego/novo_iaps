<?php
/**
 * Admin: Editar Notícia
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$id      = (int)($_GET['id'] ?? 0);
$noticia = $id ? db_fetch('SELECT * FROM tab_noticias WHERE id = ?', [$id]) : null;
if (!$noticia) { flash('error', 'Notícia não encontrada.'); redirect(base_url('/admin/noticias/listar.php')); }

$page_title = 'Admin — Editar Notícia';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Notícias',  'url' => base_url('/admin/noticias/listar.php')],
    ['label' => 'Editar'],
];

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = $_POST['action'] ?? 'update';

    if ($action === 'delete_img_inicio') {
        if ($noticia['imagem_inicio']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio']);
        db_update('tab_noticias', ['imagem_inicio' => ''], 'id = ?', [$id]);
        flash('success', 'Imagem superior removida.');
        redirect(base_url('/admin/noticias/editar.php?id=' . $id));
    }
    if ($action === 'delete_img_final') {
        if ($noticia['imagem_final']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_final']);
        db_update('tab_noticias', ['imagem_final' => ''], 'id = ?', [$id]);
        flash('success', 'Imagem inferior removida.');
        redirect(base_url('/admin/noticias/editar.php?id=' . $id));
    }

    // Update normal
    $dados = [
        'manchete'     => trim($_POST['manchete'] ?? ''),
        'resumo'       => trim($_POST['resumo'] ?? ''),
        'noticia'      => $_POST['noticia'] ?? '',
        'data_noticia' => $_POST['data_noticia'] ?: date('Y-m-d'),
    ];

    if (empty($dados['manchete'])) $erros[] = 'A manchete é obrigatória.';

    if (empty($erros)) {
        if (!empty($_FILES['imagem_inicio']['name'])) {
            if ($noticia['imagem_inicio']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio']);
            $up = upload_image($_FILES['imagem_inicio'], UPLOAD_PATH . '/noticias');
            if ($up['success']) $dados['imagem_inicio'] = $up['filename'];
        }
        if (!empty($_FILES['imagem_final']['name'])) {
            if ($noticia['imagem_final']) @unlink(UPLOAD_PATH . '/noticias/' . $noticia['imagem_final']);
            $up = upload_image($_FILES['imagem_final'], UPLOAD_PATH . '/noticias');
            if ($up['success']) $dados['imagem_final'] = $up['filename'];
        }
        db_update('tab_noticias', $dados, 'id = ?', [$id]);
        flash('success', 'Notícia atualizada!');
        redirect(base_url('/admin/noticias/listar.php'));
    }
    $noticia = array_merge($noticia, $dados);
}

ob_start();
?>
<?php if (!empty($erros)): ?>
<div class="alert alert-error"><?php foreach ($erros as $e) echo '<p>' . htmlspecialchars($e, ENT_QUOTES) . '</p>'; ?></div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" enctype="multipart/form-data" id="form-noticia">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="update">

        <div class="form-group">
            <label for="manchete" class="form-label">Manchete <span class="required">*</span></label>
            <input type="text" id="manchete" name="manchete" class="form-input" required value="<?= e($noticia['manchete']) ?>">
        </div>
        <div class="form-group">
            <label for="resumo" class="form-label">Resumo</label>
            <textarea id="resumo" name="resumo" class="form-textarea" rows="2"><?= e($noticia['resumo'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="data_noticia" class="form-label">Data</label>
            <input type="date" id="data_noticia" name="data_noticia" class="form-input" value="<?= e($noticia['data_noticia'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="noticia" class="form-label">Conteúdo completo</label>
            <textarea id="noticia" name="noticia" class="form-textarea tinymce-editor" rows="15"><?= $noticia['noticia'] ?? '' ?></textarea>
        </div>

        <!-- Imagens atuais -->
        <div class="form-grid-2">
            <div class="form-group">
                <label class="form-label">Imagem superior atual</label>
                <?php if (!empty($noticia['imagem_inicio']) && file_exists(UPLOAD_PATH . '/noticias/' . $noticia['imagem_inicio'])): ?>
                <img src="/uploads/noticias/<?= e($noticia['imagem_inicio']) ?>" style="max-width:200px;border-radius:8px;display:block;margin-bottom:.5rem">
                <form method="POST" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete_img_inicio">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Remover esta imagem?">Remover imagem</button>
                </form>
                <?php else: ?>
                <p class="text-muted text-sm">Sem imagem.</p>
                <?php endif; ?>
                <label for="imagem_inicio" class="form-label" style="margin-top:.75rem">Nova imagem superior</label>
                <input type="file" id="imagem_inicio" name="imagem_inicio" class="form-input" accept="image/*" data-preview="#preview-inicio">
                <img id="preview-inicio" src="" alt="" style="display:none;max-width:200px;margin-top:.5rem;border-radius:8px">
            </div>
            <div class="form-group">
                <label class="form-label">Imagem inferior atual</label>
                <?php if (!empty($noticia['imagem_final']) && file_exists(UPLOAD_PATH . '/noticias/' . $noticia['imagem_final'])): ?>
                <img src="/uploads/noticias/<?= e($noticia['imagem_final']) ?>" style="max-width:200px;border-radius:8px;display:block;margin-bottom:.5rem">
                <form method="POST" style="display:inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="delete_img_final">
                    <button type="submit" class="btn btn-danger btn-sm" data-confirm="Remover esta imagem?">Remover imagem</button>
                </form>
                <?php else: ?>
                <p class="text-muted text-sm">Sem imagem.</p>
                <?php endif; ?>
                <label for="imagem_final" class="form-label" style="margin-top:.75rem">Nova imagem inferior</label>
                <input type="file" id="imagem_final" name="imagem_final" class="form-input" accept="image/*" data-preview="#preview-final">
                <img id="preview-final" src="" alt="" style="display:none;max-width:200px;margin-top:.5rem;border-radius:8px">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('/admin/noticias/listar.php') ?>" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
