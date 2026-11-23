<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login();
$admin = $u['role'] === 'admin';

$totalLeads = $newWeek = $newOnes = 0; $lastLeads = [];
if ($admin) {
    $totalLeads = (int)val('SELECT COUNT(*) FROM leads');
    $newWeek = (int)val('SELECT COUNT(*) FROM leads WHERE created_at >= :d', [':d' => date('Y-m-d H:i:s', strtotime('-7 days'))]);
    $newOnes = (int)val("SELECT COUNT(*) FROM leads WHERE status = 'novo'");
    $lastLeads = rows('SELECT id, name, email, phone, subject, status, created_at FROM leads ORDER BY created_at DESC, id DESC LIMIT 5');
}
$totalArt = (int)val('SELECT COUNT(*) FROM articles');
$pubArt = (int)val("SELECT COUNT(*) FROM articles WHERE status IN ('publicado','agendado') AND published_at <= :n", [':n' => now()]);
$lastArt = rows('SELECT a.id, a.title, a.status, a.published_at, c.name AS cat FROM articles a LEFT JOIN categories c ON c.id = a.category_id ORDER BY a.updated_at DESC, a.id DESC LIMIT 5');
$mailWarn = $admin ? (int)val('SELECT COUNT(*) FROM leads WHERE mail_sent = 0 AND created_at >= :d', [':d' => date('Y-m-d H:i:s', strtotime('-7 days'))]) : 0;

admin_head('Início', 'index', $u);
?>
<div class="ph">
  <div><h1>Olá, <?= e(explode(' ', $u['name'])[0]) ?></h1><p>Resumo do site da MB Consultoria.</p></div>
  <div class="actions"><a class="btn gold" href="artigo-editar.php"><?= a_icon('plus') ?>Novo artigo</a></div>
</div>

<?php if ($mailWarn > 0): ?><div class="alert info"><?= $mailWarn ?> contato<?= $mailWarn > 1 ? 's' : '' ?> dos últimos 7 dias não gerou<?= $mailWarn > 1 ? '' : '' ?> e-mail de aviso. Confira <a href="configuracoes.php">Configurações &gt; E-mail</a>. Os contatos estão salvos normalmente em Leads.</div><?php endif; ?>

<div class="stats">
<?php if ($admin): ?>
  <div class="stat hl"><b><?= $totalLeads ?></b><span>Leads recebidos no total</span></div>
  <div class="stat"><b><?= $newWeek ?></b><span>Leads novos nos últimos 7 dias</span></div>
  <div class="stat"><b><?= $newOnes ?></b><span>Aguardando contato</span></div>
<?php endif; ?>
  <div class="stat"><b><?= $pubArt ?></b><span>Artigos no ar (de <?= $totalArt ?>)</span></div>
</div>

<div class="grid2">
<?php if ($admin): ?>
  <section class="card">
    <h2>Últimos leads</h2>
    <?php if (!$lastLeads): ?><p class="empty">Quando alguém preencher o formulário do site, o contato aparece aqui.</p><?php else: ?>
    <div class="tbl-w"><table class="tbl stack"><thead><tr><th>Contato</th><th>Data</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($lastLeads as $l): ?>
      <tr><td data-l="Contato"><a class="t" href="lead.php?id=<?= (int)$l['id'] ?>"><?= e($l['name']) ?></a><small><?= e($l['phone'] ?: $l['email']) ?></small></td>
        <td data-l="Data"><?= e(fmt_date($l['created_at'], 'd/m H:i')) ?></td>
        <td data-l="Status"><span class="badge b-<?= e($l['status']) ?>"><?= e(LEAD_STATUS[$l['status']] ?? $l['status']) ?></span></td></tr>
    <?php endforeach; ?></tbody></table></div>
    <p style="margin:14px 0 0"><a class="btn ghost sm" href="leads.php">Ver todos os leads</a></p>
    <?php endif; ?>
  </section>
<?php endif; ?>
  <section class="card">
    <h2>Últimos artigos</h2>
    <?php if (!$lastArt): ?><p class="empty">Nenhum artigo ainda. Que tal escrever o primeiro?</p><?php else: ?>
    <div class="tbl-w"><table class="tbl stack"><thead><tr><th>Título</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($lastArt as $a): [$sk, $sl] = effective_status($a); ?>
      <tr><td data-l="Título"><a class="t" href="artigo-editar.php?id=<?= (int)$a['id'] ?>"><?= e($a['title']) ?></a><small><?= e($a['cat'] ?: 'Sem categoria') ?></small></td>
        <td data-l="Status"><span class="badge b-<?= e($sk) ?>"><?= e($sl) ?></span></td></tr>
    <?php endforeach; ?></tbody></table></div>
    <p style="margin:14px 0 0"><a class="btn ghost sm" href="artigos.php">Ver todos os artigos</a></p>
    <?php endif; ?>
  </section>
</div>
<?php admin_foot();
