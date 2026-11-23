/* Comportamentos do conteúdo (artigos): clique nos cards, filtros/paginação sem recarregar, compartilhar. */
(function () {
  var script = document.currentScript;
  var API = script ? new URL('../api/', script.src).pathname : '/api/';

  // Clique em qualquer lugar do card abre o artigo
  document.addEventListener('click', function (e) {
    var post = e.target.closest('.post[data-href]');
    if (post && !e.target.closest('a')) { location.href = post.getAttribute('data-href'); }
  });

  // Copiar link
  document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-copy]');
    if (!b) { return; }
    var url = b.getAttribute('data-copy');
    var done = function () {
      var s = b.querySelector('span'); var old = s ? s.textContent : '';
      b.classList.add('done'); if (s) { s.textContent = 'Link copiado'; }
      setTimeout(function () { b.classList.remove('done'); if (s) { s.textContent = old; } }, 2200);
    };
    if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(url).then(done, function () { window.prompt('Copie o link:', url); }); }
    else { window.prompt('Copie o link:', url); }
  });

  // Filtro por categoria e paginação (página Conteúdos)
  var cat = '';
  function load(c, page) {
    var list = document.querySelector('[data-posts-list]'), pager = document.querySelector('[data-posts-pager]');
    if (!list) { return; }
    list.classList.add('loading');
    fetch(API + 'conteudos.php?cat=' + encodeURIComponent(c) + '&pagina=' + (page || 1), { headers: { 'X-Requested-With': 'fetch' } })
      .then(function (r) { return r.json(); })
      .then(function (j) {
        list.innerHTML = j.cards; if (pager) { pager.innerHTML = j.pager; }
        list.classList.remove('loading');
        if (window.ScrollTrigger) { setTimeout(function () { ScrollTrigger.refresh(); }, 120); }
      })
      .catch(function () { list.classList.remove('loading'); });
  }
  function scrollToList() {
    var t = document.querySelector('[data-page="conteudos"] .filters') || document.querySelector('[data-posts-list]');
    if (t) { t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
  }
  document.addEventListener('click', function (e) {
    var chip = e.target.closest('.filters .chip[data-cat]');
    if (chip) {
      cat = chip.getAttribute('data-cat') || '';
      chip.parentNode.querySelectorAll('.chip').forEach(function (c) { c.setAttribute('aria-pressed', c === chip ? 'true' : 'false'); });
      load(cat, 1);
      return;
    }
    var pg = e.target.closest('.pager a[data-pagina]');
    if (pg) {
      e.preventDefault();
      var active = document.querySelector('.filters .chip[aria-pressed="true"]');
      cat = active ? (active.getAttribute('data-cat') || '') : cat;
      load(cat, parseInt(pg.getAttribute('data-pagina'), 10) || 1);
      scrollToList();
    }
  });

  // Menu no celular nas páginas fora da página inicial (a página inicial tem o seu próprio)
  if (!document.querySelector('section.page')) {
    var nav = document.getElementById('nav'), btn = document.getElementById('menuBtn');
    if (nav && btn) {
      btn.addEventListener('click', function () { var o = nav.classList.toggle('open'); btn.setAttribute('aria-expanded', o); });
    }
  }
})();
