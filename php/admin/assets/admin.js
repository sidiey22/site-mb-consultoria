/* Painel MB: menu no celular, confirmações e pequenos ajudantes. */
(function () {
  var btn = document.getElementById('menuT'), scrim = document.getElementById('scrim');
  function setMenu(open) {
    document.body.classList.toggle('menu-open', open);
    if (btn) { btn.setAttribute('aria-expanded', open ? 'true' : 'false'); }
  }
  if (btn) { btn.addEventListener('click', function () { setMenu(!document.body.classList.contains('menu-open')); }); }
  if (scrim) { scrim.addEventListener('click', function () { setMenu(false); }); }
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setMenu(false); } });

  // Confirmação antes de ações perigosas: <form data-confirm="Texto">
  document.addEventListener('submit', function (e) {
    var f = e.target, msg = f.getAttribute && f.getAttribute('data-confirm');
    if (msg && !window.confirm(msg)) { e.preventDefault(); }
  });

  // Contador de caracteres: <input data-count="160"> + <span class="cnt" data-for="id">
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var out = document.querySelector('.cnt[data-for="' + el.id + '"]');
    if (!out) { return; }
    var max = parseInt(el.getAttribute('data-count'), 10);
    var up = function () { out.textContent = el.value.length + ' / ' + max; out.style.color = el.value.length > max ? '#b3382b' : ''; };
    el.addEventListener('input', up); up();
  });

  // Mostrar/ocultar senha
  document.querySelectorAll('[data-toggle-pass]').forEach(function (b) {
    b.addEventListener('click', function () {
      var i = document.getElementById(b.getAttribute('data-toggle-pass'));
      if (!i) { return; }
      var show = i.type === 'password'; i.type = show ? 'text' : 'password';
      b.textContent = show ? 'Ocultar senha' : 'Mostrar senha';
    });
  });
})();
