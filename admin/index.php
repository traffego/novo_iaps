<?php
// admin/index.php — Página de Login do Painel Administrativo
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/config.php';
require_once ROOT_PATH . '/src/database.php';
require_once ROOT_PATH . '/src/helpers.php';
require_once ROOT_PATH . '/src/csrf.php';
require_once ROOT_PATH . '/src/auth.php';

// Se já está logado, vai pro dashboard
if (auth_check()) {
    redirect('/admin/dashboard.php');
}

$erro = flash('error');
$sucesso = flash('success');

// Processa o POST de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();

    $usuario = trim($_POST['usuario'] ?? '');
    $senha   = $_POST['senha'] ?? '';

    if (empty($usuario) || empty($senha)) {
        flash('error', 'Preencha o usuário e a senha.');
        redirect('/admin/index.php');
    }

    if (auth_login($usuario, $senha)) {
        redirect('/admin/dashboard.php');
    } else {
        flash('error', 'Credenciais inválidas ou conta bloqueada temporariamente.');
        redirect('/admin/index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin — Instituto Atleta Para Sempre</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
    <style>
        /* Reset e layout de login standalone */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #0d1117;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .login-wrapper {
            width: 100%;
            max-width: 420px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-logo img {
            height: 64px;
            width: auto;
        }
        .login-logo-text {
            display: block;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 600;
            margin-top: 0.75rem;
            letter-spacing: 0.02em;
        }
        .login-logo-sub {
            display: block;
            color: #8b949e;
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        .login-card {
            background: #161b22;
            border: 1px solid #30363d;
            border-radius: 12px;
            padding: 2rem;
        }
        .login-card h1 {
            color: #e6edf3;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1.25rem;
            font-size: 0.9rem;
        }
        .alert-danger {
            background: rgba(248,81,73,0.1);
            border: 1px solid rgba(248,81,73,0.4);
            color: #f85149;
        }
        .alert-success {
            background: rgba(63,185,80,0.1);
            border: 1px solid rgba(63,185,80,0.4);
            color: #3fb950;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            color: #e6edf3;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.4rem;
        }
        .form-control {
            width: 100%;
            padding: 0.6rem 0.85rem;
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 6px;
            color: #e6edf3;
            font-size: 0.95rem;
            transition: border-color 0.15s;
            outline: none;
        }
        .form-control:focus {
            border-color: #1f6feb;
            box-shadow: 0 0 0 3px rgba(31,111,235,0.3);
        }
        .btn-login {
            display: block;
            width: 100%;
            padding: 0.7rem 1rem;
            background: #1f6feb;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: background 0.15s;
        }
        .btn-login:hover {
            background: #388bfd;
        }
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #8b949e;
            font-size: 0.8rem;
        }
        .login-footer a {
            color: #58a6ff;
            text-decoration: none;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- Logo / Marca -->
        <div class="login-logo">
            <?php if (file_exists(ROOT_PATH . '/assets/img/logo.png')): ?>
                <img src="/assets/img/logo.png" alt="Instituto Atleta Para Sempre">
            <?php else: ?>
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#1f6feb" stroke-width="1.5">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            <?php endif; ?>
            <span class="login-logo-text">Instituto Atleta Para Sempre</span>
            <span class="login-logo-sub">Painel Administrativo</span>
        </div>

        <!-- Card de Login -->
        <div class="login-card">
            <h1>Entrar no Sistema</h1>

            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= e($erro) ?></div>
            <?php endif; ?>

            <?php if ($sucesso): ?>
                <div class="alert alert-success"><?= e($sucesso) ?></div>
            <?php endif; ?>

            <form method="POST" action="/admin/index.php" novalidate>
                <?= csrf_field() ?>

                <div class="form-group">
                    <label for="usuario">Usuário ou E-mail</label>
                    <input
                        type="text"
                        id="usuario"
                        name="usuario"
                        class="form-control"
                        value="<?= e($_POST['usuario'] ?? '') ?>"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="senha">Senha</label>
                    <input
                        type="password"
                        id="senha"
                        name="senha"
                        class="form-control"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn-login">Entrar</button>
            </form>
        </div>

        <div class="login-footer">
            <a href="/">&larr; Voltar ao site</a>
            &nbsp;|&nbsp;
            &copy; <?= date('Y') ?> Instituto Atleta Para Sempre
        </div>
    </div>
</body>
</html>
