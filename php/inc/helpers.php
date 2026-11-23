<?php
declare(strict_types=1);

/** Escapa texto para HTML. */
function e($v): string
{
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function now(): string { return date('Y-m-d H:i:s'); }

/** Caminho base do site (vazio se estiver na raiz do domínio; "/pasta" se estiver em subpasta). */
function base_path(): string
{
    static $b = null;
    if ($b !== null) { return $b; }
    $b = '';
    $doc = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string)$_SERVER['DOCUMENT_ROOT']) : false;
    $root = realpath(MB_ROOT);
    if ($doc && $root && strpos($root, $doc) === 0) {
        $b = str_replace('\\', '/', substr($root, strlen($doc)));
        $b = rtrim($b, '/');
    }
    return $b;
}

/** URL relativa à raiz do domínio: url('admin/artigos.php') */
function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

/** URL de arquivo estático com versão (muda quando o arquivo muda: o navegador não usa cópia antiga). */
function asset(string $path): string
{
    $f = MB_ROOT . '/' . ltrim($path, '/');
    return url($path) . (is_file($f) ? '?v=' . filemtime($f) : '');
}

/** URL absoluta (para e-mails, sitemap, Open Graph). */
function abs_url(string $path = ''): string
{
    if (SITE_URL !== '') {
        $origin = rtrim(SITE_URL, '/');
    } else {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
        $host = preg_replace('/[^a-zA-Z0-9.\-:]/', '', (string)($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $origin = ($https ? 'https://' : 'http://') . $host;
        $origin .= base_path();
        return rtrim($origin, '/') . '/' . ltrim($path, '/');
    }
    return $origin . base_path() . '/' . ltrim($path, '/');
}

function is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function client_ip(): string
{
    $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

function slugify(string $s): string
{
    $s = trim($s);
    if (function_exists('transliterator_transliterate')) {
        $t = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $s);
        if (is_string($t)) { $s = $t; }
    } else {
        $map = ['á'=>'a','à'=>'a','â'=>'a','ã'=>'a','ä'=>'a','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','í'=>'i','ì'=>'i','î'=>'i','ï'=>'i',
                'ó'=>'o','ò'=>'o','ô'=>'o','õ'=>'o','ö'=>'o','ú'=>'u','ù'=>'u','û'=>'u','ü'=>'u','ç'=>'c','ñ'=>'n'];
        $s = strtr(mb_strtolower($s), $map);
    }
    $s = strtolower($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s) ?? '';
    $s = trim($s, '-');
    return mb_substr($s, 0, 110) ?: 'artigo';
}

function redirect(string $to, int $code = 302): never
{
    header('Location: ' . $to, true, $code);
    exit;
}

function json_out(array $data, int $code = 200): never
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function post(string $k, string $default = ''): string
{
    $v = $_POST[$k] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function get(string $k, string $default = ''): string
{
    $v = $_GET[$k] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function fmt_date(?string $dt, string $fmt = 'd/m/Y'): string
{
    if (!$dt) { return ''; }
    $t = strtotime($dt);
    return $t ? date($fmt, $t) : '';
}

function limit_text(string $s, int $n): string
{
    $s = trim(preg_replace('/\s+/', ' ', $s) ?? '');
    return mb_strlen($s) > $n ? rtrim(mb_substr($s, 0, $n - 1)) . '…' : $s;
}

function plain_excerpt(string $html, int $n = 160): string
{
    $t = html_entity_decode(strip_tags(str_replace(['</p>', '<br>', '</li>', '</h2>', '</h3>'], ' ', $html)), ENT_QUOTES, 'UTF-8');
    return limit_text($t, $n);
}

/** Número de WhatsApp a partir de um telefone brasileiro (adiciona 55 quando preciso). */
function wa_digits(string $phone): string
{
    $d = preg_replace('/\D+/', '', $phone) ?? '';
    if ($d === '') { return ''; }
    if (strlen($d) <= 11) { $d = '55' . ltrim($d, '0'); }
    return $d;
}

function wa_link(string $phone, string $text = ''): string
{
    $d = wa_digits($phone);
    if ($d === '') { return ''; }
    return 'https://wa.me/' . $d . ($text !== '' ? '?text=' . rawurlencode($text) : '');
}

/** Cabeçalhos de segurança básicos para todas as respostas HTML. */
function security_headers(): void
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}

function paginate(int $total, int $page, int $per): array
{
    $pages = max(1, (int)ceil($total / max(1, $per)));
    $page = min(max(1, $page), $pages);
    return ['total' => $total, 'page' => $page, 'pages' => $pages, 'per' => $per, 'offset' => ($page - 1) * $per];
}
