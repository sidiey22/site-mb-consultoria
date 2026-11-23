<?php
declare(strict_types=1);

const POSTS_PER_PAGE = 9;

function icon(string $name): string
{
    static $icons = null;
    if ($icons === null) { $icons = is_file(__DIR__ . '/icons.php') ? (require __DIR__ . '/icons.php') : []; }
    return $icons[$name] ?? '';
}

/* ---------- Consultas de artigos (só o que já pode aparecer no site) ---------- */
const VISIBLE_SQL = "a.status IN ('publicado','agendado') AND a.published_at IS NOT NULL AND a.published_at <= :now";

function article_select(): string
{
    return 'SELECT a.*, c.name AS cat_name, c.slug AS cat_slug FROM articles a LEFT JOIN categories c ON c.id = a.category_id';
}

function public_categories(): array
{
    try {
        return rows('SELECT c.id, c.name, c.slug FROM categories c WHERE EXISTS (SELECT 1 FROM articles a WHERE a.category_id = c.id AND ' . VISIBLE_SQL . ') ORDER BY c.name', [':now' => now()]);
    } catch (Throwable $e) { return []; }
}

/** @return array{items:array,pg:array} */
function public_articles(string $catSlug = '', int $page = 1, int $per = POSTS_PER_PAGE): array
{
    try {
        $where = VISIBLE_SQL; $p = [':now' => now()];
        if ($catSlug !== '') { $where .= ' AND c.slug = :cs'; $p[':cs'] = $catSlug; }
        $total = (int)val('SELECT COUNT(*) FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE ' . $where, $p);
        $pg = paginate($total, $page, $per);
        $items = rows(article_select() . ' WHERE ' . $where . ' ORDER BY a.featured DESC, a.published_at DESC, a.id DESC LIMIT ' . (int)$pg['per'] . ' OFFSET ' . (int)$pg['offset'], $p);
        return ['items' => $items, 'pg' => $pg];
    } catch (Throwable $e) {
        return ['items' => [], 'pg' => paginate(0, 1, $per)];
    }
}

function public_article_by_slug(string $slug): ?array
{
    try {
        return row(article_select() . ' WHERE a.slug = :s AND ' . VISIBLE_SQL, [':s' => $slug, ':now' => now()]);
    } catch (Throwable $e) { return null; }
}

function related_articles(array $a, int $limit = 3): array
{
    try {
        $p = [':now' => now(), ':id' => (int)$a['id'], ':cat' => (int)($a['category_id'] ?? 0)];
        $items = rows(article_select() . ' WHERE ' . VISIBLE_SQL . ' AND a.id <> :id ORDER BY CASE WHEN a.category_id = :cat THEN 0 ELSE 1 END, a.published_at DESC LIMIT ' . (int)$limit, $p);
        return $items;
    } catch (Throwable $e) { return []; }
}


function latest_articles(int $excludeId, int $limit = 4): array
{
    try {
        return rows(article_select() . ' WHERE ' . VISIBLE_SQL . ' AND a.id <> :id ORDER BY a.published_at DESC, a.id DESC LIMIT ' . (int)$limit, [':now' => now(), ':id' => $excludeId]);
    } catch (Throwable $e) { return []; }
}

function category_articles(array $a, int $limit = 3): array
{
    if ((int)($a['category_id'] ?? 0) <= 0) { return []; }
    try {
        return rows(article_select() . ' WHERE ' . VISIBLE_SQL . ' AND a.id <> :id AND a.category_id = :cat ORDER BY a.published_at DESC, a.id DESC LIMIT ' . (int)$limit, [':now' => now(), ':id' => (int)$a['id'], ':cat' => (int)$a['category_id']]);
    } catch (Throwable $e) { return []; }
}

/** Insere um bloco de chamada após o N-ésimo parágrafo (só se o texto for longo o bastante). */
function inject_after_paragraph(string $html, string $block, int $n = 4, int $min = 7): string
{
    if (!class_exists('DOMDocument') || trim($html) === '') { return $html; }
    try {
        $prev = libxml_use_internal_errors(true);
        $d = new DOMDocument('1.0', 'UTF-8');
        $d->loadHTML('<?xml encoding="utf-8"?><div id="mbroot">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors(); libxml_use_internal_errors($prev);
        $root = $d->getElementById('mbroot');
        if (!$root) { return $html; }
        $ps = [];
        foreach ($root->childNodes as $c) { if ($c instanceof DOMElement && strtolower($c->nodeName) === 'p' && trim($c->textContent) !== '') { $ps[] = $c; } }
        if (count($ps) < $min) { return $html; }
        $f = $d->createDocumentFragment();
        $f->appendXML('<div class="art-inline-slot" data-slot="1"></div>');
        $ref = $ps[$n - 1]->nextSibling;
        $slot = $f->firstChild;
        if ($ref) { $root->insertBefore($slot, $ref); } else { $root->appendChild($slot); }
        $out = '';
        foreach ($root->childNodes as $c) { $out .= $d->saveHTML($c); }
        return str_replace('<div class="art-inline-slot" data-slot="1"></div>', $block, $out);
    } catch (Throwable $e) { return $html; }
}

/* ---------- Peças de HTML no layout do site ---------- */
function article_url(array $a): string { return url('conteudos/' . $a['slug']); }

function render_post_card(array $a): string
{
    $href = e(article_url($a));
    $cover = media_url($a['cover'] ?? '');
    $img = $cover !== ''
        ? '<div class="ph"><img loading="lazy" src="' . e($cover) . '" alt=""></div>'
        : '<div class="ph empty"><img loading="lazy" src="' . e(url('img/logo-simbolo.png')) . '" alt=""></div>';
    $cat = !empty($a['cat_name']) ? '<span class="pill">' . e($a['cat_name']) . '</span>' : '<span></span>';
    return '<article class="post" data-href="' . $href . '">' . $img
        . '<div class="meta">' . $cat . '<span>' . icon('calendar') . e(fmt_date($a['published_at'])) . '</span></div>'
        . '<h3>' . e($a['title']) . '</h3>'
        . '<p>' . e(limit_text((string)($a['summary'] ?? ''), 150)) . '</p>'
        . '<div class="foot"><a class="link" href="' . $href . '">Leia mais</a><span class="circle">' . icon('arrow-up-right') . '</span></div></article>';
}

function render_pager(array $pg, string $cat = ''): string
{
    if ($pg['pages'] <= 1) { return ''; }
    $link = function (int $n, string $label, string $cls = '', string $aria = '') use ($cat): string {
        $q = [];
        if ($cat !== '') { $q['cat'] = $cat; }
        if ($n > 1) { $q['pagina'] = $n; }
        $href = '?' . http_build_query($q) . '#conteudos';
        if (!$q) { $href = '#conteudos'; }
        return '<a class="pg ' . $cls . '" href="' . e($href) . '" data-pagina="' . $n . '"' . ($aria ? ' aria-label="' . e($aria) . '"' : '') . '>' . $label . '</a>';
    };
    $o = '<nav class="pager" aria-label="Paginação dos conteúdos">';
    if ($pg['page'] > 1) { $o .= $link($pg['page'] - 1, icon('chevron-left'), 'prev', 'Página anterior'); }
    for ($i = 1; $i <= $pg['pages']; $i++) {
        $cur = $i === $pg['page'];
        $o .= $cur ? '<span class="pg on" aria-current="page">' . $i . '</span>' : $link($i, (string)$i, '', 'Página ' . $i);
    }
    if ($pg['page'] < $pg['pages']) { $o .= $link($pg['page'] + 1, icon('chevron-right'), 'next', 'Próxima página'); }
    return $o . '</nav>';
}

function render_posts_inner(string $cat = '', int $page = 1): array
{
    $r = public_articles($cat, $page);
    if (!$r['items']) {
        $msg = $cat !== '' ? 'Ainda não há conteúdos nessa categoria.' : 'Os primeiros conteúdos serão publicados em breve.';
        return ['cards' => '<p class="pending posts-empty">' . e($msg) . '</p>', 'pager' => '', 'count' => 0];
    }
    $cards = '<div class="posts">' . implode('', array_map('render_post_card', $r['items'])) . '</div>';
    return ['cards' => $cards, 'pager' => render_pager($r['pg'], $cat), 'count' => count($r['items'])];
}

/** Bloco da página Conteúdos: filtros por categoria + cards + paginação. */
function render_conteudos_block(): string
{
    $cat = preg_replace('/[^a-z0-9\-]/', '', strtolower(get('cat')));
    $page = max(1, (int)get('pagina', '1'));
    $cats = public_categories();
    if ($cat !== '' && !in_array($cat, array_column($cats, 'slug'), true)) { $cat = ''; }
    $f = '';
    if ($cats) {
        $f = '<div class="filters" role="group" aria-label="Filtrar conteúdos"><button class="chip" type="button" data-cat="" aria-pressed="' . ($cat === '' ? 'true' : 'false') . '">Todos</button>';
        foreach ($cats as $c) {
            $f .= '<button class="chip" type="button" data-cat="' . e($c['slug']) . '" aria-pressed="' . ($cat === $c['slug'] ? 'true' : 'false') . '">' . e($c['name']) . '</button>';
        }
        $f .= '</div>';
    }
    $in = render_posts_inner($cat, $page);
    return $f . '<div data-posts-list>' . $in['cards'] . '</div><div data-posts-pager>' . $in['pager'] . '</div>';
}

function reading_minutes(string $html): int
{
    $words = str_word_count(strip_tags($html), 0, 'ÀÁÂÃÇÉÊÍÓÔÕÚàáâãçéêíóôõú');
    return max(1, (int)ceil($words / 200));
}

/** Página completa de um artigo, no visual do site. $preview = true mostra a faixa de pré-visualização. */
function render_article_page(array $a, bool $preview = false): void
{
    security_headers();
    $S = site_settings();
    $h = base_path() . '/';
    $title = trim((string)($a['seo_title'] ?? '')) !== '' ? (string)$a['seo_title'] : (string)$a['title'];
    $desc = trim((string)($a['seo_description'] ?? '')) !== '' ? (string)$a['seo_description'] : (trim((string)($a['summary'] ?? '')) !== '' ? (string)$a['summary'] : plain_excerpt((string)$a['content'], 160));
    $canon = abs_url('conteudos/' . $a['slug']);
    $cover = media_url($a['cover'] ?? '');
    $og = $cover !== '' ? ((preg_match('~^https?://~', $cover)) ? $cover : abs_url(ltrim(substr($cover, strlen(base_path())), '/'))) : abs_url('img/cba-fachada-logo.jpg');
    $full = $title . ' · MB Consultoria e Assessoria Política';
    $iso = $a['published_at'] ? date('c', strtotime((string)$a['published_at'])) : date('c');
    $related = $preview ? [] : related_articles($a);
    $sameCat = $preview ? [] : category_articles($a, 3);
    if ($sameCat) { $ids = array_map(fn($x) => (int)$x['id'], $sameCat); $related = array_values(array_filter($related, fn($x) => !in_array((int)$x['id'], $ids, true))); }
    $latest = $preview ? [] : latest_articles((int)$a['id'], 4);
    $inlineBox = '<aside class="art-inline"><div><b>Quer aplicar isso na sua comunicação?</b><span>Converse com a MB sobre estratégia e posicionamento.</span></div><a class="btn btn-gold" href="' . e($h) . '#contato">Falar com a MB</a></aside>';
    $inlineHtml = inject_after_paragraph((string)$a['content'], $inlineBox);
    $shareText = rawurlencode($a['title'] . ' ' . $canon);
    $ld = json_encode([
        '@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => $a['title'], 'description' => $desc,
        'image' => [$og], 'datePublished' => $iso, 'dateModified' => date('c', strtotime((string)($a['updated_at'] ?? $iso))),
        'author' => ['@type' => 'Person', 'name' => ($a['author'] ?: 'MB Consultoria')],
        'publisher' => ['@type' => 'Organization', 'name' => 'MB Consultoria e Assessoria Política', 'logo' => ['@type' => 'ImageObject', 'url' => abs_url('img/logo-bloco.png')]],
        'mainEntityOfPage' => $canon,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG);
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($full) ?></title>
<meta name="description" content="<?= e(limit_text($desc, 200)) ?>">
<?php if ($preview): ?><meta name="robots" content="noindex,nofollow"><?php else: ?><link rel="canonical" href="<?= e($canon) ?>"><?php endif; ?>
<meta property="og:type" content="article">
<meta property="og:site_name" content="MB Consultoria e Assessoria Política">
<meta property="og:locale" content="pt_BR">
<meta property="og:title" content="<?= e($title) ?>">
<meta property="og:description" content="<?= e(limit_text($desc, 200)) ?>">
<meta property="og:url" content="<?= e($canon) ?>">
<meta property="og:image" content="<?= e($og) ?>">
<meta property="article:published_time" content="<?= e($iso) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($title) ?>">
<meta name="twitter:description" content="<?= e(limit_text($desc, 200)) ?>">
<meta name="twitter:image" content="<?= e($og) ?>">
<script type="application/ld+json"><?= $ld ?></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(url('css/site.css')) ?>">
<link rel="stylesheet" href="<?= e(url('css/cms.css')) ?>">
</head>
<body class="art-page">
<?php if ($preview): ?><div class="preview-bar">Pré-visualização — este artigo ainda não está publicado.</div><?php endif; ?>
<?php include __DIR__ . '/partials/header.php'; ?>
<main class="art-main">
  <div class="ph-wrap">
    <header class="ph-hdr art-hero<?= $cover === '' ? ' hatch' : '' ?>">
      <?php if ($cover !== ''): ?><img class="art-hero-img" src="<?= e($cover) ?>" alt=""><?php else: ?><img class="hero-mark" src="<?= e(url('img/logo-simbolo.png')) ?>" alt=""><?php endif; ?>
      <div class="art-hero-in">
        <a class="art-back" href="<?= e($h) ?>#conteudos"><?= icon('arrow-left') ?>Conteúdos e Análises</a>
        <?php if (!empty($a['cat_name'])): ?><span class="pill gold"><?= e($a['cat_name']) ?></span><?php endif; ?>
        <h1 class="art-h1"><?= e($a['title']) ?></h1>
        <div class="art-meta">
          <?php if (!empty($a['author'])): ?><span><?= icon('user') ?><?= e($a['author']) ?></span><?php endif; ?>
          <?php if (!empty($a['published_at'])): ?><span><?= icon('calendar') ?><?= e(fmt_date($a['published_at'])) ?></span><?php endif; ?>
          <span><?= icon('clock') ?><?= reading_minutes((string)$a['content']) ?> min de leitura</span>
        </div>
      </div>
    </header>
  </div>

  <div class="blk art-body">
    <div class="wrap">
      <div class="art-grid">
        <article class="art-col">
          <?php if (trim((string)($a['summary'] ?? '')) !== ''): ?><p class="art-lead"><?= e($a['summary']) ?></p><?php endif; ?>
          <div class="art-content"><?= $inlineHtml /* já sanitizado ao salvar e na pré-visualização */ ?></div>

          <div class="art-take">
            <div class="label"><i></i><i></i>Próximo passo</div>
            <h3>Sua comunicação política precisa de método?</h3>
            <p>Converse com a MB e descubra como transformar presença em confiança — e confiança em resultado.</p>
            <a class="btn btn-navy" href="<?= e($h) ?>#contato">Agende uma conversa <?= icon('arrow-right') ?></a>
          </div>

          <div class="art-share">
            <b>Compartilhe</b>
            <div class="art-share-btns">
              <a href="https://wa.me/?text=<?= $shareText ?>" target="_blank" rel="noopener" aria-label="Compartilhar no WhatsApp"><?= icon('whatsapp') ?></a>
              <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($canon) ?>" target="_blank" rel="noopener" aria-label="Compartilhar no Facebook"><?= icon('facebook') ?></a>
              <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= rawurlencode($canon) ?>" target="_blank" rel="noopener" aria-label="Compartilhar no LinkedIn"><?= icon('linkedin') ?></a>
              <a href="https://twitter.com/intent/tweet?text=<?= rawurlencode($a['title']) ?>&url=<?= rawurlencode($canon) ?>" target="_blank" rel="noopener" aria-label="Compartilhar no X"><?= icon('x') ?></a>
              <button type="button" data-copy="<?= e($canon) ?>" aria-label="Copiar o link"><?= icon('link') ?><span>Copiar link</span></button>
            </div>
          </div>
        </article>

        <aside class="art-side">
          <div class="side-box side-cta hatch">
            <div class="label light"><i></i><i></i>MB Consultoria</div>
            <h3>Estratégia, imagem e posicionamento político.</h3>
            <p>Fale com quem entende de Cuiabá e Brasília.</p>
            <a class="btn btn-gold" href="<?= e($h) ?>#contato">Falar com a MB</a>
          </div>
<?php if ($latest): ?>
          <div class="side-box">
            <h4>Mais recentes</h4>
            <ul class="side-list">
<?php foreach ($latest as $l): ?>
              <li><a href="<?= e(article_url($l)) ?>"><?php if (!empty($l['cat_name'])): ?><small><?= e($l['cat_name']) ?></small><?php endif; ?><span><?= e($l['title']) ?></span></a></li>
<?php endforeach; ?>
            </ul>
          </div>
<?php endif; ?>
        </aside>
      </div>
    </div>
  </div>

<?php if ($sameCat): ?>
  <div class="blk" style="padding-top:0">
    <div class="wrap">
      <div class="sec-head"><div class="label"><i></i><i></i>Na mesma categoria</div><h2>Mais em <?= e($a['cat_name']) ?></h2></div>
      <div class="posts"><?= implode('', array_map('render_post_card', $sameCat)) ?></div>
    </div>
  </div>
<?php endif; ?>
<?php if ($related): ?>
  <div class="blk" style="padding-top:0">
    <div class="wrap">
      <div class="sec-head"><div class="label"><i></i><i></i>Continue lendo</div><h2>Artigos relacionados</h2></div>
      <div class="posts"><?= implode('', array_map('render_post_card', $related)) ?></div>
    </div>
  </div>
<?php endif; ?>

<?php include __DIR__ . '/partials/cta.php'; ?>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
<script defer src="<?= e(url('js/cms.js')) ?>"></script>
</body>
</html>
<?php
}
