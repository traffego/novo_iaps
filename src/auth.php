<?php
// src/auth.php

function auth_check_brute_force(string $ip, int $cod_org = 1): bool {
    // Evitar que ocorra erro fatal se a tabela não existir em ambiente de testes ainda.
    try {
        $sql = 'SELECT COUNT(*) FROM tab_login_erro WHERE ip = ? AND cod_org = ? AND data_hora > (NOW() - INTERVAL 30 MINUTE)';
        $erros = db_count($sql, [$ip, $cod_org]);
        return $erros >= 5;
    } catch (Exception $e) {
        return false;
    }
}

function auth_log_error(string $ip, int $cod_org = 1, string $usuario = ''): void {
    try {
        db_insert('tab_login_erro', [
            'ip' => $ip,
            'cod_org' => $cod_org,
            'usuario_tentado' => $usuario,
            'data_hora' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        // Ignora erro caso a tabela não exista ainda.
    }
}

function auth_clear_errors(string $ip, int $cod_org = 1): void {
    try {
        db_delete('tab_login_erro', 'ip = ? AND cod_org = ?', [$ip, $cod_org]);
    } catch (Exception $e) {
        // Ignora
    }
}

function auth_log_access(int $user_id, int $cod_org = 1): void {
    try {
        db_insert('tab_login_registro', [
            'cod_usuario' => $user_id,
            'cod_org' => $cod_org,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            'data_hora' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        // Ignora
    }
}

function auth_login(string $usuario, string $senha, int $cod_org = 1): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    
    if (auth_check_brute_force($ip, $cod_org)) {
        return false; // Bloqueado temporariamente
    }
    
    try {
        $sql = 'SELECT * FROM tab_usuario WHERE (email = ? OR login = ?) AND cod_org = ? AND status = 1 LIMIT 1';
        $user = db_fetch($sql, [$usuario, $usuario, $cod_org]);
        
        if ($user && password_verify($senha, $user['senha'])) {
            // Sucesso
            auth_clear_errors($ip, $cod_org);
            
            $_SESSION['user_id'] = (int) $user['cod_usuario'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_perfil'] = (int) $user['cod_perfil'];
            $_SESSION['cod_org'] = $cod_org;
            $_SESSION['logged_in'] = true;
            
            auth_log_access((int) $user['cod_usuario'], $cod_org);
            
            return true;
        }
    } catch (Exception $e) {
        // Em caso de banco não configurado, falha na autenticação silenciosamente.
    }
    
    // Falha
    auth_log_error($ip, $cod_org, $usuario);
    return false;
}

function auth_logout(): void {
    $_SESSION = [];
    session_destroy();
    redirect('/admin/login');
}

function auth_check(): bool {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function auth_require(): void {
    if (!auth_check()) {
        redirect('/admin/login');
    }
}

function auth_user(): array|false {
    if (!auth_check()) {
        return false;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? 0,
        'nome' => $_SESSION['user_nome'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'perfil' => $_SESSION['user_perfil'] ?? 0,
        'cod_org' => $_SESSION['cod_org'] ?? 1
    ];
}
