<?php
/**
 * Mailer: sends HTML email through Hostinger SMTP.
 *
 * 1) If PHPMailer is present in /vendor/PHPMailer/src (recommended), it is used.
 * 2) Otherwise a built-in SMTP client is used (SSL 465 or STARTTLS 587, AUTH LOGIN, attachments).
 * 3) If SMTP_HOST is empty, PHP mail() is used as a last resort.
 */
class Mailer
{
    public string $lastError = '';

    /**
     * @param array $attachments [['path' => '/abs/file.pdf', 'name' => 'resume.pdf']]
     */
    public function send(string $to, string $subject, string $html, string $text = '', ?string $replyTo = null, ?string $replyName = null, array $attachments = []): bool
    {
        $this->lastError = '';
        if ($text === '') $text = trim(html_entity_decode(strip_tags(preg_replace('/<br\s*\/?>|<\/p>|<\/tr>/i', "\n", $html) ?? ''), ENT_QUOTES, 'UTF-8'));

        $pm = ROOT . '/vendor/PHPMailer/src/PHPMailer.php';
        if (is_file($pm) && SMTP_HOST !== '') {
            return $this->viaPHPMailer($to, $subject, $html, $text, $replyTo, $replyName, $attachments);
        }
        if (SMTP_HOST !== '') {
            return $this->viaSmtp($to, $subject, $html, $text, $replyTo, $replyName, $attachments);
        }
        return $this->viaMail($to, $subject, $html, $text, $replyTo);
    }

    /* ---------------- PHPMailer ---------------- */
    private function viaPHPMailer(string $to, string $subject, string $html, string $text, ?string $replyTo, ?string $replyName, array $attachments): bool
    {
        require_once ROOT . '/vendor/PHPMailer/src/Exception.php';
        require_once ROOT . '/vendor/PHPMailer/src/PHPMailer.php';
        require_once ROOT . '/vendor/PHPMailer/src/SMTP.php';
        $m = new \PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $m->isSMTP();
            $m->Host = SMTP_HOST;
            $m->Port = SMTP_PORT;
            $m->SMTPAuth = true;
            $m->Username = SMTP_USER;
            $m->Password = SMTP_PASS;
            $m->SMTPSecure = SMTP_SECURE === 'tls' ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            $m->CharSet = 'UTF-8';
            $m->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $m->addAddress($to);
            if ($replyTo) $m->addReplyTo($replyTo, $replyName ?? '');
            foreach ($attachments as $a) {
                if (is_file($a['path'])) $m->addAttachment($a['path'], $a['name'] ?? basename($a['path']));
            }
            $m->isHTML(true);
            $m->Subject = $subject;
            $m->Body = $html;
            $m->AltBody = $text;
            return $m->send();
        } catch (\Throwable $e) {
            $this->lastError = 'PHPMailer: ' . $m->ErrorInfo . ' ' . $e->getMessage();
            return false;
        }
    }

    /* ---------------- Built-in SMTP ---------------- */
    private $sock = null;

    private function viaSmtp(string $to, string $subject, string $html, string $text, ?string $replyTo, ?string $replyName, array $attachments): bool
    {
        try {
            $host = (SMTP_SECURE === 'ssl' ? 'ssl://' : 'tcp://') . SMTP_HOST . ':' . SMTP_PORT;
            $ctx = stream_context_create(['ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'peer_name' => SMTP_HOST]]);
            $this->sock = @stream_socket_client($host, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
            if (!$this->sock) throw new RuntimeException("Connect failed: $errstr ($errno)");
            stream_set_timeout($this->sock, 15);
            $this->expect(220);
            $ehloHost = parse_url(SITE_URL, PHP_URL_HOST) ?: 'localhost';
            $this->cmd('EHLO ' . $ehloHost, 250);
            if (SMTP_SECURE === 'tls') {
                $this->cmd('STARTTLS', 220);
                if (!stream_socket_enable_crypto($this->sock, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT)) {
                    throw new RuntimeException('STARTTLS failed');
                }
                $this->cmd('EHLO ' . $ehloHost, 250);
            }
            $this->cmd('AUTH LOGIN', 334);
            $this->cmd(base64_encode(SMTP_USER), 334);
            $this->cmd(base64_encode(SMTP_PASS), 235);
            $this->cmd('MAIL FROM:<' . MAIL_FROM . '>', 250);
            $this->cmd('RCPT TO:<' . $to . '>', [250, 251]);
            $this->cmd('DATA', 354);
            $msg = $this->buildMessage($to, $subject, $html, $text, $replyTo, $replyName, $attachments);
            // dot-stuffing
            $msg = preg_replace('/^\./m', '..', $msg);
            fwrite($this->sock, $msg . "\r\n.\r\n");
            $this->expect(250);
            $this->cmd('QUIT', 221);
            fclose($this->sock);
            return true;
        } catch (\Throwable $e) {
            $this->lastError = 'SMTP: ' . $e->getMessage();
            if (is_resource($this->sock)) @fclose($this->sock);
            return false;
        }
    }

    private function cmd(string $line, int|array $expect): string
    {
        fwrite($this->sock, $line . "\r\n");
        return $this->expect($expect);
    }

    private function expect(int|array $codes): string
    {
        $codes = (array)$codes;
        $data = '';
        while (($line = fgets($this->sock, 1024)) !== false) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') break;   // last line of a multi-line reply
            if (strlen($line) < 4) break;
        }
        $code = (int)substr($data, 0, 3);
        if (!in_array($code, $codes, true)) {
            throw new RuntimeException('Unexpected reply: ' . trim($data));
        }
        return $data;
    }

    private function encHeader(string $s): string
    {
        return preg_match('/[^\x20-\x7E]/', $s) ? '=?UTF-8?B?' . base64_encode($s) . '?=' : $s;
    }

    private function buildMessage(string $to, string $subject, string $html, string $text, ?string $replyTo, ?string $replyName, array $attachments): string
    {
        $eol = "\r\n";
        $mixed = 'mix_' . bin2hex(random_bytes(8));
        $alt = 'alt_' . bin2hex(random_bytes(8));
        $domain = parse_url(SITE_URL, PHP_URL_HOST) ?: 'localhost';
        $h = [];
        $h[] = 'Date: ' . date('r');
        $h[] = 'From: ' . $this->encHeader(MAIL_FROM_NAME) . ' <' . MAIL_FROM . '>';
        $h[] = 'To: <' . $to . '>';
        if ($replyTo) $h[] = 'Reply-To: ' . ($replyName ? $this->encHeader($replyName) . ' ' : '') . '<' . $replyTo . '>';
        $h[] = 'Subject: ' . $this->encHeader($subject);
        $h[] = 'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $domain . '>';
        $h[] = 'MIME-Version: 1.0';
        $h[] = 'X-Mailer: ConnektMailer/1.0';

        $altPart = '--' . $alt . $eol
            . 'Content-Type: text/plain; charset=UTF-8' . $eol . 'Content-Transfer-Encoding: base64' . $eol . $eol
            . chunk_split(base64_encode($text)) . $eol
            . '--' . $alt . $eol
            . 'Content-Type: text/html; charset=UTF-8' . $eol . 'Content-Transfer-Encoding: base64' . $eol . $eol
            . chunk_split(base64_encode($html)) . $eol
            . '--' . $alt . '--' . $eol;

        $files = array_filter($attachments, fn($a) => is_file($a['path'] ?? ''));
        if (!$files) {
            $h[] = 'Content-Type: multipart/alternative; boundary="' . $alt . '"';
            return implode($eol, $h) . $eol . $eol . $altPart;
        }
        $h[] = 'Content-Type: multipart/mixed; boundary="' . $mixed . '"';
        $body = '--' . $mixed . $eol . 'Content-Type: multipart/alternative; boundary="' . $alt . '"' . $eol . $eol . $altPart;
        foreach ($files as $a) {
            $name = preg_replace('/[^\w.\- ]+/', '_', $a['name'] ?? basename($a['path']));
            $mime = function_exists('mime_content_type') ? (mime_content_type($a['path']) ?: 'application/octet-stream') : 'application/octet-stream';
            $body .= '--' . $mixed . $eol
                . 'Content-Type: ' . $mime . '; name="' . $name . '"' . $eol
                . 'Content-Transfer-Encoding: base64' . $eol
                . 'Content-Disposition: attachment; filename="' . $name . '"' . $eol . $eol
                . chunk_split(base64_encode((string)file_get_contents($a['path']))) . $eol;
        }
        $body .= '--' . $mixed . '--' . $eol;
        return implode($eol, $h) . $eol . $eol . $body;
    }

    /* ---------------- PHP mail() fallback ---------------- */
    private function viaMail(string $to, string $subject, string $html, string $text, ?string $replyTo): bool
    {
        $headers = 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n" . 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>' . "\r\n";
        if ($replyTo) $headers .= 'Reply-To: <' . $replyTo . '>' . "\r\n";
        $ok = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, $headers);
        if (!$ok) $this->lastError = 'mail() failed';
        return $ok;
    }
}
