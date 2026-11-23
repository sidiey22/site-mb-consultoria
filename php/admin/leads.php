<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
require __DIR__ . '/_leads.php';
$u = require_login(true);

[$W, $P, $keep] = leads_filter();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $id = (int)post('id');
    if (post('acao') === 'status' && array_key_exists(post('st'), LEAD_STATUS)) {
        q('UPDATE leads SET status = :s, updated_at = :n WHERE id = :id', [':s' => post('st'), ':n' => now(), ':id' => $id]);
        flash('ok', 'Status atualizado.');
    } elseif (post('acao') === 'excluir') {
        q('DELETE FROM leads WHERE id = :id', [':id' => $id]);
        flash('ok', 'Lead excluído.');
    }
    parse_str((string)post('back'), $back);
    $back = array_intersect_key($back, array_flip(['q', 'status', 'origem', 'de', 'ate', 'p']));
    redirect('leads.php' . ($back ? '?' . http_build_query($back) : ''));
}

$page = max(1, (int)get('p', '1'));
$total = (int)val("SELECT COUNT(*) FROM leads WHERE $W", $P);
$pg = paginate($total, $page, 20);
$list = rows("SELECT id, name, email, phone, company, subject, form_name, utm_source, status, created_at FROM leads WHERE $W ORDER BY created_at DESC, id DESC LIMIT " . (int)$pg['per'] . ' OFFSET ' . (int)$pg['offset'], $P);
$origens = array_column(rows('SELECT DISTINCT form_name FROM leads ORDER BY form_name'), 'form_name');
$backQ = http_build_query($keep + ($pg['page'] > 1 ? ['p' => $pg['page']] : []));

admin_head('Leads', 'leads', $u);
?>
<div class="ph">
  <div><h1>Leads</h1><p><?= $total ?> contato<?= $total === 1 ? '' : 's' ?><?= $keep ? ' com esses filtros' : ' recebido' . ($total === 1 ? '' : 's') . ' pelo site' ?>.</p></div>
  <div class="actions"><a class="btn ghost" href="leads-csv.php<?= $keep ? '?' . e(http_build_query($keep)) : '' ?>"><?= a_icon('down') ?>Exportar CSV (Excel)</a></div>
</div>

<form class="card filters" method="get">
  <div class="fld grow"><label for="q">Buscar</label><input id="q" name="q" type="search" placeholder="Nome, e-mail, telefone ou mensagem" value="<?= e(get('q')) ?>"></div>
  <div class="fld"><label for="status">Status</label><select id="status" name="status"><option value="">Todos</option>
    <?php foreach (LEAD_STATUS as $k => $lbl): ?><option value="<?= $k ?>"<?= get('status') === $k ? ' selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?></select></div>
  <div class="fld"><label for="origem">Origem</label><select id="origem" name="origem"><option value="">Todas</option>
    <?php foreach ($origens as $o): ?><option value="<?= e($o) ?>"<?= get('origem') === $o ? ' selected' : '' ?>><?= e($o) ?></option><?php endforeach; ?></select></div>
  <div class="fld"><label for="de">De</label><input id="de" name="de" type="date" value="<?= e(get('de')) ?>"></div>
  <div class="fld"><label for="ate">Até</label><input id="ate" name="ate" type="date" value="<?= e(get('ate')) ?>"></div>
  <div class="fld"><button class="btn" type="submit"><?= a_icon('search') ?>Filtrar</button></div>
  <?php if ($keep): ?><div class="fld"><a class="btn ghost" href="leads.php">Limpar</a></div><?php endif; ?>
</form>

<section class="card" style="margin-top:14px">
<?php if (!$list): ?>
  <p class="empty"><?= $keep ? 'Nenhum lead com esses filtros.' : 'Quando alguém preencher o formulário do site, o contato aparece aqui.' ?></p>
<?php else: ?>
  <div class="tbl-w"><table class="tbl stack">
    <thead><tr><th>Data</th><th>Contato</th><th>Assunto</th><th>Origem</th><th>Status</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($list as $l): $wa = wa_link((string)$l['phone'], 'Olá, ' . $l['name'] . '! Aqui é da MB Consultoria e Assessoria Política. Recebemos seu contato.'); ?>
      <tr>
        <td data-l="Data"><?= e(fmt_date($l['created_at'], 'd/m/Y')) ?><small><?= e(fmt_date($l['created_at'], 'H:i')) ?></small></td>
        <td data-l="Contato"><a class="t" href="lead.php?id=<?= (int)$l['id'] ?>"><?= e($l['name']) ?></a><small><?= e($l['email']) ?><?= $l['phone'] ? ' · ' . e($l['phone']) : '' ?></small></td>
        <td data-l="Assunto"><?= e($l['subject'] ?: '—') ?><?= $l['company'] ? '<small>' . e($l['company']) . '</small>' : '' ?></td>
        <td data-l="Origem"><?= e($l['form_name']) ?><?= $l['utm_source'] ? '<small>' . e($l['utm_source']) . '</small>' : '' ?></td>
        <td data-l="Status">
          <form method="post">
            <?= csrf_field() ?><input type="hidden" name="acao" value="status"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><input type="hidden" name="back" value="<?= e($backQ) ?>">
            <select class="st b-<?= e($l['status']) ?>" name="st" onchange="this.form.submit()" aria-label="Status de <?= e($l['name']) ?>">
              <?php foreach (LEAD_STATUS as $k => $lbl): ?><option value="<?= $k ?>"<?= $l['status'] === $k ? ' selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?>
            </select>
          </form>
        </td>
        <td><div class="acts">
          <?php if ($wa): ?><a class="icon-btn" href="<?= e($wa) ?>" target="_blank" rel="noopener" title="Chamar no WhatsApp" aria-label="Chamar <?= e($l['name']) ?> no WhatsApp"><?= a_icon('wa') ?></a><?php endif; ?>
          <a class="icon-btn" href="lead.php?id=<?= (int)$l['id'] ?>" title="Abrir" aria-label="Abrir lead de <?= e($l['name']) ?>"><?= a_icon('eye') ?></a>
          <form method="post" data-confirm="Excluir o lead de <?= e($l['name']) ?>? Essa ação não pode ser desfeita.">
            <?= csrf_field() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><input type="hidden" name="back" value="<?= e($backQ) ?>">
            <button class="icon-btn danger" type="submit" title="Excluir" aria-label="Excluir lead de <?= e($l['name']) ?>"><?= a_icon('trash') ?></button>
          </form>
        </div></td>
      </tr>
    <?php endforeach; ?>
    </tbody></table></div>
  <?= admin_pager($pg, $keep) ?>
<?php endif; ?>
</section>
<?php admin_foot();
