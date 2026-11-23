<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login(true);

$id = (int)get('id');
$l = row('SELECT * FROM leads WHERE id = :id', [':id' => $id]);
if (!$l) { flash('err', 'Lead não encontrado.'); redirect('leads.php'); }

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    if (post('acao') === 'excluir') {
        q('DELETE FROM leads WHERE id = :id', [':id' => $id]);
        flash('ok', 'Lead excluído.'); redirect('leads.php');
    }
    $st = array_key_exists(post('status'), LEAD_STATUS) ? post('status') : $l['status'];
    q('UPDATE leads SET status = :s, notes = :n, updated_at = :u WHERE id = :id', [':s' => $st, ':n' => mb_substr((string)($_POST['notes'] ?? ''), 0, 5000), ':u' => now(), ':id' => $id]);
    flash('ok', 'Lead atualizado.');
    redirect('lead.php?id=' . $id);
}

$wa = wa_link((string)$l['phone'], 'Olá, ' . $l['name'] . '! Aqui é da MB Consultoria e Assessoria Política. Recebemos seu contato' . ($l['subject'] ? ' sobre ' . $l['subject'] : '') . '.');
admin_head('Lead: ' . $l['name'], 'leads', $u);
?>
<div class="ph">
  <div><h1><?= e($l['name']) ?></h1><p><a href="leads.php">&larr; Voltar para os leads</a> · recebido em <?= e(fmt_date($l['created_at'], 'd/m/Y \à\s H:i')) ?></p></div>
  <div class="actions">
    <?php if ($wa): ?><a class="btn wa" href="<?= e($wa) ?>" target="_blank" rel="noopener"><?= a_icon('wa') ?>Chamar no WhatsApp</a><?php endif; ?>
    <a class="btn ghost" href="mailto:<?= e($l['email']) ?>"><?= a_icon('mail') ?>Enviar e-mail</a>
  </div>
</div>

<div class="ed-grid">
  <div>
    <section class="card">
      <h2>Dados do contato</h2>
      <dl class="kv">
        <dt>Nome</dt><dd><?= e($l['name']) ?></dd>
        <dt>E-mail</dt><dd><a href="mailto:<?= e($l['email']) ?>"><?= e($l['email']) ?></a></dd>
        <dt>Telefone / WhatsApp</dt><dd><?= e($l['phone'] ?: '—') ?></dd>
        <?php if ($l['company']): ?><dt>Instituição ou projeto</dt><dd><?= e($l['company']) ?></dd><?php endif; ?>
        <?php if ($l['city']): ?><dt>Cidade / Estado</dt><dd><?= e($l['city']) ?></dd><?php endif; ?>
        <dt>Assunto</dt><dd><?= e($l['subject'] ?: '—') ?></dd>
      </dl>
      <?php if (trim((string)$l['message']) !== ''): ?><h3 style="margin:22px 0 10px">Mensagem</h3><div class="msg-box"><?= e($l['message']) ?></div><?php endif; ?>
    </section>
    <section class="card">
      <h2>De onde veio</h2>
      <dl class="kv">
        <dt>Formulário</dt><dd><?= e($l['form_name']) ?></dd>
        <?php if ($l['page_url']): ?><dt>Página</dt><dd><?= e($l['page_url']) ?></dd><?php endif; ?>
        <?php foreach (['utm_source' => 'Origem (utm_source)', 'utm_medium' => 'Meio (utm_medium)', 'utm_campaign' => 'Campanha (utm_campaign)', 'utm_term' => 'Termo (utm_term)', 'utm_content' => 'Conteúdo (utm_content)'] as $k => $lbl): if ($l[$k]): ?><dt><?= e($lbl) ?></dt><dd><?= e($l[$k]) ?></dd><?php endif; endforeach; ?>
        <?php if ($l['referrer'] && $l['referrer'] !== '-'): ?><dt>Chegou por</dt><dd><?= e($l['referrer']) ?></dd><?php endif; ?>
        <dt>Aviso por e-mail</dt><dd><?= $l['mail_sent'] ? 'Enviado à equipe' : 'Não enviado' . ($l['mail_error'] ? ' — ' . e($l['mail_error']) : '') ?></dd>
      </dl>
    </section>
  </div>
  <aside class="ed-side">
    <form method="post" class="card">
      <?= csrf_field() ?>
      <h3 style="margin-bottom:14px">Acompanhamento</h3>
      <div class="fld"><label for="status">Status</label><select id="status" name="status"><?php foreach (LEAD_STATUS as $k => $lbl): ?><option value="<?= $k ?>"<?= $l['status'] === $k ? ' selected' : '' ?>><?= $lbl ?></option><?php endforeach; ?></select></div>
      <div class="fld"><label for="notes">Anotações internas</label><textarea id="notes" name="notes" rows="7" placeholder="Só a equipe vê isto. Ex.: liguei dia 10, pediu proposta."><?= e($l['notes'] ?? '') ?></textarea></div>
      <button class="btn gold" type="submit">Salvar</button>
    </form>
    <form method="post" class="card" data-confirm="Excluir este lead? Essa ação não pode ser desfeita.">
      <?= csrf_field() ?><input type="hidden" name="acao" value="excluir">
      <button class="btn danger" type="submit"><?= a_icon('trash') ?>Excluir lead</button>
    </form>
  </aside>
</div>
<?php admin_foot();
