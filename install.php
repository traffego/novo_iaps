<?php
/**
 * install.php — Instalador do sistema IAPS
 *
 * ⚠️  APAGUE ESTE ARQUIVO APÓS A INSTALAÇÃO!
 *
 * Executa em ordem:
 *   1. migrations/001_schema.sql  (criação das tabelas)
 *   2. migrations/002_seed_domain.sql (dados iniciais)
 */

// ─── Segurança: bloquear em produção com token ───────────────────────────────
define('INSTALL_TOKEN', 'iaps2025');   // ← troque se quiser
$token = $_GET['token'] ?? '';
if ($token !== INSTALL_TOKEN) {
    http_response_code(403);
    die('<h2>Acesso negado.</h2><p>Use: <code>install.php?token=' . INSTALL_TOKEN . '</code></p>');
}

// ─── Carregar configuração ────────────────────────────────────────────────────
$env_file = __DIR__ . '/.env';
if (!file_exists($env_file)) {
    die('<h2>❌ Arquivo .env não encontrado!</h2><p>Crie o arquivo .env baseado no .env.example antes de continuar.</p>');
}

// Parser simples de .env
foreach (file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
    if (str_starts_with(trim($linha), '#') || !str_contains($linha, '=')) continue;
    [$chave, $valor] = explode('=', $linha, 2);
    putenv(trim($chave) . '=' . trim($valor));
}

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$name = getenv('DB_NAME') ?: '';
$user = getenv('DB_USER') ?: '';
$pass = getenv('DB_PASS') ?: '';

// ─── Migrations a executar (em ordem) ────────────────────────────────────────
$migrations = [
    '001_schema.sql'      => 'Criação das tabelas',
    '002_seed_domain.sql' => 'Dados iniciais (estados, funções, organização, admin)',
];

// ─── Conectar ao banco ────────────────────────────────────────────────────────
try {
    $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('<h2>❌ Erro de conexão com o banco</h2><pre>' . htmlspecialchars($e->getMessage()) . '</pre>');
}

// ─── Função para executar arquivo SQL ────────────────────────────────────────
function executar_sql(PDO $pdo, string $arquivo): array {
    $log     = [];
    $caminho = __DIR__ . '/migrations/' . $arquivo;

    if (!file_exists($caminho)) {
        return [['ok' => false, 'msg' => "Arquivo não encontrado: {$arquivo}"]];
    }

    $sql_raw = file_get_contents($caminho);

    // Remover comentários de linha dupla (--)
    $sql_raw = preg_replace('/^\s*--.*$/m', '', $sql_raw);

    // Dividir por ; (statements individuais)
    $statements = array_filter(
        array_map('trim', explode(';', $sql_raw)),
        fn($s) => !empty($s)
    );

    foreach ($statements as $stmt) {
        try {
            $pdo->exec($stmt);
            // Pegar primeira linha para log
            $primeira = strtok($stmt, "\n");
            $log[] = ['ok' => true, 'msg' => htmlspecialchars(substr($primeira, 0, 100))];
        } catch (PDOException $e) {
            // Ignorar erros de "tabela já existe" (1050) e "duplicate entry" (1062)
            $codigo = (int)$e->getCode();
            if (in_array($codigo, [1050, 1060, 1061, 1062, 1068])) {
                $primeira = strtok($stmt, "\n");
                $log[] = ['ok' => null, 'msg' => '(já existe) ' . htmlspecialchars(substr($primeira, 0, 80))];
            } else {
                $log[] = ['ok' => false, 'msg' => htmlspecialchars($e->getMessage())];
            }
        }
    }

    return $log;
}

// ─── Executar migrations ──────────────────────────────────────────────────────
$resultados = [];
foreach ($migrations as $arquivo => $descricao) {
    $resultados[$arquivo] = [
        'descricao' => $descricao,
        'log'       => executar_sql($pdo, $arquivo),
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IAPS — Instalador</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0 }
        body { font-family: system-ui, sans-serif; background: #0f172a; color: #e2e8f0; padding: 2rem }
        h1 { color: #10b981; margin-bottom: 2rem; font-size: 1.5rem }
        .migration { background: #1e293b; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; border: 1px solid #334155 }
        .migration h2 { font-size: 1rem; color: #94a3b8; margin-bottom: .25rem }
        .migration h3 { font-size: .8rem; color: #64748b; font-weight: normal; margin-bottom: 1rem }
        .log-item { padding: .3rem .6rem; border-radius: 6px; font-size: .8rem; font-family: monospace; margin-bottom: .25rem }
        .ok   { background: #052e16; color: #86efac }
        .skip { background: #1c1917; color: #78716c }
        .err  { background: #450a0a; color: #fca5a5 }
        .summary { display: flex; gap: 1rem; margin-top: 1rem; font-size: .85rem }
        .badge { padding: .2rem .6rem; border-radius: 99px; font-weight: 600 }
        .badge-ok   { background: #14532d; color: #86efac }
        .badge-skip { background: #292524; color: #78716c }
        .badge-err  { background: #7f1d1d; color: #fca5a5 }
        .warn { background: #713f12; border: 1px solid #d97706; border-radius: 10px; padding: 1rem 1.5rem; margin-top: 2rem; color: #fde68a }
        .warn strong { display: block; margin-bottom: .5rem; font-size: 1rem }
        code { background: #0f172a; padding: .1rem .4rem; border-radius: 4px }
    </style>
</head>
<body>

<h1>⚙️ IAPS — Instalador do Banco de Dados</h1>
<p style="color:#64748b;margin-bottom:2rem">Banco: <strong style="color:#e2e8f0"><?= htmlspecialchars($name) ?></strong> em <strong style="color:#e2e8f0"><?= htmlspecialchars($host) ?></strong></p>

<?php foreach ($resultados as $arquivo => $dados):
    $total_ok   = count(array_filter($dados['log'], fn($l) => $l['ok'] === true));
    $total_skip = count(array_filter($dados['log'], fn($l) => $l['ok'] === null));
    $total_err  = count(array_filter($dados['log'], fn($l) => $l['ok'] === false));
?>
<div class="migration">
    <h2><?= htmlspecialchars($arquivo) ?></h2>
    <h3><?= htmlspecialchars($dados['descricao']) ?></h3>

    <?php foreach ($dados['log'] as $item):
        $cls = match($item['ok']) { true => 'ok', null => 'skip', false => 'err' };
        $ico = match($item['ok']) { true => '✓', null => '~', false => '✗' };
    ?>
    <div class="log-item <?= $cls ?>"><?= $ico ?> <?= $item['msg'] ?></div>
    <?php endforeach ?>

    <div class="summary">
        <span class="badge badge-ok"><?= $total_ok ?> executados</span>
        <span class="badge badge-skip"><?= $total_skip ?> já existiam</span>
        <?php if ($total_err): ?>
        <span class="badge badge-err"><?= $total_err ?> erros</span>
        <?php endif ?>
    </div>
</div>
<?php endforeach ?>

<div class="warn">
    <strong>⚠️  Instalação concluída — APAGUE ESTE ARQUIVO AGORA!</strong>
    Delete <code>install.php</code> do servidor via FTP ou cPanel File Manager.<br>
    Manter este arquivo no ar é um risco de segurança.<br><br>
    Acesse o painel em: <a href="/admin/" style="color:#fbbf24">/admin/</a> — Usuário: <code>admin</code> / Senha: <code>admin123</code><br>
    <strong>Troque a senha imediatamente após o primeiro login.</strong>
</div>

</body>
</html>
