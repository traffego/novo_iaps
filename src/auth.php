<?php
// src/auth.php - Autenticação Standard via Banco de Dados (tab_login) com Auto-Recuperação

function auth_login(string $usuario, string $senha): bool {
    $usuario = trim($usuario);
    $senha   = trim($senha);

    if (empty($usuario) || empty($senha)) {
        return false;
    }

    try {
        // Auto-seed: Garantir que a conta 'admin' (senha 'admin123') e 'claudio' (senha 'clau829') existam na tabela tab_login
        $has_admin = (int) db_count('SELECT COUNT(*) FROM tab_login WHERE usuario = ?', ['admin']);
        if ($has_admin === 0) {
            db_query(
                "INSERT INTO tab_login (usuario, senha, nome_usuario, nivel, cod_org) VALUES (?, ?, ?, ?, ?)",
                ['admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', '1', 10001]
            );
        }

        $has_claudio = (int) db_count('SELECT COUNT(*) FROM tab_login WHERE usuario = ?', ['claudio']);
        if ($has_claudio === 0) {
            db_query(
                "INSERT INTO tab_login (usuario, senha, nome_usuario, nivel, cod_org) VALUES (?, ?, ?, ?, ?)",
                ['claudio', 'clau829', 'Cláudio', '1', 10001]
            );
        }

        // Consultar a tabela tab_login
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
        // Exceção de banco tratada
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
