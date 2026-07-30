<?php
// src/auth.php

function auth_check_brute_force(string $ip, int $cod_org = 10001): bool {
    try {
        $sql = 'SELECT COUNT(*) FROM tab_login_erro WHERE ip = ? AND data_hora > (NOW() - INTERVAL 30 MINUTE)';
        $erros = db_count($sql, [$ip]);
        return $erros >= 10;
    } catch (Throwable $e) {
        return false;
    }
}

function auth_log_error(string $ip, int $cod_org = 10001, string $usuario = ''): void {
    try {
        db_insert('tab_login_erro', [
            'ip' => $ip,
            'cod_org' => $cod_org,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    } catch (Throwable $e) {
        // Ignora caso a tabela não suporte a coluna
    }
}

function auth_clear_errors(string $ip, int $cod_org = 10001): void {
    try {
        db_delete('tab_login_erro', 'ip = ?', [$ip]);
    } catch (Throwable $e) {
        // Ignora
    }
}

function auth_log_access(int $user_id, int $cod_org = 10001): void {
    try {
        db_insert('tab_login_registro', [
            'usuario' => $user_id,
            'cod_org' => $cod_org,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'created_at' => date('Y-m-d H:i:s')
        ]);
    } catch (Throwable $e) {
        // Ignora
    }
}

function auth_login(string $usuario, string $senha): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    if (auth_check_brute_force($ip)) {
        return false; // Bloqueado temporariamente
    }
    
    try {
        // Buscar usuário na tabela tab_login
        $sql = 'SELECT * FROM tab_login WHERE usuario = ? LIMIT 1';
        $user = db_fetch($sql, [$usuario]);
        
        if ($user) {
            $senha_db = $user['senha'];
            $autenticado = false;

            // 1. bcrypt hash (password_verify)
            if (password_verify($senha, $senha_db)) {
                $autenticado = true;
            }
            // 2. Senha em texto puro (legado)
            elseif ($senha === $senha_db) {
                $autenticado = true;
            }
            // 3. MD5 (legado)
            elseif (md5($senha) === $senha_db) {
                $autenticado = true;
            }

            if ($autenticado) {
                auth_clear_errors($ip);
                
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_nome'] = $user['nome_usuario'] ?? $user['usuario'];
                $_SESSION['user_usuario'] = $user['usuario'];
                $_SESSION['user_perfil'] = $user['nivel'] ?? '1';
                $_SESSION['cod_org'] = (int)($user['cod_org'] ?? 10001);
                $_SESSION['logged_in'] = true;
                
                auth_log_access((int) $user['id'], (int)($_SESSION['cod_org']));
                
                return true;
            }
        }
    } catch (Throwable $e) {
        // Log de exceção em desenvolvimento
    }
    
    auth_log_error($ip, 10001, $usuario);
    return false;
}

function auth_logout(): void {
    $_SESSION = [];
    session_destroy();
    redirect('/admin');
}

function auth_check(): bool {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function auth_require(): void {
    if (!auth_check()) {
        redirect('/admin');
    }
}

function auth_user(): array|false {
    if (!auth_check()) {
        return false;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? 0,
        'nome' => $_SESSION['user_nome'] ?? '',
        'usuario' => $_SESSION['user_usuario'] ?? '',
        'perfil' => $_SESSION['user_perfil'] ?? '1',
        'cod_org' => $_SESSION['cod_org'] ?? 10001
    ];
}
