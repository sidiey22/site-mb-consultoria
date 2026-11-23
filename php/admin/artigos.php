<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    if (post('acao') === 'excluir') {
        $id = (int)post('id');
        $a = row('SELECT id, title FROM articles WHERE id = :id', [':id' => $id]);
        if ($a) { q('DELETE FROM articles WHERE id = :id', [':id' => $id]); flash('ok', 'Artigo "' . $a['title'] . '" excluído.'); }
    }
    redirect('artigos.php?' . http_build_query(array_filter(['q' => get('q'), 'status' => get('status'), 'cat' => get('cat'), 'p' => get('p')])));
}

$q = get('q'); $st = get('status'); $cat = (int)get('cat'); $page = max(1, (int)get('p', '1'));
$where = ['1=1']; $p = [];
if ($q !== '') { $where[] = '(a.title LIKE :q1 OR a.summary LIKE :q2)'; $p[':q1'] = '%' . $q . '%'; $p[':q2'] = '%' . $q . '%'; }
if ($cat > 0) { $where[] = 'a.category_id = :cat'; $p[':cat'] = $cat; }
if ($st === 'rascunho') { $where[] = "a.status = 'rascunho'"; }
elseif ($st === 'publicado') { $where[] = "a.status IN ('publicado','agendado') AND a.published_at <= :now"; $p[':now'] = now(); }
elseif ($st === 'agendado') { $where[] = "a.status = 'agendado' AND a.published_at > :now"; $p[':now'] = now(); }
$W = implode(' AND ', $where);

$total = (int)val("SELECT COUNT(*) FROM articles a WHERE $W", $p);
$pg = paginate($total, $page, 15);
$list = rows("SELECT a.id, a.title, a.slug, a.status, a.published_at, a.featured, a.author, c.name AS cat FROM articles a LEFT JOIN categories c ON c.id = a.category_id WHERE $W ORDER BY a.updated_at DESC, a.id DESC LIMIT " . (int)$pg['per'] . ' OFFSET ' . (int)$pg['offset'], $p);
$cats = rows('SELECT id, name FROM categories ORDER BY name');
$keep = array_filter(['q' => $q, 'status' => $st, 'cat' => $cat ?: '']);

admin_head('Artigos', 'artigos', $u);
?>
<div class="ph">
  <div><h1>Artigos</h1><p><?= $total ?> artigo<?= $total === 1 ? '' : 's' ?><?= $keep ? ' encontrado' . ($total === 1 ? '' : 's') : '' ?>.</p></div>
  <div class="actions"><a class="btn gold" href="artigo-editar.php"><?= a_icon('plus') ?>Novo artigo</a></div>
</div>

<form class="card filters" method="get">
  <div class="fld grow"><label for="q">Buscar</label><input id="q" name="q" type="search" placeholder="Título ou resumo" value="<?= e($q) ?>"></div>
  <div class="fld"><label for="status">Status</label><select id="status" name="status"><option value="">Todos</option>
    <?php foreach (ART_STATUS as $k => $lbl): ?><option value="<?= $k ?>"<?= $st === $k ? ' selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?></select></div>
  <div class="fld"><label for="cat">Categoria</label><select id="cat" name="cat"><option value="">Todas</option>
    <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>"<?= $cat === (int)$c['id'] ? ' selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
  <div class="fld"><button class="btn" type="submit"><?= a_icon('search') ?>Filtrar</button></div>
  <?php if ($keep): ?><div class="fld"><a class="btn ghost" href="artigos.php">Limpar</a></div><?php endif; ?>
</form>

<section class="card" style="margin-top:14px">
<?php if (!$list): ?>
  <p class="empty"><?= $keep ? 'Nenhum artigo com esses filtros.' : 'Você ainda não criou nenhum artigo.' ?></p>
<?php else: ?>
  <div class="tbl-w"><table class="tbl stack">
    <thead><tr><th>Título</th><th>Categoria</th><th>Data</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($list as $a): [$sk, $sl] = effective_status($a); ?>
      <tr>
        <td data-l="Título"><a class="t" href="artigo-editar.php?id=<?= (int)$a['id'] ?>"><?= e($a['title']) ?></a>
          <small><?= $a['featured'] ? '<span class="badge b-star">Destaque</span> ' : '' ?><?= e($a['author'] ?: '') ?></small></td>
        <td data-l="Categoria"><?= e($a['cat'] ?: '—') ?></td>
        <td data-l="Data"><?= e($a['published_at'] ? fmt_date($a['published_at'], 'd/m/Y H:i') : '—') ?></td>
        <td data-l="Status"><span class="badge b-<?= e($sk) ?>"><?= e($sl) ?></span></td>
        <td><div class="acts">
          <a class="icon-btn" href="artigo-editar.php?id=<?= (int)$a['id'] ?>" title="Editar" aria-label="Editar <?= e($a['title']) ?>"><?= a_icon('edit') ?></a>
          <?php if ($sk === 'publicado'): ?><a class="icon-btn" href="<?= e(url('conteudos/' . $a['slug'])) ?>" target="_blank" rel="noopener" title="Ver no site" aria-label="Ver no site"><?= a_icon('eye') ?></a><?php endif; ?>
          <form method="post" data-confirm="Excluir o artigo &quot;<?= e($a['title']) ?>&quot;? Essa ação não pode ser desfeita.">
            <?= csrf_field() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
            <button class="icon-btn danger" type="submit" title="Excluir" aria-label="Excluir <?= e($a['title']) ?>"><?= a_icon('trash') ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></div>
  <?= admin_pager($pg, $keep) ?>
<?php endif; ?>
</section>
<?php admin_foot();
