<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login(true);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $acao = post('acao'); $id = (int)post('id'); $name = mb_substr(post('nome'), 0, 80);
    if ($acao === 'criar') {
        if (mb_strlen($name) < 2) { flash('err', 'Digite o nome da categoria.'); }
        elseif ((int)val('SELECT COUNT(*) FROM categories WHERE LOWER(name) = :n', [':n' => mb_strtolower($name)]) > 0) { flash('err', 'Já existe uma categoria com esse nome.'); }
        else { insert('categories', ['name' => $name, 'slug' => unique_slug('categories', slugify($name)), 'created_at' => now()]); flash('ok', 'Categoria criada.'); }
    } elseif ($acao === 'renomear' && $id) {
        if (mb_strlen($name) < 2) { flash('err', 'Digite o nome da categoria.'); }
        else { q('UPDATE categories SET name = :n WHERE id = :id', [':n' => $name, ':id' => $id]); flash('ok', 'Categoria atualizada.'); }
    } elseif ($acao === 'excluir' && $id) {
        q('UPDATE articles SET category_id = NULL WHERE category_id = :id', [':id' => $id]);
        q('DELETE FROM categories WHERE id = :id', [':id' => $id]);
        flash('ok', 'Categoria excluída. Os artigos dela ficaram sem categoria.');
    }
    redirect('categorias.php');
}

$cats = rows('SELECT c.id, c.name, c.slug, (SELECT COUNT(*) FROM articles a WHERE a.category_id = c.id) AS n FROM categories c ORDER BY c.name');
admin_head('Categorias', 'categorias', $u);
?>
<div class="ph"><div><h1>Categorias</h1><p>Elas aparecem como filtros na página Conteúdos do site.</p></div></div>

<section class="card">
  <h2>Nova categoria</h2>
  <form method="post" class="filters" style="margin:0">
    <?= csrf_field() ?><input type="hidden" name="acao" value="criar">
    <div class="fld grow" style="margin:0"><label for="nome">Nome</label><input id="nome" name="nome" type="text" maxlength="80" required placeholder="Ex.: Gestão pública"></div>
    <div class="fld" style="margin:0"><button class="btn gold" type="submit"><?= a_icon('plus') ?>Criar categoria</button></div>
  </form>
</section>

<section class="card">
  <h2>Categorias existentes</h2>
<?php if (!$cats): ?><p class="empty">Nenhuma categoria ainda.</p><?php else: ?>
  <div class="tbl-w"><table class="tbl stack"><thead><tr><th>Nome</th><th>Artigos</th><th></th></tr></thead><tbody>
  <?php foreach ($cats as $c): ?>
    <tr>
      <td data-l="Nome">
        <form method="post" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
          <?= csrf_field() ?><input type="hidden" name="acao" value="renomear"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <input type="text" name="nome" value="<?= e($c['name']) ?>" maxlength="80" required aria-label="Nome da categoria" style="max-width:300px">
          <button class="btn ghost sm" type="submit">Salvar nome</button>
        </form>
      </td>
      <td data-l="Artigos"><?= (int)$c['n'] ?></td>
      <td><div class="acts">
        <form method="post" data-confirm="Excluir a categoria &quot;<?= e($c['name']) ?>&quot;? Os artigos dela ficam sem categoria.">
          <?= csrf_field() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <button class="icon-btn danger" type="submit" aria-label="Excluir <?= e($c['name']) ?>" title="Excluir"><?= a_icon('trash') ?></button>
        </form></div></td>
    </tr>
  <?php endforeach; ?></tbody></table></div>
<?php endif; ?>
</section>
<?php admin_foot();
