<?php
require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/database.php';

$sql = file_get_contents(__DIR__ . '/migrations/007_seed_homologacoes_992680_992681_992669.sql');

$pdo = db_connect();
$pdo->exec($sql);

echo "Migration executada com sucesso.\n";

$docs = db_fetch_all('SELECT id, id_projeto, id_grupo_documento, nome_documento, arquivo FROM tab_projetos_documentos WHERE id IN (138,139,140)');
foreach ($docs as $d) {
    echo "ID={$d['id']} | Projeto={$d['id_projeto']} | Grupo={$d['id_grupo_documento']} | Nome={$d['nome_documento']} | Arquivo={$d['arquivo']}\n";
}
