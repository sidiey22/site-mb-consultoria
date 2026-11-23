<?php
declare(strict_types=1);

/** Conexão PDO única (prepared statements em tudo). */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) { return $pdo; }
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    if (DB_DRIVER === 'sqlite') { // apenas para testes locais
        $pdo = new PDO('sqlite:' . DB_PATH, null, null, $opts);
        $pdo->exec('PRAGMA foreign_keys = ON');
    } else {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $opts);
        $pdo->exec("SET NAMES utf8mb4");
    }
    return $pdo;
}

function db_ok(): bool
{
    try { db(); return true; } catch (Throwable $e) { return false; }
}

function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}

function rows(string $sql, array $params = []): array { return q($sql, $params)->fetchAll(); }

function row(string $sql, array $params = []): ?array
{
    $r = q($sql, $params)->fetch();
    return $r === false ? null : $r;
}

function val(string $sql, array $params = [])
{
    $r = q($sql, $params)->fetch(PDO::FETCH_NUM);
    return $r === false ? null : $r[0];
}

function insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $cols) . ') VALUES (' . implode(',', array_map(fn($c) => ':' . $c, $cols)) . ')';
    q($sql, $data);
    return (int)db()->lastInsertId();
}

function update(string $table, int $id, array $data): void
{
    $set = implode(',', array_map(fn($c) => $c . '=:' . $c, array_keys($data)));
    $data['__id'] = $id;
    q('UPDATE ' . $table . ' SET ' . $set . ' WHERE id=:__id', $data);
}

/** Estrutura do banco (MySQL). Usada pelo install.php. */
function schema_statements(): array
{
    $mysql = DB_DRIVER !== 'sqlite';
    $pk = $mysql ? 'INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $text = $mysql ? 'MEDIUMTEXT' : 'TEXT';
    $eng = $mysql ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci' : '';
    $ix = fn(string $name, string $tbl, string $cols) => "CREATE INDEX $name ON $tbl ($cols)";
    return [
        "CREATE TABLE IF NOT EXISTS users (
            id $pk,
            name VARCHAR(120) NOT NULL,
            email VARCHAR(160) NOT NULL,
            username VARCHAR(60) NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            role VARCHAR(10) NOT NULL DEFAULT 'editor',
            active TINYINT NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            last_login DATETIME NULL,
            UNIQUE (email), UNIQUE (username)
        )$eng",
        "CREATE TABLE IF NOT EXISTS password_resets (
            id $pk,
            user_id INT NOT NULL,
            token_hash CHAR(64) NOT NULL,
            expires_at DATETIME NOT NULL,
            used TINYINT NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL
        )$eng",
        "CREATE TABLE IF NOT EXISTS login_attempts (
            id $pk,
            ip VARCHAR(45) NOT NULL,
            username VARCHAR(120) NOT NULL,
            created_at DATETIME NOT NULL
        )$eng",
        "CREATE TABLE IF NOT EXISTS categories (
            id $pk,
            name VARCHAR(80) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            created_at DATETIME NOT NULL,
            UNIQUE (slug)
        )$eng",
        "CREATE TABLE IF NOT EXISTS articles (
            id $pk,
            title VARCHAR(200) NOT NULL,
            slug VARCHAR(120) NOT NULL,
            summary TEXT NULL,
            cover VARCHAR(255) NULL,
            content $text NULL,
            category_id INT NULL,
            author VARCHAR(120) NULL,
            user_id INT NULL,
            published_at DATETIME NULL,
            status VARCHAR(12) NOT NULL DEFAULT 'rascunho',
            featured TINYINT NOT NULL DEFAULT 0,
            seo_title VARCHAR(160) NULL,
            seo_description VARCHAR(320) NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            UNIQUE (slug)
        )$eng",
        $ix('idx_articles_status', 'articles', 'status, published_at'),
        $ix('idx_articles_cat', 'articles', 'category_id'),
        "CREATE TABLE IF NOT EXISTS leads (
            id $pk,
            name VARCHAR(160) NOT NULL,
            email VARCHAR(160) NOT NULL,
            phone VARCHAR(40) NULL,
            city VARCHAR(120) NULL,
            company VARCHAR(160) NULL,
            subject VARCHAR(160) NULL,
            message TEXT NULL,
            form_name VARCHAR(80) NOT NULL DEFAULT 'contato',
            page_url VARCHAR(255) NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'novo',
            notes TEXT NULL,
            utm_source VARCHAR(120) NULL,
            utm_medium VARCHAR(120) NULL,
            utm_campaign VARCHAR(160) NULL,
            utm_term VARCHAR(160) NULL,
            utm_content VARCHAR(160) NULL,
            referrer VARCHAR(255) NULL,
            ip VARCHAR(45) NULL,
            user_agent VARCHAR(255) NULL,
            mail_sent TINYINT NOT NULL DEFAULT 0,
            mail_error VARCHAR(255) NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL
        )$eng",
        $ix('idx_leads_created', 'leads', 'created_at'),
        $ix('idx_leads_status', 'leads', 'status'),
        "CREATE TABLE IF NOT EXISTS settings (
            skey VARCHAR(80) NOT NULL PRIMARY KEY,
            svalue TEXT NULL
        )$eng",
        "CREATE TABLE IF NOT EXISTS rate_limits (
            id $pk,
            ip VARCHAR(45) NOT NULL,
            kind VARCHAR(30) NOT NULL,
            created_at DATETIME NOT NULL
        )$eng",
        $ix('idx_rate_ip', 'rate_limits', 'ip, kind, created_at'),
        $ix('idx_attempts_ip', 'login_attempts', 'ip, created_at'),
    ];
}
