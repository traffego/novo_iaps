<?php
/**
 * API: Cidades por UF
 * GET /api/cidades.php?uf=XX
 */
require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: ' . APP_URL);

$uf = strtoupper(trim($_GET['uf'] ?? ''));

if (empty($uf) || strlen($uf) !== 2 || !ctype_alpha($uf)) {
    echo json_encode([]);
    exit;
}

try {
    $cidades = db_fetch_all(
        'SELECT id, nome FROM cidade WHERE codUf = ? ORDER BY nome',
        [$uf]
    );
    echo json_encode($cidades);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar cidades']);
}
