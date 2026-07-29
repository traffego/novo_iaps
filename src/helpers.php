<?php
// src/helpers.php

function e(string $string): string {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): never {
    header(sprintf('Location: %s', $url));
    exit;
}

function flash(string $key, ?string $message = null): ?string {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }
    
    if (isset($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }
    
    return null;
}

function old(string $key, mixed $default = ''): mixed {
    $old = $_SESSION['old_input'][$key] ?? $default;
    unset($_SESSION['old_input'][$key]); // Apaga ao usar
    return $old;
}

function save_old_input(): void {
    $_SESSION['old_input'] = $_POST;
}

function is_current_page(string $page): bool {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($page === '/') {
        return $uri === '/';
    }
    return str_starts_with($uri, $page);
}

function format_date(string $date, string $format = 'd/m/Y'): string {
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : '';
}

function format_money(float $value): string {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function truncate(string $string, int $length = 100): string {
    if (mb_strlen($string) <= $length) {
        return $string;
    }
    return mb_substr($string, 0, $length) . '...';
}

function slug(string $string): string {
    // Substitui caracteres especiais
    $map = [
        '/[áàâãä]/u' => 'a', '/[ÁÀÂÃÄ]/u' => 'A',
        '/[éèêë]/u' => 'e', '/[ÉÈÊË]/u' => 'E',
        '/[íìîï]/u' => 'i', '/[ÍÌÎÏ]/u' => 'I',
        '/[óòôõö]/u' => 'o', '/[ÓÒÔÕÖ]/u' => 'O',
        '/[úùûü]/u' => 'u', '/[ÚÙÛÜ]/u' => 'U',
        '/ç/' => 'c', '/Ç/' => 'C', '/ñ/' => 'n', '/Ñ/' => 'N'
    ];
    $string = preg_replace(array_keys($map), array_values($map), $string);
    $string = strtolower(trim($string));
    // Troca tudo que não for letra e número por traço
    $string = preg_replace('/[^a-z0-9-]+/', '-', $string);
    // Remove traços duplos
    return preg_replace('/-+/', '-', $string);
}

function paginate(int $total, int $per_page = 20): array {
    $current_page = max(1, (int)($_GET['pagina'] ?? 1));
    $total_pages = max(1, ceil($total / $per_page));
    
    // Garante que a página atual não ultrapasse o total
    if ($current_page > $total_pages) {
        $current_page = (int) $total_pages;
    }
    
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'current_page' => $current_page,
        'total_pages' => (int) $total_pages,
        'total' => $total,
        'per_page' => $per_page,
        'offset' => $offset
    ];
}

function pagination_html(array $pagination, string $base_url): string {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<ul class="pagination">';
    
    // Processa a URL base para preservar query strings existentes
    $parsed = parse_url($base_url);
    $path = $parsed['path'] ?? '/';
    $query = [];
    if (isset($parsed['query'])) {
        parse_str($parsed['query'], $query);
    }
    
    $build_url = function($page) use ($path, $query) {
        $query['pagina'] = $page;
        return $path . '?' . http_build_query($query);
    };
    
    if ($pagination['current_page'] > 1) {
        $html .= sprintf('<li><a href="%s">&laquo; Anterior</a></li>', e($build_url($pagination['current_page'] - 1)));
    }
    
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = $i === $pagination['current_page'] ? 'class="active"' : '';
        $html .= sprintf('<li %s><a href="%s">%d</a></li>', $active, e($build_url($i)), $i);
    }
    
    if ($pagination['current_page'] < $pagination['total_pages']) {
        $html .= sprintf('<li><a href="%s">Próxima &raquo;</a></li>', e($build_url($pagination['current_page'] + 1)));
    }
    
    $html .= '</ul>';
    
    return $html;
}

function asset(string $path): string {
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function base_url(string $path = ''): string {
    return rtrim(APP_URL, '/') . '/' . ltrim($path, '/');
}

function upload_path(string $subdir = ''): string {
    $path = UPLOAD_PATH;
    if ($subdir !== '') {
        $path .= DIRECTORY_SEPARATOR . trim($subdir, '/\\');
    }
    return $path;
}

function is_ajax(): bool {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function json_response(mixed $data, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
