<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
$u = require_login(true);

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_verify();
    $acao = post('acao');
    if ($acao === 'teste') {
        $dest = post('teste_para');
        if (!filter_var($dest, FILTER_VALIDATE_EMAIL)) { flash('err', 'Digite um e-mail válido para receber o teste.'); }
        else {
            $r = send_mail($dest, 'Teste de e-mail do painel MB', mail_layout('Teste de e-mail', '<p>Se você está lendo esta mensagem, o envio de e-mails do site está funcionando.</p>'));
            flash($r === true ? 'ok' : 'err', $r === true ? 'E-mail de teste enviado para ' . $dest . '. Confira a caixa de entrada (e o spam).' : 'Não foi possível enviar: ' . $r);
        }
        redirect('configuracoes.php');
    }

    $emails = array_filter(array_map('trim', preg_split('/[,;\s]+/', post('notify_emails')) ?: []));
    $bad = array_filter($emails, fn($x) => !filter_var($x, FILTER_VALIDATE_EMAIL));
    $errs = [];
    if ($bad) { $errs[] = 'E-mail inválido na lista de avisos: ' . implode(', ', $bad); }
    foreach (['instagram', 'linkedin', 'facebook', 'youtube'] as $k) {
        $v = post($k);
        if ($v !== '' && !preg_match('~^https?://~i', $v)) { $errs[] = 'O endereço de ' . ucfirst($k) . ' precisa começar com https://'; }
    }
    if (post('email') !== '' && !filter_var(post('email'), FILTER_VALIDATE_EMAIL)) { $errs[] = 'O e-mail público do site é inválido.'; }
    $secure = in_array(post('smtp_secure'), ['ssl', 'tls', 'none'], true) ? post('smtp_secure') : 'ssl';
    if ($errs) { foreach ($errs as $er) { flash('err', $er); } redirect('configuracoes.php'); }

    set_setting('notify_emails', implode(', ', $emails));
    foreach (['smtp_host', 'smtp_user', 'smtp_from', 'smtp_from_name', 'phone', 'email', 'instagram', 'linkedin', 'facebook', 'youtube'] as $k) { set_setting($k, mb_substr(post($k), 0, 200)); }
    set_setting('smtp_port', (string)max(1, min(65535, (int)post('smtp_port') ?: 465)));
    set_setting('smtp_secure', $secure);
    set_setting('smtp_verify', post('smtp_verify') === '1' ? '1' : '0');
    $wa = preg_replace('/\D+/', '', post('whatsapp')) ?? '';
    if ($wa !== '' && strlen($wa) <= 11) { $wa = '55' . ltrim($wa, '0'); }
    set_setting('whatsapp', $wa);
    if (post('smtp_pass') !== '') { set_setting('smtp_pass', (string)($_POST['smtp_pass'] ?? '')); }
    if (post('smtp_pass_clear') === '1') { set_setting('smtp_pass', ''); }
    flash('ok', 'Configurações salvas.');
    redirect('configuracoes.php');
}

$s = settings_all(true);
admin_head('Configurações', 'configuracoes', $u);
?>
<div class="ph"><div><h1>Configurações</h1><p>E-mails de aviso, envio de e-mail (SMTP) e os contatos que aparecem no site.</p></div></div>

<form method="post" autocomplete="off">
  <?= csrf_field() ?>
  <section class="card">
    <h2>Avisos de novos leads</h2>
    <div class="fld"><label for="notify_emails">E-mails que recebem cada contato do site</label>
      <textarea id="notify_emails" name="notify_emails" rows="2" placeholder="contato@seudominio.com.br, outra@seudominio.com.br"><?= e($s['notify_emails']) ?></textarea>
      <small>Separe vários e-mails com vírgula. Cada vez que alguém preencher o formulário, todos recebem um aviso.</small></div>
  </section>

  <section class="card">
    <h2>E-mail (SMTP)</h2>
    <p class="hint" style="margin-top:-8px">Use uma conta de e-mail criada no cPanel da HostGator. Servidor costuma ser <b>mail.seudominio.com.br</b>.</p>
    <div class="row2">
      <div class="fld"><label for="smtp_host">Servidor SMTP</label><input id="smtp_host" name="smtp_host" type="text" value="<?= e($s['smtp_host']) ?>" placeholder="mail.seudominio.com.br"></div>
      <div class="row2" style="gap:0 12px">
        <div class="fld"><label for="smtp_port">Porta</label><input id="smtp_port" name="smtp_port" type="number" value="<?= e($s['smtp_port']) ?>"></div>
        <div class="fld"><label for="smtp_secure">Segurança</label><select id="smtp_secure" name="smtp_secure">
          <option value="ssl"<?= $s['smtp_secure'] === 'ssl' ? ' selected' : '' ?>>SSL (porta 465)</option>
          <option value="tls"<?= $s['smtp_secure'] === 'tls' ? ' selected' : '' ?>>TLS (porta 587)</option>
          <option value="none"<?= $s['smtp_secure'] === 'none' ? ' selected' : '' ?>>Nenhuma</option></select></div>
      </div>
    </div>
    <div class="row2">
      <div class="fld"><label for="smtp_user">Usuário (e-mail completo)</label><input id="smtp_user" name="smtp_user" type="text" value="<?= e($s['smtp_user']) ?>" placeholder="site@seudominio.com.br" autocomplete="off"></div>
      <div class="fld"><label for="smtp_pass">Senha do e-mail</label><input id="smtp_pass" name="smtp_pass" type="password" autocomplete="new-password" placeholder="<?= $s['smtp_pass'] !== '' ? '•••••••• (guardada — deixe vazio para manter)' : 'Digite a senha' ?>">
        <?php if ($s['smtp_pass'] !== ''): ?><label class="chk" style="margin-top:6px"><input type="checkbox" name="smtp_pass_clear" value="1"> Apagar a senha guardada</label><?php endif; ?></div>
    </div>
    <div class="row2">
      <div class="fld"><label for="smtp_from">E-mail que aparece como remetente</label><input id="smtp_from" name="smtp_from" type="text" value="<?= e($s['smtp_from']) ?>" placeholder="Normalmente o mesmo do usuário"></div>
      <div class="fld"><label for="smtp_from_name">Nome do remetente</label><input id="smtp_from_name" name="smtp_from_name" type="text" value="<?= e($s['smtp_from_name']) ?>"></div>
    </div>
    <label class="chk"><input type="checkbox" name="smtp_verify" value="1"<?= $s['smtp_verify'] !== '0' ? ' checked' : '' ?>> Verificar o certificado de segurança do servidor de e-mail (recomendado)</label>
    <p class="hint">Se o teste de envio disser que o certificado não confere, use como servidor o endereço que a HostGator mostra em "Configurar cliente de e-mail" ou desmarque esta opção.</p>
  </section>

  <section class="card">
    <h2>Contatos e redes exibidos no site</h2>
    <div class="row2">
      <div class="fld"><label for="phone">Telefone (como aparece no site)</label><input id="phone" name="phone" type="text" value="<?= e($s['phone']) ?>" placeholder="(65) 98115-7076"></div>
      <div class="fld"><label for="whatsapp">WhatsApp (com DDD)</label><input id="whatsapp" name="whatsapp" type="text" value="<?= e($s['whatsapp']) ?>" placeholder="65981157076"><small>Os botões de WhatsApp do site abrem este número.</small></div>
    </div>
    <div class="fld"><label for="email">E-mail de contato</label><input id="email" name="email" type="email" value="<?= e($s['email']) ?>"></div>
    <div class="row2">
      <div class="fld"><label for="instagram">Instagram (endereço completo)</label><input id="instagram" name="instagram" type="url" value="<?= e($s['instagram']) ?>" placeholder="https://www.instagram.com/…"></div>
      <div class="fld"><label for="linkedin">LinkedIn</label><input id="linkedin" name="linkedin" type="url" value="<?= e($s['linkedin']) ?>" placeholder="https://www.linkedin.com/company/…"><small>Só aparece no site quando preenchido.</small></div>
    </div>
    <div class="row2">
      <div class="fld"><label for="facebook">Facebook</label><input id="facebook" name="facebook" type="url" value="<?= e($s['facebook']) ?>" placeholder="https://www.facebook.com/…"></div>
      <div class="fld"><label for="youtube">YouTube</label><input id="youtube" name="youtube" type="url" value="<?= e($s['youtube']) ?>" placeholder="https://www.youtube.com/@…"></div>
    </div>
  </section>

  <div class="form-actions"><button class="btn gold" type="submit">Salvar configurações</button></div>
</form>

<form method="post" class="card" style="margin-top:18px">
  <?= csrf_field() ?><input type="hidden" name="acao" value="teste">
  <h2>Testar o envio de e-mail</h2>
  <p class="hint" style="margin-top:-8px">Salve as configurações acima primeiro. Depois digite um e-mail seu e clique em enviar.</p>
  <div class="filters" style="margin:0">
    <div class="fld grow" style="margin:0"><label for="teste_para">Enviar teste para</label><input id="teste_para" name="teste_para" type="email" value="<?= e($u['email']) ?>"></div>
    <div class="fld" style="margin:0"><button class="btn" type="submit"><?= a_icon('mail') ?>Enviar e-mail de teste</button></div>
  </div>
</form>
<?php admin_foot();
