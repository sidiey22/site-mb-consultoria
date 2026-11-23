<?php
declare(strict_types=1);

/**
 * Limpa o HTML vindo do editor (lista de tags e atributos permitidos).
 * Tudo que não está na lista é removido; scripts, estilos e atributos "on..." nunca passam.
 */
function sanitize_html(string $html): string
{
    $html = trim($html);
    if ($html === '') { return ''; }
    if (!class_exists('DOMDocument')) { return nl2br(e(strip_tags($html))); }

    $allowed = [
        'p' => ['class'], 'br' => [], 'hr' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [],
        'h2' => ['class'], 'h3' => ['class'], 'h4' => ['class'],
        'ul' => [], 'ol' => [], 'li' => ['class'],
        'blockquote' => [],
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'width', 'height'],
        'iframe' => ['src', 'class'],
    ];
    $dropWithContent = ['script', 'style', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'svg', 'math',
                        'link', 'meta', 'base', 'noscript', 'template', 'frame', 'frameset', 'applet', 'audio', 'video', 'canvas'];

    $dom = new DOMDocument('1.0', 'UTF-8');
    $prev = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="UTF-8"><div id="mb-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    libxml_use_internal_errors($prev);

    $root = $dom->getElementById('mb-root');
    if (!$root) { return ''; }

    $clean = function (DOMNode $node) use (&$clean, $allowed, $dropWithContent, $dom): void {
        $children = [];
        foreach ($node->childNodes as $c) { $children[] = $c; }
        foreach ($children as $child) {
            if ($child instanceof DOMComment || $child instanceof DOMProcessingInstruction) { $node->removeChild($child); continue; }
            if (!($child instanceof DOMElement)) { continue; }
            $tag = strtolower($child->tagName);

            if (in_array($tag, $dropWithContent, true)) { $node->removeChild($child); continue; }

            if (!isset($allowed[$tag])) { // desconhecida: mantém só o conteúdo
                $clean($child);
                while ($child->firstChild) { $node->insertBefore($child->firstChild, $child); }
                $node->removeChild($child);
                continue;
            }

            // atributos
            $attrs = [];
            foreach ($child->attributes as $a) { $attrs[$a->name] = $a->value; }
            foreach (array_keys($attrs) as $name) { $child->removeAttribute($name); }
            foreach ($allowed[$tag] as $name) {
                if (!isset($attrs[$name])) { continue; }
                $v = trim($attrs[$name]);
                switch ($name) {
                    case 'class':
                        $ok = [];
                        foreach (preg_split('/\s+/', $v) ?: [] as $cls) {
                            if (preg_match('/^(ql-align-(center|right|justify)|ql-indent-[1-8]|ql-video)$/', $cls)) { $ok[] = $cls; }
                        }
                        if ($ok) { $child->setAttribute('class', implode(' ', $ok)); }
                        break;
                    case 'href':
                        $u = sanitize_url($v, ['http', 'https', 'mailto', 'tel'], true);
                        if ($u !== '') { $child->setAttribute('href', $u); }
                        break;
                    case 'src':
                        if ($tag === 'iframe') {
                            if (preg_match('~^https://(www\.)?(youtube\.com|youtube-nocookie\.com)/embed/[A-Za-z0-9_\-]{6,20}(\?[A-Za-z0-9_=&\-%]*)?$~', $v)) {
                                $child->setAttribute('src', $v);
                            }
                        } else {
                            $u = sanitize_url($v, ['http', 'https'], true);
                            if ($u !== '') { $child->setAttribute('src', $u); }
                        }
                        break;
                    case 'target':
                        if ($v === '_blank') { $child->setAttribute('target', '_blank'); }
                        break;
                    case 'alt':
                        $child->setAttribute('alt', mb_substr($v, 0, 200));
                        break;
                    case 'width':
                    case 'height':
                        if (preg_match('/^\d{1,4}$/', $v)) { $child->setAttribute($name, $v); }
                        break;
                    default:
                        break;
                }
            }

            // regras por tag
            if ($tag === 'a') {
                if (!$child->hasAttribute('href')) { // link sem destino válido vira texto
                    $clean($child);
                    while ($child->firstChild) { $node->insertBefore($child->firstChild, $child); }
                    $node->removeChild($child);
                    continue;
                }
                if ($child->getAttribute('target') === '_blank') { $child->setAttribute('rel', 'noopener noreferrer'); }
            }
            if ($tag === 'img') {
                if (!$child->hasAttribute('src')) { $node->removeChild($child); continue; }
                $child->setAttribute('loading', 'lazy');
                if (!$child->hasAttribute('alt')) { $child->setAttribute('alt', ''); }
            }
            if ($tag === 'iframe') {
                if (!$child->hasAttribute('src')) { $node->removeChild($child); continue; }
                $child->setAttribute('class', 'ql-video');
                $child->setAttribute('frameborder', '0');
                $child->setAttribute('allowfullscreen', 'true');
                $child->setAttribute('loading', 'lazy');
                $child->setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
                $child->setAttribute('title', 'Vídeo do YouTube');
                continue; // iframe não tem filhos úteis
            }
            $clean($child);
        }
    };
    $clean($root);

    $out = '';
    foreach ($root->childNodes as $c) { $out .= $dom->saveHTML($c); }
    return trim($out);
}

/** Valida uma URL: só aceita os esquemas permitidos (ou caminhos relativos). */
function sanitize_url(string $u, array $schemes, bool $allowRelative): string
{
    $u = trim($u);
    $probe = strtolower(preg_replace('/[\x00-\x20\x7f]+/', '', html_entity_decode($u, ENT_QUOTES, 'UTF-8')) ?? '');
    if ($probe === '') { return ''; }
    if (preg_match('/^([a-z][a-z0-9+.\-]*):/', $probe, $m)) {
        return in_array($m[1], $schemes, true) ? $u : '';
    }
    if (str_starts_with($probe, '//')) { return ''; }
    if ($allowRelative && (str_starts_with($probe, '/') || str_starts_with($probe, '#') || preg_match('~^[a-z0-9_\-./]+$~', $probe))) {
        return $u;
    }
    return '';
}
