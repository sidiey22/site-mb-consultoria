#!/usr/bin/env python3
"""Gera o pacote PHP (php/) a partir do site atual (site/index.html, já com os ícones inline).
Não altera layout/textos: só extrai o CSS, transforma contatos em variáveis e liga Conteúdos e formulário ao banco."""
import re, shutil, subprocess, os, json

ROOT = '/home/claude/mb'
OUT = ROOT + '/php'
L = ROOT + '/tools/node_modules/lucide-static/icons/'
S = ROOT + '/tools/node_modules/simple-icons/icons/'

subprocess.run(['python3', ROOT + '/build.py'], check=True, stdout=subprocess.DEVNULL)
html = open(ROOT + '/site/index.html', encoding='utf-8').read()

# ---------- ícones usados pelo PHP ----------
def lucide(name):
    s = open(L + name + '.svg').read()
    s = re.sub(r'<!--.*?-->', '', s, flags=re.S).strip()
    s = re.sub(r'<svg[^>]*>', '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">', s, count=1)
    return re.sub(r'\s*\n\s*', ' ', s)

def brand(name):
    s = open(S + name + '.svg').read()
    d = re.search(r'<path d="([^"]+)"', s).group(1)
    return f'<svg class="ic ic-b" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{d}"/></svg>'

icons = {n: lucide(n) for n in ['calendar', 'arrow-up-right', 'arrow-left', 'arrow-right', 'chevron-left', 'chevron-right', 'user', 'clock', 'link']}
icons.update({n: brand(n) for n in ['whatsapp', 'facebook', 'linkedin', 'x', 'youtube', 'instagram']})
# json -> php array literal: usar var_export seria melhor; converte chaves/valores simples
def php_array(d):
    return 'array(\n' + ''.join("  '%s' => '%s',\n" % (k, v.replace('\\', '\\\\').replace("'", "\\'")) for k, v in d.items()) + ');'
with open(OUT + '/inc/icons.php', 'w', encoding='utf-8') as f:
    f.write('<?php\n// Gerado automaticamente a partir do pacote de ícones do site.\nreturn ' + php_array(icons) + '\n')

# ---------- 1. CSS para arquivo ----------
m = re.search(r'<style>\n(.*?)</style>', html, flags=re.S)
css = m.group(1)
open(OUT + '/css/site.css', 'w', encoding='utf-8').write(css)
html = html.replace(m.group(0), '<link rel="stylesheet" href="css/site.css">\n<link rel="stylesheet" href="css/cms.css">')

# ---------- 2. contatos viram variáveis ----------
def tok(s):
    s = s.replace('https://wa.me/5565981157076', 'https://wa.me/<?= e($S[\'whatsapp\']) ?>')
    s = s.replace('(65) 98115-7076', "<?= e($S['phone']) ?>")
    s = s.replace('marianabonjour@mbconsultoriapolitica.com', "<?= e($S['email']) ?>")
    s = s.replace('https://www.instagram.com/mbconsultoriapolitica/', "<?= e($S['instagram']) ?>")
    s = s.replace('@mbconsultoriapolitica', "<?= e($S['instagram_handle']) ?>")
    return s

def linkedin_blocks(s):
    # contato: <li> do LinkedIn
    pat = re.compile(r'<li><span class="ico">(<svg[^>]*>(?:(?!</svg>).)*</svg>)</span><div><small>LinkedIn</small><span style="color:#9ea3b8">Link a inserir</span></div></li>', re.S)
    assert pat.search(s), 'li linkedin (contato)'
    s = pat.sub(lambda m: "<?php if ($S['linkedin'] !== ''): ?><li><span class=\"ico\">" + m.group(1) + "</span><div><small>LinkedIn</small><a href=\"<?= e($S['linkedin']) ?>\" target=\"_blank\" rel=\"noopener\">Ver perfil</a></div></li><?php endif; ?>", s)
    # rodapé: ícones sociais
    pat = re.compile(r'<a href="#contato" aria-label="LinkedIn \(link a inserir\)">(<svg[^>]*>(?:(?!</svg>).)*</svg>)</a>', re.S)
    assert pat.search(s), 'icone linkedin (rodape)'
    extra = ''.join(
        "<?php if ($S['%s'] !== ''): ?><a href=\"<?= e($S['%s']) ?>\" target=\"_blank\" rel=\"noopener\" aria-label=\"%s\">%s</a><?php endif; ?>" % (k, k, lab, icons[k])
        for k, lab in [('facebook', 'Facebook'), ('youtube', 'YouTube')])
    s = pat.sub(lambda m: "<?php if ($S['linkedin'] !== ''): ?><a href=\"<?= e($S['linkedin']) ?>\" target=\"_blank\" rel=\"noopener\" aria-label=\"LinkedIn\">" + m.group(1) + "</a><?php endif; ?>" + extra, s)
    # rodapé: lista de contato
    old = '<li>LinkedIn: link a inserir</li>'
    assert old in s
    s = s.replace(old, "<?php if ($S['linkedin'] !== ''): ?><li>LinkedIn: <a href=\"<?= e($S['linkedin']) ?>\" target=\"_blank\" rel=\"noopener\">ver perfil</a></li><?php endif; ?>")
    return s

html = linkedin_blocks(html)
html = tok(html)

# ---------- 3. partes compartilhadas (header, footer, CTA) ----------
def cut(s, start, end, include_end=True):
    i = s.index(start)
    j = s.index(end, i) + (len(end) if include_end else 0)
    return i, j

def to_partial(chunk):
    return chunk.replace('href="#', 'href="<?= e($h) ?>#').replace('src="img/', 'src="<?= e($h) ?>img/')

i, j = cut(html, '<div class="topbar">', '</header>')
header = html[i:j]
html = html[:i] + "<?php include __DIR__ . '/inc/partials/header.php'; ?>" + html[j:]

i, j = cut(html, '<footer>', '</footer>')
footer = html[i:j]
wa_i = html.index('<a class="wa"', j)
wa_j = html.index('</a>', wa_i) + 4
footer += '\n\n' + html[wa_i:wa_j]
html = html[:i] + "<?php include __DIR__ . '/inc/partials/footer.php'; ?>" + html[j:wa_i] + html[wa_j:]

i, j = cut(html, '<template id="ctaTpl">', '</template>')
cta_inner = html[i + len('<template id="ctaTpl">'):j - len('</template>')]
cta = '<div class="blk" style="padding-top:0">' + cta_inner + '</div>'

open(OUT + '/inc/partials/header.php', 'w', encoding='utf-8').write(to_partial(header) + '\n')
open(OUT + '/inc/partials/footer.php', 'w', encoding='utf-8').write(to_partial(footer) + '\n')
open(OUT + '/inc/partials/cta.php', 'w', encoding='utf-8').write(to_partial(cta) + '\n')

# ---------- 4. página Conteúdos ligada ao banco ----------
i = html.index('<div class="filters" role="group" aria-label="Filtrar conteúdos">')
j = html.index('</article>\n      </div>', i)
j = html.index('</div>', j + len('</article>\n')) + len('</div>')
html = html[:i] + '<?= render_conteudos_block() ?>' + html[j:]

# filtro antigo (só alternava o botão marcado) sai; o novo está em js/cms.js
old_filter = re.search(r"  document\.querySelectorAll\('\[data-page=\"conteudos\"\] \.filters'\)\.forEach\(function\(f\)\{.*?\n  \}\);\n", html, flags=re.S)
assert old_filter, 'filtro conteudos antigo'
html = html.replace(old_filter.group(0), '')

# ---------- 5. formulário envia para o banco ----------
html = html.replace('<form id="form" novalidate>', '<form id="form" novalidate>\n          <div class="hp" aria-hidden="true"><label>Não preencha este campo<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>', 1)
a = html.index('  // Formulário\n')
b = html.index('  // Rotas\n')
new_form = r"""  // Formulário: envia para api/lead.php e responde dentro do próprio formulário
  (function(){
    var API = <?= json_encode(url('api/')) ?>;
    var WA = 'https://wa.me/<?= e($S['whatsapp']) ?>';
    var form = document.getElementById('form'), msg = document.getElementById('formmsg');
    if(!form) return;
    var btn = form.querySelector('button[type=submit]'), tok = {t:'', sig:''};
    function say(html, kind){ msg.innerHTML = html; msg.className = 'formmsg show ' + kind; }
    function plain(t, kind){ msg.textContent = t; msg.className = 'formmsg show ' + kind; }
    function token(){ return fetch(API + 'token.php', {cache:'no-store'}).then(function(r){return r.json()}).then(function(j){tok=j}).catch(function(){}); }
    token();
    var utm = {};
    try{
      var p = new URLSearchParams(location.search);
      ['utm_source','utm_medium','utm_campaign','utm_term','utm_content'].forEach(function(k){ if(p.get(k)) utm[k]=p.get(k); });
      if(Object.keys(utm).length) sessionStorage.setItem('mb-utm', JSON.stringify(utm)); else utm = JSON.parse(sessionStorage.getItem('mb-utm')||'{}');
      if(!sessionStorage.getItem('mb-ref')) sessionStorage.setItem('mb-ref', document.referrer||'-');
    }catch(e){}
    form.addEventListener('submit', function(e){
      e.preventDefault();
      var f = e.target;
      var missing = ['f-nome','f-email','f-tel'].filter(function(id){return !document.getElementById(id).value.trim()});
      if(missing.length){ plain('Preencha nome, e-mail e telefone para enviar.', 'err'); document.getElementById(missing[0]).focus(); return; }
      if(!document.getElementById('f-ok').checked){ plain('Marque o aceite da Política de Privacidade para enviar.', 'err'); return; }
      var fd = new FormData();
      fd.append('form_name', 'Contato (página Contato)');
      fd.append('nome', f.nome.value); fd.append('email', f.email.value); fd.append('telefone', f.telefone.value);
      fd.append('instituicao', f.instituicao.value); fd.append('assunto', f.assunto.value); fd.append('mensagem', f.mensagem.value);
      fd.append('website', f.website.value); fd.append('t', tok.t); fd.append('sig', tok.sig); fd.append('aceite', '1');
      fd.append('pagina', location.href.split('#')[0] + '#contato');
      try{ fd.append('referrer', sessionStorage.getItem('mb-ref')||''); }catch(e){}
      Object.keys(utm).forEach(function(k){ fd.append(k, utm[k]); });
      btn.disabled = true; btn.setAttribute('aria-busy', 'true'); plain('Enviando…', 'info');
      fetch(API + 'lead.php', {method:'POST', body:fd, headers:{'X-Requested-With':'fetch'}})
        .then(function(r){ return r.json().catch(function(){ return {ok:false, error:'Não foi possível enviar agora.'}; }); })
        .then(function(j){
          if(j && j.ok){ f.reset(); plain(j.message || 'Mensagem enviada! Nossa equipe retornará o contato em breve.', 'ok'); }
          else {
            var t = 'Olá, MB! Sou '+f.nome.value+(f.instituicao.value?' ('+f.instituicao.value+')':'')+'. Assunto: '+f.assunto.value+'. '+f.mensagem.value;
            say(((j && j.error) || 'Não foi possível enviar agora.') + ' Se preferir, <a href="'+WA+'?text='+encodeURIComponent(t)+'" target="_blank" rel="noopener">envie a mesma mensagem pelo WhatsApp</a>.', 'err');
          }
        })
        .catch(function(){ say('Não foi possível enviar agora. Confira sua conexão ou <a href="'+WA+'" target="_blank" rel="noopener">fale pelo WhatsApp</a>.', 'err'); })
        .then(function(){ btn.disabled = false; btn.removeAttribute('aria-busy'); token(); });
    });
  })();

"""
html = html[:a] + new_form + html[b:]

# ---------- 6. cabeçalho PHP, Open Graph, rotas com base ----------
og = """<title>MB Consultoria e Assessoria Política</title>
<meta name="description" content="Comunicação política com propósito. Estratégia que aproxima. Consultoria em Comunicação Governamental e Marketing Político em Cuiabá e Brasília.">
<link rel="canonical" href="<?= e(abs_url('')) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="MB Consultoria e Assessoria Política">
<meta property="og:locale" content="pt_BR">
<meta property="og:title" content="MB Consultoria e Assessoria Política">
<meta property="og:description" content="Comunicação política com propósito. Estratégia que aproxima.">
<meta property="og:url" content="<?= e(abs_url('')) ?>">
<meta property="og:image" content="<?= e(abs_url('img/cba-fachada-logo.jpg')) ?>">
<meta name="twitter:card" content="summary_large_image">"""
m = re.search(r'<title>MB Consultoria e Assessoria Política</title>\n<meta name="description"[^\n]*\n', html)
assert m
html = html.replace(m.group(0), og + '\n', 1)

# scripts do final: motion.js e cms.js
html = html.replace('<script defer src="motion.js"></script>', '<script defer src="motion.js"></script>\n<script defer src="js/cms.js"></script>')

prefix = "<?php\nrequire __DIR__ . '/inc/bootstrap.php';\nsecurity_headers();\n$S = site_settings();\n$h = '';\n?>"
open(OUT + '/index.php', 'w', encoding='utf-8').write(prefix + html)

# ---------- 7. assets do site ----------
for f in ['motion.js', 'motion.css']:
    shutil.copy(ROOT + '/site/' + f, OUT + '/' + f)
if os.path.isdir(OUT + '/img'):
    shutil.rmtree(OUT + '/img')
shutil.copytree(ROOT + '/site/img', OUT + '/img')
print('ok', len(html))
