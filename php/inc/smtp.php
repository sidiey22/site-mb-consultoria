<?php
declare(strict_types=1);

/**
 * Cliente SMTP simples e completo (sem dependências): SSL (465), STARTTLS (587) ou sem criptografia,
 * autenticação LOGIN/PLAIN, e-mail em HTML + texto, assunto e nomes com acentos.
 * Retorna true quando enviado ou uma mensagem de erro em português.
 */
final class MbSmtp
{
    /** @var resource|null */
    private $fp = null;
    private string $log = '';

    public function __construct(
        private string $host,
        private int $port,
        private string $secure,   // ssl | tls | none
        private string $user,
        private string $pass,
        private bool $verifyCert = true,
        private int $timeout = 15
    ) {}

    public function lastLog(): string { return $this->log; }

    /** @param string[] $to */
    public function send(string $fromEmail, string $fromName, array $to, string $subject, string $html, string $text = '', ?string $replyTo = null, ?string $replyName = null): true|string
    {
        $to = array_values(array_filter(array_map('trim', $to), fn($x) => filter_var($x, FILTER_VALIDATE_EMAIL)));
        if (!$to) { return 'Nenhum e-mail de destino válido.'; }
        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) { return 'E-mail do remetente inválido.'; }
        try {
            $this->connect();
            $this->expect(220);
            $hello = preg_replace('/[^a-zA-Z0-9.\-]/', '', (string)($_SERVER['SERVER_NAME'] ?? 'localhost')) ?: 'localhost';
            $this->cmd('EHLO ' . $hello, [250]);
            if ($this->secure === 'tls') {
                $this->cmd('STARTTLS', [220]);
                $crypto = STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT') ? STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT : 0);
                if (!@stream_socket_enable_crypto($this->fp, true, $crypto)) {
                    throw new RuntimeException('Não foi possível iniciar a conexão segura (TLS). Confira a porta e o certificado.');
                }
                $this->cmd('EHLO ' . $hello, [250]);
            }
            if ($this->user !== '') {
                $r = $this->cmd('AUTH LOGIN', [334, 235, 504, 502]);
                if ($r[0] === 334) {
                    $this->cmd(base64_encode($this->user), [334]);
                    $this->cmd(base64_encode($this->pass), [235]);
                } elseif ($r[0] !== 235) {
                    $this->cmd('AUTH PLAIN ' . base64_encode("\0" . $this->user . "\0" . $this->pass), [235]);
                }
            }
            $this->cmd('MAIL FROM:<' . $fromEmail . '>', [250]);
            foreach ($to as $rcpt) { $this->cmd('RCPT TO:<' . $rcpt . '>', [250, 251]); }
            $this->cmd('DATA', [354]);
            $msg = $this->buildMessage($fromEmail, $fromName, $to, $subject, $html, $text, $replyTo, $replyName);
            $msg = preg_replace('/^\./m', '..', $msg) ?? $msg; // dot-stuffing
            $this->write($msg . "\r\n.");
            $this->expect(250);
            @$this->cmd('QUIT', [221, 250]);
            $this->close();
            return true;
        } catch (Throwable $e) {
            $this->close();
            return $e->getMessage();
        }
    }

    private function connect(): void
    {
        $ssl = ['verify_peer' => $this->verifyCert, 'verify_peer_name' => $this->verifyCert, 'allow_self_signed' => !$this->verifyCert, 'SNI_enabled' => true];
        $ctx = stream_context_create(['ssl' => $ssl]);
        $remote = ($this->secure === 'ssl' ? 'ssl://' : 'tcp://') . $this->host . ':' . $this->port;
        $errno = 0; $errstr = '';
        $fp = @stream_socket_client($remote, $errno, $errstr, $this->timeout, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            $hint = $errstr !== '' ? $errstr : 'sem resposta';
            throw new RuntimeException('Não foi possível conectar ao servidor de e-mail (' . $this->host . ':' . $this->port . '): ' . $hint);
        }
        stream_set_timeout($fp, $this->timeout);
        $this->fp = $fp;
    }

    private function close(): void
    {
        if (is_resource($this->fp)) { @fclose($this->fp); }
        $this->fp = null;
    }

    private function write(string $s): void
    {
        $this->log .= '> ' . (strlen($s) > 200 ? substr($s, 0, 60) . '…' : $s) . "\n";
        $ok = @fwrite($this->fp, $s . "\r\n");
        if ($ok === false) { throw new RuntimeException('A conexão com o servidor de e-mail foi interrompida.'); }
    }

    /** @return array{0:int,1:string} */
    private function read(): array
    {
        $data = '';
        while (true) {
            $line = @fgets($this->fp, 515);
            if ($line === false) {
                $meta = is_resource($this->fp) ? stream_get_meta_data($this->fp) : [];
                throw new RuntimeException(!empty($meta['timed_out']) ? 'O servidor de e-mail demorou demais para responder.' : 'A conexão com o servidor de e-mail foi encerrada.');
            }
            $data .= $line;
            if (strlen($line) < 4 || $line[3] === ' ') { break; }
        }
        $this->log .= '< ' . trim($data) . "\n";
        return [(int)substr($data, 0, 3), trim($data)];
    }

    private function expect(int ...$codes): array
    {
        $r = $this->read();
        if (!in_array($r[0], $codes, true)) { throw new RuntimeException($this->friendly($r)); }
        return $r;
    }

    /** @param int[] $codes */
    private function cmd(string $c, array $codes): array
    {
        $this->write($c);
        $r = $this->read();
        if (!in_array($r[0], $codes, true)) { throw new RuntimeException($this->friendly($r)); }
        return $r;
    }

    private function friendly(array $r): string
    {
        [$code, $text] = $r;
        if ($code === 535 || $code === 534 || $code === 530) { return 'Usuário ou senha do e-mail incorretos (' . $text . ').'; }
        if ($code === 550 || $code === 553) { return 'O servidor recusou o envio (' . $text . ').'; }
        return 'Erro do servidor de e-mail: ' . $text;
    }

    private function encHeader(string $s): string
    {
        return preg_match('/[^\x20-\x7e]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s;
    }

    private function addr(string $email, string $name = ''): string
    {
        $name = trim(str_replace(['"', "\r", "\n"], '', $name));
        return $name === '' ? '<' . $email . '>' : $this->encHeader($name) . ' <' . $email . '>';
    }

    private function buildMessage(string $fromEmail, string $fromName, array $to, string $subject, string $html, string $text, ?string $replyTo, ?string $replyName): string
    {
        $host = preg_replace('/[^a-zA-Z0-9.\-]/', '', (string)($_SERVER['SERVER_NAME'] ?? 'localhost')) ?: 'localhost';
        $b = '=_mb_' . bin2hex(random_bytes(12));
        if ($text === '') { $text = trim(html_entity_decode(strip_tags(preg_replace('~<(br|/p|/tr|/h[1-6]|/li)>~i', "\n", $html) ?? $html), ENT_QUOTES, 'UTF-8')); }
        $h = [];
        $h[] = 'Date: ' . date('r');
        $h[] = 'From: ' . $this->addr($fromEmail, $fromName);
        $h[] = 'To: ' . implode(', ', array_map(fn($x) => '<' . $x . '>', $to));
        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) { $h[] = 'Reply-To: ' . $this->addr($replyTo, (string)$replyName); }
        $h[] = 'Subject: ' . $this->encHeader(str_replace(["\r", "\n"], ' ', $subject));
        $h[] = 'Message-ID: <' . bin2hex(random_bytes(10)) . '@' . $host . '>';
        $h[] = 'MIME-Version: 1.0';
        $h[] = 'Content-Type: multipart/alternative; boundary="' . $b . '"';
        $body  = '--' . $b . "\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($text)) . "\r\n";
        $body .= '--' . $b . "\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html)) . "\r\n";
        $body .= '--' . $b . '--';
        return implode("\r\n", $h) . "\r\n\r\n" . $body;
    }
}
