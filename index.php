<?php
// index.php - Front Controller Principal

require_once __DIR__ . '/src/config.php';
require_once __DIR__ . '/src/database.php';
require_once __DIR__ . '/src/helpers.php';
require_once __DIR__ . '/src/csrf.php';

// Verificação de bloqueio da organização
// Como exigido, verificamos se a organização está liberada.
try {
    // 1 seria o código da organização principal do CMS, adapte conforme a necessidade de multi-inquilino.
    $org = db_fetch("SELECT liberado FROM tab_org WHERE cod_org = 1 LIMIT 1");
    if ($org && (int)$org['liberado'] !== 1) {
        http_response_code(503);
        die("<h1>Serviço Indisponível</h1><p>A organização encontra-se temporariamente bloqueada no sistema.</p>");
    }
} catch (Exception $e) {
    // Ignorar no bootstrap se o banco ainda não estiver populado ou criado.
}

// Analisa a URI requisitada
$request_uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
// Remove o diretório base, caso não esteja na raiz do domínio localmente
$base_dir = parse_url(APP_URL, PHP_URL_PATH);
if ($base_dir && $base_dir !== '/' && str_starts_with($request_uri, $base_dir)) {
    $request_uri = substr($request_uri, strlen($base_dir));
}

if ($request_uri === '' || $request_uri === '/') {
    $request_uri = '/';
}

// Mapa de rotas -> Arquivo
$routes = [
    '/' => 'pages/home.php',
    '/quem-somos' => 'pages/quem-somos.php',
    '/contato' => 'pages/contato.php',
    '/noticias' => 'pages/noticias.php',
    '/editais' => 'pages/editais.php',
    '/trabalhe-conosco' => 'pages/trabalhe-conosco.php',
    '/fornecedores' => 'pages/fornecedores.php',
    '/transparencia/declaracao' => 'pages/transparencia/declaracao.php',
    '/transparencia/dirigentes' => 'pages/transparencia/dirigentes.php',
    '/transparencia/estatuto' => 'pages/transparencia/estatuto.php',
    '/transparencia/financeiro' => 'pages/transparencia/financeiro.php',
    '/transparencia/regulamento' => 'pages/transparencia/regulamento.php',
    '/transparencia/termos' => 'pages/transparencia/termos.php',
    '/transparencia/painel-transferencias' => 'pages/transparencia/painel-transferencias.php'
];

$file_to_include = null;
$route_params = [];

// Roteador Básico com suporte a parâmetros simples, ex: /noticia/{id}
if (isset($routes[$request_uri])) {
    $file_to_include = $routes[$request_uri];
} else {
    // Verifica rotas dinâmicas
    if (preg_match('#^/noticia/(\d+)$#', $request_uri, $matches)) {
        $file_to_include = 'pages/noticia-detalhe.php';
        $route_params['id'] = (int) $matches[1];
        $_GET['id'] = $route_params['id']; // Retro-compatibilidade simples
    }
}

// Cria a pasta pages se não existir pra não quebrar no teste inicial
if (!is_dir(__DIR__ . '/pages')) {
    mkdir(__DIR__ . '/pages');
}

// Verifica se arquivo existe, se não, cai em 404
if ($file_to_include && file_exists(__DIR__ . '/' . $file_to_include)) {
    // Captura conteúdo da página
    ob_start();
    require __DIR__ . '/' . $file_to_include;
    $content = ob_get_clean();
    
    // Se a página definiu usar o layout e não mandou um JSON ou header específico, inclui
    // (Presumimos que arquivos de pages incluam o template base ou nós forçamos aqui se existir)
    $layout_file = __DIR__ . '/templates/layout.php';
    if (file_exists($layout_file) && !isset($no_layout)) {
        require $layout_file;
    } else {
        echo $content;
    }
} else {
    // 404
    http_response_code(404);
    echo "<h1>404 - Página não encontrada</h1>";
    echo "<p>A URL solicitada não existe neste servidor.</p>";
}
