<?php
declare(strict_types=1);

/** Valores padrão (os mesmos que já estavam no site antes do painel). */
function setting_defaults(): array
{
    return [
        'notify_emails' => '',
        'smtp_host' => SMTP_HOST, 'smtp_port' => (string)SMTP_PORT, 'smtp_secure' => SMTP_SECURE,
        'smtp_user' => SMTP_USER, 'smtp_pass' => SMTP_PASS, 'smtp_from' => SMTP_FROM,
        'smtp_from_name' => 'Site MB Consultoria', 'smtp_verify' => '1',
        'phone' => '(65) 98115-7076',
        'whatsapp' => '5565981157076',
        'email' => 'marianabonjour@mbconsultoriapolitica.com',
        'instagram' => 'https://www.instagram.com/mbconsultoriapolitica/',
        'linkedin' => '', 'facebook' => '', 'youtube' => '',
        'app_secret' => '',
    ];
}

function settings_all(bool $refresh = false): array
{
    static $cache = null;
    if ($cache !== null && !$refresh) { return $cache; }
    $cache = setting_defaults();
    try {
        foreach (rows('SELECT skey, svalue FROM settings') as $r) {
            if ($r['svalue'] !== null && ($r['svalue'] !== '' || array_key_exists($r['skey'], $cache))) {
                $cache[$r['skey']] = (string)$r['svalue'];
            }
        }
    } catch (Throwable $e) { /* banco ainda não configurado: usa os padrões */ }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $a = settings_all();
    return $a[$key] ?? $default;
}

function set_setting(string $key, string $value): void
{
    $exists = val('SELECT COUNT(*) FROM settings WHERE skey = :k', [':k' => $key]);
    if ($exists) { q('UPDATE settings SET svalue = :v WHERE skey = :k', [':v' => $value, ':k' => $key]); }
    else { q('INSERT INTO settings (skey, svalue) VALUES (:k, :v)', [':k' => $key, ':v' => $value]); }
    settings_all(true);
}

/** Dados públicos usados no site (contatos e redes). */
function site_settings(): array
{
    $s = settings_all();
    $ig = trim($s['instagram']);
    $handle = '';
    if ($ig !== '') {
        $path = trim((string)parse_url($ig, PHP_URL_PATH), '/');
        $handle = $path !== '' ? '@' . ltrim(explode('/', $path)[0], '@') : $ig;
    }
    return [
        'phone' => $s['phone'], 'whatsapp' => preg_replace('/\D+/', '', $s['whatsapp']) ?: '5565981157076',
        'email' => $s['email'], 'instagram' => $ig, 'instagram_handle' => $handle,
        'linkedin' => trim($s['linkedin']), 'facebook' => trim($s['facebook']), 'youtube' => trim($s['youtube']),
    ];
}

/** Chave secreta do site (gerada na instalação), usada para assinar tokens dos formulários. */
function app_secret(): string
{
    $s = setting('app_secret');
    return $s !== '' ? $s : hash('sha256', DB_NAME . DB_USER . DB_PASS . 'mb-fallback');
}
