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
<body>
<?php if ($preview): ?><div class="preview-bar">Pré-visualização — este artigo ainda não está publicado.</div><?php endif; ?>
<?php include __DIR__ . '/partials/header.php'; ?>
<main class="art-main">
  <div class="ph-wrap">
    <div class="ph-hdr solo hatch">
      <img class="hero-mark" src="<?= e(url('img/logo-simbolo.png')) ?>" alt="">
      <div class="ph-in">
        <div>
          <a class="art-back" href="<?= e($h) ?>#conteudos"><?= icon('arrow-left') ?>Conteúdos e Análises</a>
          <?php if (!empty($a['cat_name'])): ?><div class="label light"><i></i><i></i><?= e($a['cat_name']) ?></div><?php endif; ?>
          <h1 class="art-h1"><?= e($a['title']) ?></h1>
          <?php if (trim((string)($a['summary'] ?? '')) !== ''): ?><p class="lead"><?= e($a['summary']) ?></p><?php endif; ?>
          <div class="art-meta">
            <?php if (!empty($a['author'])): ?><span><?= icon('user') ?><?= e($a['author']) ?></span><?php endif; ?>
            <?php if (!empty($a['published_at'])): ?><span><?= icon('calendar') ?><?= e(fmt_date($a['published_at'])) ?></span><?php endif; ?>
            <span><?= icon('clock') ?><?= reading_minutes((string)$a['content']) ?> min de leitura</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="blk">
    <div class="wrap">
      <div class="big-card art-card">
        <?php if ($cover !== ''): ?><figure class="art-cover"><img src="<?= e($cover) ?>" alt="<?= e($a['title']) ?>"></figure><?php endif; ?>
        <div class="art-content"><?= $a['content'] /* já sanitizado ao salvar e na pré-visualização */ ?></div>
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
      </div>
    </div>
  </div>

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
