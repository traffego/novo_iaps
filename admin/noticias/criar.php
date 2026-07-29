<?php
/**
 * Admin: Criar Notícia
 */
require_once dirname(dirname(__DIR__)) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';
require_once ROOT_PATH . '/src/upload.php';
auth_require();

$page_title = 'Admin — Nova Notícia';
$breadcrumb = [
    ['label' => 'Dashboard',  'url' => base_url('/admin/dashboard.php')],
    ['label' => 'Notícias',   'url' => base_url('/admin/noticias/listar.php')],
    ['label' => 'Nova Notícia'],
];

$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $dados = [
        'manchete'     => trim($_POST['manchete'] ?? ''),
        'resumo'       => trim($_POST['resumo'] ?? ''),
        'noticia'      => $_POST['noticia'] ?? '',
        'data_noticia' => $_POST['data_noticia'] ?: date('Y-m-d'),
        'n_visitas'    => 0,
    ];

    if (empty($dados['manchete'])) $erros[] = 'A manchete é obrigatória.';

    if (empty($erros)) {
        // Upload imagem superior
        if (!empty($_FILES['imagem_inicio']['name'])) {
            $up = upload_image($_FILES['imagem_inicio'], UPLOAD_PATH . '/noticias');
            if ($up['success']) $dados['imagem_inicio'] = $up['filename'];
            else $erros[] = 'Imagem superior: ' . $up['error'];
        }
        // Upload imagem inferior
        if (!empty($_FILES['imagem_final']['name'])) {
            $up = upload_image($_FILES['imagem_final'], UPLOAD_PATH . '/noticias');
            if ($up['success']) $dados['imagem_final'] = $up['filename'];
            else $erros[] = 'Imagem inferior: ' . $up['error'];
        }
    }

    if (empty($erros)) {
        db_insert('tab_noticias', $dados);
        flash('success', 'Notícia criada com sucesso!');
        redirect(base_url('/admin/noticias/listar.php'));
    }
}

ob_start();
?>
<?php if (!empty($erros)): ?>
<div class="alert alert-error"><?php foreach ($erros as $e) echo '<p>' . htmlspecialchars($e, ENT_QUOTES) . '</p>'; ?></div>
<?php endif; ?>

<div class="admin-card">
    <form method="POST" enctype="multipart/form-data" id="form-noticia">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="manchete" class="form-label">Manchete <span class="required">*</span></label>
            <input type="text" id="manchete" name="manchete" class="form-input" required value="<?= e(old('manchete')) ?>" placeholder="Título da notícia">
        </div>
        <div class="form-group">
            <label for="resumo" class="form-label">Resumo</label>
            <textarea id="resumo" name="resumo" class="form-textarea" rows="2" placeholder="Resumo curto para a listagem..."><?= e(old('resumo')) ?></textarea>
        </div>
        <div class="form-group">
            <label for="data_noticia" class="form-label">Data</label>
            <input type="date" id="data_noticia" name="data_noticia" class="form-input" value="<?= e(old('data_noticia', date('Y-m-d'))) ?>">
        </div>
        <div class="form-group">
            <label for="noticia" class="form-label">Conteúdo completo</label>
            <textarea id="noticia" name="noticia" class="form-textarea tinymce-editor" rows="15"><?= e(old('noticia')) ?></textarea>
        </div>
        <div class="form-grid-2">
            <div class="form-group">
                <label for="imagem_inicio" class="form-label">Imagem superior</label>
                <input type="file" id="imagem_inicio" name="imagem_inicio" class="form-input" accept="image/*" data-preview="#preview-inicio">
                <img id="preview-inicio" src="" alt="Preview" style="display:none;max-width:200px;margin-top:.5rem;border-radius:8px">
            </div>
            <div class="form-group">
                <label for="imagem_final" class="form-label">Imagem inferior</label>
                <input type="file" id="imagem_final" name="imagem_final" class="form-input" accept="image/*" data-preview="#preview-final">
                <img id="preview-final" src="" alt="Preview" style="display:none;max-width:200px;margin-top:.5rem;border-radius:8px">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?= base_url('/admin/noticias/listar.php') ?>" class="btn btn-ghost">Cancelar</a>
            <button type="submit" class="btn btn-primary">Publicar Notícia</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/templates/admin/layout.php';
