<?php
/**
 * API: Funções vinculadas a um projeto
 * GET /api/funcoes-projeto.php?projeto_id=X
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . APP_URL);

$projeto_id = (int)($_GET['projeto_id'] ?? 0);

if ($projeto_id <= 0) {
    echo json_encode([]);
    exit;
}

try {
    $funcoes = db_fetch_all(
        'SELECT f.id, f.funcao
         FROM tab_curriculos_funcao f
         INNER JOIN tab_projetos_funcao pf ON f.id = pf.id_funcao
         WHERE pf.id_projeto = ? AND f.ativo = 1
         ORDER BY f.funcao',
        [$projeto_id]
    );
    echo json_encode($funcoes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar funções']);
}
