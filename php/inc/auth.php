<?php
declare(strict_types=1);

const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_WINDOW_MIN = 15;
const SESSION_IDLE_SECONDS = 14400; // 4 horas

function mb_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) { return; }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    session_name('mbadm');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => base_path() . '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    if (isset($_SESSION['last']) && time() - (int)$_SESSION['last'] > SESSION_IDLE_SECONDS) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    $_SESSION['last'] = time();
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    mb_session();
    if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
    return (string)$_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void
{
    mb_session();
    $sent = (string)($_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if ($sent === '' || empty($_SESSION['csrf']) || !hash_equals((string)$_SESSION['csrf'], $sent)) {
        http_response_code(419);
        if (str_contains((string)($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') || ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
            json_out(['ok' => false, 'error' => 'Sessão expirada. Recarregue a página.'], 419);
        }
        exit('Sessão expirada ou formulário inválido. Volte, recarregue a página e tente de novo.');
    }
}

/* ---------- Mensagens rápidas ---------- */
function flash(string $type, string $msg): void
{
    mb_session();
    $_SESSION['flash'][] = ['t' => $type, 'm' => $msg];
}

function flash_pull(): array
{
    mb_session();
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- Usuário atual ---------- */
function current_user(): ?array
{
    static $u = false;
    if ($u !== false) { return $u; }
    mb_session();
    $u = null;
    if (!empty($_SESSION['uid'])) {
        $r = row('SELECT id, name, email, username, role, active FROM users WHERE id = :id', [':id' => (int)$_SESSION['uid']]);
        if ($r && (int)$r['active'] === 1) { $u = $r; }
        else { unset($_SESSION['uid']); }
    }
    return $u;
}

function is_admin(): bool
{
    $u = current_user();
    return $u !== null && $u['role'] === 'admin';
}

/** Exige login. Com $adminOnly, só deixa passar Administradores. */
function require_login(bool $adminOnly = false): array
{
    security_headers();
    header('Cache-Control: no-store');
    $u = current_user();
    if (!$u) { redirect(url('admin/login.php')); }
    if ($adminOnly && $u['role'] !== 'admin') {
        flash('err', 'Você não tem permissão para acessar essa área.');
        redirect(url('admin/index.php'));
    }
    return $u;
}

/* ---------- Proteção contra força bruta ---------- */
/** Minutos restantes de bloqueio (0 = liberado). */
function login_blocked_minutes(string $ip, string $login): int
{
    $since = date('Y-m-d H:i:s', time() - LOGIN_WINDOW_MIN * 60);
    q('DELETE FROM login_attempts WHERE created_at < :t', [':t' => date('Y-m-d H:i:s', time() - 86400)]);
    $worst = 0;
    foreach ([['ip = :v', $ip], ['username = :v', mb_strtolower($login)]] as [$cond, $v]) {
        $list = rows("SELECT created_at FROM login_attempts WHERE $cond AND created_at >= :s ORDER BY created_at DESC", [':v' => $v, ':s' => $since]);
        if (count($list) >= LOGIN_MAX_ATTEMPTS) {
            $oldestOfBlock = strtotime($list[LOGIN_MAX_ATTEMPTS - 1]['created_at']);
            $left = (int)ceil(($oldestOfBlock + LOGIN_WINDOW_MIN * 60 - time()) / 60);
            $worst = max($worst, max(1, $left));
        }
    }
    return $worst;
}

function attempt_login(string $login, string $pass): true|string
{
    $ip = client_ip();
    $login = trim($login);
    $wait = login_blocked_minutes($ip, $login);
    if ($wait > 0) { return 'Muitas tentativas erradas. Aguarde ' . $wait . ' minuto' . ($wait > 1 ? 's' : '') . ' e tente de novo.'; }

    $u = row('SELECT * FROM users WHERE (username = :l1 OR email = :l2) AND active = 1', [':l1' => $login, ':l2' => $login]);
    // gasta o mesmo tempo mesmo quando o usuário não existe
    $hash = $u['password_hash'] ?? '$2y$10$usesomesillystringforsaltuQ7hQ2cV7h1bX5b4QqY0m5e5m6Gx3W';
    $ok = password_verify($pass, $hash) && $u;
    if (!$ok) {
        q('INSERT INTO login_attempts (ip, username, created_at) VALUES (:i, :u, :c)', [':i' => $ip, ':u' => mb_strtolower($login), ':c' => now()]);
        return 'Usuário ou senha incorretos.';
    }
    q('DELETE FROM login_attempts WHERE ip = :i OR username = :u', [':i' => $ip, ':u' => mb_strtolower($login)]);
    mb_session();
    session_regenerate_id(true);
    $_SESSION['uid'] = (int)$u['id'];
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
        q('UPDATE users SET password_hash = :h WHERE id = :id', [':h' => password_hash($pass, PASSWORD_DEFAULT), ':id' => $u['id']]);
    }
    q('UPDATE users SET last_login = :n WHERE id = :id', [':n' => now(), ':id' => $u['id']]);
    return true;
}

function do_logout(): void
{
    mb_session();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', ['expires' => time() - 3600, 'path' => $p['path'], 'secure' => $p['secure'], 'httponly' => true, 'samesite' => 'Lax']);
    }
    session_destroy();
}

function password_problem(string $pass): ?string
{
    if (mb_strlen($pass) < 8) { return 'A senha precisa ter pelo menos 8 caracteres.'; }
    if (!preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) { return 'Use letras e números na senha.'; }
    return null;
}

/* ---------- Recuperação de senha ---------- */
function create_password_reset(int $userId): string
{
    $token = bin2hex(random_bytes(32));
    q('UPDATE password_resets SET used = 1 WHERE user_id = :u', [':u' => $userId]);
    q('INSERT INTO password_resets (user_id, token_hash, expires_at, used, created_at) VALUES (:u, :h, :e, 0, :c)',
        [':u' => $userId, ':h' => hash('sha256', $token), ':e' => date('Y-m-d H:i:s', time() + 3600), ':c' => now()]);
    return $token;
}

function find_valid_reset(string $token): ?array
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) { return null; }
    return row('SELECT * FROM password_resets WHERE token_hash = :h AND used = 0 AND expires_at >= :n', [':h' => hash('sha256', $token), ':n' => now()]);
}

/** Limita pedidos de recuperação por IP (3 por hora). */
function rate_limited(string $kind, int $max, int $seconds): bool
{
    $ip = client_ip();
    $since = date('Y-m-d H:i:s', time() - $seconds);
    q('DELETE FROM rate_limits WHERE created_at < :t', [':t' => date('Y-m-d H:i:s', time() - 86400)]);
    $n = (int)val('SELECT COUNT(*) FROM rate_limits WHERE ip = :i AND kind = :k AND created_at >= :s', [':i' => $ip, ':k' => $kind, ':s' => $since]);
    return $n >= $max;
}

function rate_hit(string $kind): void
{
    q('INSERT INTO rate_limits (ip, kind, created_at) VALUES (:i, :k, :c)', [':i' => client_ip(), ':k' => $kind, ':c' => now()]);
}
