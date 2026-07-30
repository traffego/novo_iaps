<?php
// src/auth.php - Autenticação Standard via Banco de Dados (tab_login)

function auth_login(string $usuario, string $senha): bool {
    $usuario = trim($usuario);
    $senha   = trim($senha);

    if (empty($usuario) || empty($senha)) {
        return false;
    }

    try {
        // Consultar tabela tab_login
        $sql  = 'SELECT * FROM tab_login WHERE usuario = ? LIMIT 1';
        $user = db_fetch($sql, [$usuario]);

        if ($user && !empty($user['senha'])) {
            $senha_db    = $user['senha'];
            $autenticado = false;

            // 1. Verificação padrão bcrypt (password_verify)
            if (password_verify($senha, $senha_db)) {
                $autenticado = true;
            }
            // 2. Verificação de senha legada em texto puro
            elseif ($senha === $senha_db) {
                $autenticado = true;
            }
            // 3. Verificação de MD5 legado
            elseif (md5($senha) === $senha_db) {
                $autenticado = true;
            }

            if ($autenticado) {
                // Registrar sessão
                $_SESSION['user_id']      = (int) $user['id'];
                $_SESSION['user_nome']    = $user['nome_usuario'] ?? $user['usuario'];
                $_SESSION['user_usuario'] = $user['usuario'];
                $_SESSION['user_perfil']  = $user['nivel'] ?? '1';
                $_SESSION['cod_org']      = (int) ($user['cod_org'] ?? 10001);
                $_SESSION['logged_in']    = true;

                return true;
            }
        }
    } catch (Throwable $e) {
        // Erro de banco de dados
    }

    return false;
}

function auth_logout(): void {
    $_SESSION = [];
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
    redirect('/admin/index.php');
}

function auth_check(): bool {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function auth_require(): void {
    if (!auth_check()) {
        redirect('/admin/index.php');
    }
}

function auth_user(): array|false {
    if (!auth_check()) {
        return false;
    }

    return [
        'id'      => $_SESSION['user_id'] ?? 0,
        'nome'    => $_SESSION['user_nome'] ?? '',
        'usuario' => $_SESSION['user_usuario'] ?? '',
        'perfil'  => $_SESSION['user_perfil'] ?? '1',
        'cod_org' => $_SESSION['cod_org'] ?? 10001
    ];
}
