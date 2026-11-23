<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login();

$id = (int)get('id');
$art = $id ? row('SELECT * FROM articles WHERE id = :id', [':id' => $id]) : null;
if ($id && !$art) { flash('err', 'Artigo não encontrado.'); redirect('artigos.php'); }

$cats = rows('SELECT id, name FROM categories ORDER BY name');
$d = $art ?? [
    'title' => '', 'slug' => '', 'summary' => '', 'cover' => '', 'content' => '', 'category_id' => null, 'author' => $u['name'],
    'published_at' => null, 'status' => 'rascunho', 'featured' => 0, 'seo_title' => '', 'seo_description' => '',
];
$errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $d['title'] = mb_substr(post('title'), 0, 200);
    $d['summary'] = mb_substr(post('summary'), 0, 400);
    $d['content'] = sanitize_html((string)($_POST['content'] ?? ''));
    $d['category_id'] = (int)post('category_id') ?: null;
    $d['author'] = mb_substr(post('author'), 0, 120);
    $d['status'] = array_key_exists(post('status'), ART_STATUS) ? post('status') : 'rascunho';
    $d['featured'] = post('featured') === '1' ? 1 : 0;
    $d['seo_title'] = mb_substr(post('seo_title'), 0, 160);
    $d['seo_description'] = mb_substr(post('seo_description'), 0, 320);
    $slugIn = post('slug');
    $d['slug'] = slugify($slugIn !== '' ? $slugIn : $d['title']);

    $pubIn = post('published_at');
    $pubTs = $pubIn !== '' ? strtotime(str_replace('T', ' ', $pubIn)) : false;
    if ($pubIn !== '' && !$pubTs) { $errors[] = 'A data de publicação é inválida.'; }
    $d['published_at'] = $pubTs ? date('Y-m-d H:i:00', $pubTs) : null;

    if ($d['title'] === '') { $errors[] = 'Escreva o título do artigo.'; }
    if (trim(strip_tags($d['content'], '<img><iframe>')) === '') { $errors[] = 'O texto do artigo está vazio.'; }

    // status x data
    if ($d['status'] === 'agendado') {
        if (!$d['published_at'] || $d['published_at'] <= now()) { $errors[] = 'Para agendar, escolha uma data e hora no futuro.'; }
    } elseif ($d['status'] === 'publicado') {
        if (!$d['published_at']) { $d['published_at'] = now(); }
        elseif ($d['published_at'] > now()) { $d['status'] = 'agendado'; flash('info', 'A data escolhida é futura, então o artigo foi agendado.'); }
    }

    // capa
    $cover = (string)($art['cover'] ?? '');
    if (post('cover_remove') === '1') { $cover = ''; }
    if (!empty($_FILES['cover_file']['name'])) {
        $r = save_upload($_FILES['cover_file'], 1600);
        if ($r['ok']) { $cover = $r['path']; } else { $errors[] = 'Imagem de capa: ' . $r['error']; }
    }
    $d['cover'] = $cover;

    if (!$errors) {
        $d['slug'] = unique_slug('articles', $d['slug'], (int)$id);
        $row = [
            'title' => $d['title'], 'slug' => $d['slug'], 'summary' => $d['summary'], 'cover' => $d['cover'] ?: null, 'content' => $d['content'],
            'category_id' => $d['category_id'], 'author' => $d['author'], 'published_at' => $d['published_at'], 'status' => $d['status'],
            'featured' => $d['featured'], 'seo_title' => $d['seo_title'], 'seo_description' => $d['seo_description'], 'updated_at' => now(),
        ];
        if ($art) { update('articles', $id, $row); }
        else { $row['user_id'] = $u['id']; $row['created_at'] = now(); $id = insert('articles', $row); }
        $msg = ['rascunho' => 'Rascunho salvo.', 'publicado' => 'Artigo publicado!', 'agendado' => 'Artigo agendado para ' . fmt_date($d['published_at'], 'd/m/Y \à\s H:i') . '.'][$d['status']];
        flash('ok', $msg);
        redirect('artigo-editar.php?id=' . $id);
    }
}

$isNew = !$id;
$pubVal = !empty($d['published_at']) ? date('Y-m-d\TH:i', strtotime((string)$d['published_at'])) : '';
$slugAuto = $isNew || $d['slug'] === '';
$extra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">';
admin_head($isNew ? 'Novo artigo' : 'Editar artigo', 'artigos', $u, $extra);
?>
<div class="ph">
  <div><h1><?= $isNew ? 'Novo artigo' : 'Editar artigo' ?></h1><p><a href="artigos.php">&larr; Voltar para a lista</a></p></div>
  <?php if (!$isNew && effective_status($d + ['status' => $d['status']])[0] === 'publicado'): ?>
    <div class="actions"><a class="btn ghost" href="<?= e(url('conteudos/' . $d['slug'])) ?>" target="_blank" rel="noopener"><?= a_icon('eye') ?>Ver no site</a></div>
  <?php endif; ?>
</div>

<?php foreach ($errors as $er): ?><div class="alert err" role="alert"><?= e($er) ?></div><?php endforeach; ?>

<form id="artForm" method="post" enctype="multipart/form-data" action="artigo-editar.php<?= $isNew ? '' : '?id=' . (int)$id ?>">
  <?= csrf_field() ?>
  <input type="hidden" name="cover_current" value="<?= e($d['cover'] ?? '') ?>">
  <input type="hidden" name="content" id="contentField">
  <div class="ed-grid">
    <div>
      <div class="card">
        <div class="fld"><label for="title">Título</label><input id="title" name="title" type="text" maxlength="200" required value="<?= e($d['title']) ?>" placeholder="Título do artigo"></div>
        <div class="fld"><label for="slug">Endereço do artigo</label>
          <input id="slug" name="slug" type="text" maxlength="110" value="<?= e($d['slug']) ?>" placeholder="gerado automaticamente a partir do título" data-auto="<?= $slugAuto ? '1' : '0' ?>">
          <small>Fica assim: <?= e(abs_url('conteudos/')) ?><b id="slugShow"><?= e($d['slug'] ?: 'nome-do-artigo') ?></b></small></div>
        <div class="fld"><label for="summary">Resumo</label><textarea id="summary" name="summary" maxlength="400" rows="3" data-count="400" placeholder="Duas frases que aparecem no card do artigo e nos compartilhamentos."><?= e($d['summary'] ?? '') ?></textarea><span class="cnt" data-for="summary"></span></div>
        <div class="fld"><span class="lbl">Texto do artigo</span>
          <div id="editor"><?= $d['content'] ?></div>
          <small>Use os botões acima do texto para subtítulos, negrito, listas, links, imagens e vídeos do YouTube (cole o endereço do vídeo).</small></div>
      </div>
    </div>

    <aside class="ed-side">
      <div class="card">
        <h3 style="margin-bottom:14px">Publicação</h3>
        <div class="fld"><label for="status">Status</label>
          <select id="status" name="status">
            <?php foreach (ART_STATUS as $k => $lbl): ?><option value="<?= $k ?>"<?= $d['status'] === $k ? ' selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
          </select>
          <small id="stHint">Rascunho não aparece no site. Publicado aparece na hora. Agendado aparece na data escolhida.</small></div>
        <div class="fld"><label for="published_at">Data e hora</label><input id="published_at" name="published_at" type="datetime-local" value="<?= e($pubVal) ?>"><small>Deixe em branco para usar o momento da publicação.</small></div>
        <label class="chk" style="margin-bottom:16px"><input type="checkbox" name="featured" value="1"<?= !empty($d['featured']) ? ' checked' : '' ?>> Destacar este artigo (aparece primeiro)</label>
        <div class="form-actions">
          <button class="btn gold" type="submit">Salvar</button>
          <button class="btn ghost" type="button" id="btnPreview"><?= a_icon('eye') ?>Pré-visualizar</button>
        </div>
      </div>

      <div class="card">
        <h3 style="margin-bottom:14px">Categoria e autor</h3>
        <div class="fld"><label for="category_id">Categoria</label>
          <select id="category_id" name="category_id"><option value="0">Sem categoria</option>
            <?php foreach ($cats as $c): ?><option value="<?= (int)$c['id'] ?>"<?= (int)($d['category_id'] ?? 0) === (int)$c['id'] ? ' selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="fld"><label for="author">Autor</label><input id="author" name="author" type="text" maxlength="120" value="<?= e($d['author'] ?? '') ?>"></div>
      </div>

      <div class="card">
        <h3 style="margin-bottom:14px">Imagem de capa</h3>
        <div class="cover-box">
          <img id="coverImg" src="<?= e(media_url($d['cover'] ?? '')) ?>" alt="" <?= empty($d['cover']) ? 'hidden' : '' ?>>
          <input id="cover_file" name="cover_file" type="file" accept="image/jpeg,image/png,image/webp" aria-label="Escolher imagem de capa">
          <small style="display:block;margin-top:8px">JPG, PNG ou WEBP, até <?= (int)round(UPLOAD_MAX_BYTES / 1048576) ?> MB. Melhor na horizontal (16:9).</small>
        </div>
        <?php if (!empty($d['cover'])): ?><label class="chk" style="margin-top:12px"><input type="checkbox" name="cover_remove" value="1"> Remover a capa atual</label><?php endif; ?>
      </div>

      <div class="card">
        <h3 style="margin-bottom:14px">Aparência no Google e nas redes</h3>
        <div class="fld"><label for="seo_title">Título para busca (SEO)</label><input id="seo_title" name="seo_title" type="text" maxlength="160" data-count="60" value="<?= e($d['seo_title'] ?? '') ?>" placeholder="Se vazio, usa o título do artigo"><span class="cnt" data-for="seo_title"></span></div>
        <div class="fld"><label for="seo_description">Descrição para busca</label><textarea id="seo_description" name="seo_description" maxlength="320" rows="3" data-count="160" placeholder="Se vazia, usa o resumo"><?= e($d['seo_description'] ?? '') ?></textarea><span class="cnt" data-for="seo_description"></span></div>
      </div>
    </aside>
  </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
(function () {
  var CSRF = <?= json_encode(csrf_token()) ?>, MAX = <?= (int)UPLOAD_MAX_BYTES ?>;
  var form = document.getElementById('artForm'), field = document.getElementById('contentField');
  var quill = null;
  if (window.Quill) {
    quill = new Quill('#editor', {
      theme: 'snow', placeholder: 'Escreva o artigo aqui…',
      modules: { toolbar: { container: [[{ header: [2, 3, 4, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote'], [{ align: [] }], ['link', 'image', 'video'], ['clean']], handlers: { image: pickImage } } }
    });
  } else {
    var ed = document.getElementById('editor'); ed.contentEditable = 'true'; ed.style.cssText = 'min-height:300px;border:1px solid #cfd3e0;border-radius:12px;padding:14px';
  }
  function html() {
    if (!quill) { return document.getElementById('editor').innerHTML; }
    var r = quill.root.innerHTML;
    return (quill.getText().trim() === '' && !quill.root.querySelector('img,iframe')) ? '' : r;
  }
  function pickImage() {
    var i = document.createElement('input'); i.type = 'file'; i.accept = 'image/jpeg,image/png,image/webp';
    i.onchange = function () {
      var f = i.files[0]; if (!f) { return; }
      if (f.size > MAX) { alert('A imagem é grande demais. O máximo é ' + Math.round(MAX / 1048576) + ' MB.'); return; }
      var fd = new FormData(); fd.append('imagem', f); fd.append('_csrf', CSRF);
      fetch('upload.php', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'fetch', 'X-CSRF-Token': CSRF } })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          if (!j.ok) { alert(j.error || 'Não foi possível enviar a imagem.'); return; }
          var sel = quill.getSelection(true); quill.insertEmbed(sel.index, 'image', j.url, 'user'); quill.setSelection(sel.index + 1);
        })
        .catch(function () { alert('Não foi possível enviar a imagem. Tente de novo.'); });
    };
    i.click();
  }
  form.addEventListener('submit', function () { field.value = html(); });

  // endereço automático a partir do título
  var title = document.getElementById('title'), slug = document.getElementById('slug'), show = document.getElementById('slugShow');
  function slugify(s) { return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 110); }
  var auto = slug.getAttribute('data-auto') === '1';
  title.addEventListener('input', function () { if (auto) { slug.value = slugify(title.value); show.textContent = slug.value || 'nome-do-artigo'; } });
  slug.addEventListener('input', function () { auto = false; show.textContent = slugify(slug.value) || 'nome-do-artigo'; });
  slug.addEventListener('blur', function () { if (slug.value) { slug.value = slugify(slug.value); } });

  // pré-visualização sem salvar
  document.getElementById('btnPreview').addEventListener('click', function () {
    field.value = html();
    var a = form.action, t = form.target;
    form.action = 'artigo-previa.php'; form.target = '_blank'; form.submit();
    form.action = a; form.target = t;
  });

  // mostra a capa escolhida antes de enviar
  document.getElementById('cover_file').addEventListener('change', function () {
    var f = this.files[0], img = document.getElementById('coverImg');
    if (f && img) { img.src = URL.createObjectURL(f); img.hidden = false; }
  });
})();
</script>
<?php admin_foot();
