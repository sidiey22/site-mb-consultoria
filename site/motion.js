/* =====================================================================
   MB Consultoria · motion.js
   GSAP + ScrollTrigger + Lenis. Camada única para todas as páginas.
   Não altera textos, estrutura, cores nem layout: só anima transform,
   opacity e filter.
   ===================================================================== */
(function () {
  'use strict';

  /* ===================================================================
     AJUSTES — duração (segundos) e intensidade do blur (pixels)
     =================================================================== */
  var MB = {
    // Preloader da logo (só na primeira visita da sessão)
    preloader: {
      progress: 1.8,   // tempo da linha dourada e do fade da logo (ideal 1,6–2,2)
      wipe: 0.9,       // tempo da tela subindo
      failsafe: 4,     // se algo falhar, o preloader some em no máximo isto
      blur: 12         // blur inicial da logo
    },
    // Textos e cards entrando
    reveal: {
      blur: 14,        // blur inicial no desktop
      blurMobile: 8,   // blur máximo no celular (< 768px)
      y: 24,           // deslocamento vertical inicial (px)
      duration: 1.1,
      stagger: 0.09,   // intervalo entre itens do mesmo grupo
      ease: 'power3.out',
      start: 0.84      // dispara quando o topo do item chega a 84% da altura da tela
    },
    // Títulos grandes ligados ao scroll (ficam nítidos ao chegar no centro)
    scrub: {
      smooth: 1.1,                       // 1 a 1,2 = acompanha o scroll com leve atraso
      start: 0.92,     // começa a focar com o topo do item a 92% da tela
      end: 0.58        // nítido quando chega a 58% (área central)
    },
    // Seção que sai da tela perde foco
    depth: {
      blur: 4,         // blur máximo da seção saindo
      opacity: 0.6
    },
    // Imagens
    image: {
      fade: 0.8,       // tempo do fade das fotos (sem máscara)
      zoom: 1.1,       // zoom inicial (vai a 1)
      duration: 1.3,   // tempo do zoom
      ease: 'power3.out',
      parallax: 10,    // movimento total em % da altura da foto (8 a 12)
      heroZoom: 1.12,  // aproximação lenta da foto de fundo do hero da Home
      heroDuration: 2.6
    },
    // Entrada do hero de cada página
    hero: {
      duration: 1.2,
      stagger: 0.11,
      delay: 0.05
    },
    // Rolagem suave
    lenis: { lerp: 0.09, wheelMultiplier: 1 },
    mobileMax: 767     // abaixo disso: blur reduzido e sem parallax
  };

  /* =================================================================== */

  var root = document.documentElement;
  var qs = function (s, c) { return (c || document).querySelector(s); };
  var qa = function (s, c) { return Array.prototype.slice.call((c || document).querySelectorAll(s)); };

  // Se algo essencial faltar, mostra o site normal.
  function showPlain() {
    root.classList.add('mb-ready');
    root.classList.remove('mb-lock');
    var p = document.getElementById('mbPre');
    if (p) p.style.display = 'none';
  }
  if (!window.gsap || !window.ScrollTrigger) { showPlain(); return; }

  gsap.registerPlugin(ScrollTrigger);
  ScrollTrigger.config({ ignoreMobileResize: true });

  var mqReduce = window.matchMedia('(prefers-reduced-motion: reduce)');
  var mqMobile = window.matchMedia('(max-width:' + MB.mobileMax + 'px)');
  var reduced = function () { return mqReduce.matches; };
  var mobile = function () { return mqMobile.matches; };
  var revealBlur = function () { return mobile() ? Math.min(MB.reveal.blur, MB.reveal.blurMobile) : MB.reveal.blur; };
  var depthBlur = function () { return mobile() ? Math.min(MB.depth.blur, MB.reveal.blurMobile) : MB.depth.blur; };

  /* ---------- seletores ---------- */
  var HERO = '.hero, .ph-hdr, .contact';
  var HERO_TXT = '.label, .eyebrow, h1, h2, p, .btns, .hero-chip, .cities, .clist li, .units-mini > div, form .f, form .consent, form > div[style]';
  var CARD = '.svc, .feat, .bt, .diff, .compare > div, .camp-list li, .deliv li, .clist li, .units-mini > div, .insta, .pending, .quote-card, .unit-info, .ft > div, .ft-top, .fb .wrap';
  var IMG = '.ph, .ph-photo, .two .photo, .gold-tile, .gallery button, .gallery .empty';
  var TXT = '.label, .eyebrow, h2, h3, p, .quote, .btns, .btn, .link, .addr, .chip-ic, .tag, .pill, .deliv-h, .gal-h, .subhead, .filters, .model-note, .meta, .more, .row2, .foot';
  var SCRUB = 'h2, .quote, .big';
  var PARALLAX = '.hero .hero-bg, .ph-hdr .ph-photo, .two .photo, .lead-member .ph';

  /* ---------- utilidades ---------- */
  function within(el, list) {
    for (var i = 0; i < list.length; i++) if (list[i] !== el && list[i].contains(el)) return true;
    return false;
  }
  function unique(a) { return a.filter(function (e, i) { return a.indexOf(e) === i; }); }
  function inDom(scopes, sel) {
    var out = [];
    scopes.forEach(function (s) { out = out.concat(qa(sel, s)); });
    return unique(out);
  }
  function byDom(a, b) { return a === b ? 0 : (a.compareDocumentPosition(b) & 4 ? -1 : 1); }
  function wc(els, on, prop) { els.forEach(function (e) { e.style.willChange = on ? (prop || 'transform, opacity, filter') : ''; }); }
  function imgOf(w) { return w.querySelector('img'); }

  // Posição de gatilho "topo do elemento a X% da tela", presa entre o início e o fim da página.
  // (-1 garante que o que já está visível ao abrir a página conta como "entrou".)
  function absTop(el) { return el.getBoundingClientRect().top + (window.scrollY || window.pageYOffset); }
  function absBottom(el) { return el.getBoundingClientRect().bottom + (window.scrollY || window.pageYOffset); }
  function maxScroll() { return ScrollTrigger.maxScroll(window) - 1; }
  function topAt(frac) {
    return function (st) { return Math.max(-1, Math.min(absTop(st.trigger) - window.innerHeight * frac, maxScroll())); };
  }
  function topEndAt(fracStart, fracEnd) {
    return function (st) {
      var s = Math.max(-1, Math.min(absTop(st.trigger) - window.innerHeight * fracStart, maxScroll()));
      var e = Math.max(-1, Math.min(absTop(st.trigger) - window.innerHeight * fracEnd, maxScroll()));
      return Math.max(e, s + 1);
    };
  }

  /* ---------- estado da página atual ---------- */
  var S = { els: [], imgs: [], px: [], heroTl: null };
  var preDone = false;
  var lenis = null;

  function teardown() {
    ScrollTrigger.getAll().forEach(function (t) { t.kill(); });
    if (S.heroTl) { S.heroTl.kill(); S.heroTl = null; }
    var all = S.els.concat(S.imgs);
    gsap.killTweensOf(all);
    gsap.set(S.els, { clearProps: 'opacity,filter,transform,clipPath,willChange,visibility' });
    gsap.set(S.imgs, { clearProps: 'transform,willChange' });
    S.px.forEach(function (w) { w.classList.remove('mb-px'); });
    S.els = []; S.imgs = []; S.px = [];
  }

  /* ===================================================================
     Rolagem suave (Lenis) integrada ao ScrollTrigger
     =================================================================== */
  function tick(t) { if (lenis) lenis.raf(t * 1000); }

  function startLenis() {
    if (lenis || reduced() || !window.Lenis) return;
    lenis = new window.Lenis({
      lerp: MB.lenis.lerp,
      wheelMultiplier: MB.lenis.wheelMultiplier,
      smoothWheel: true
    });
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add(tick);
    gsap.ticker.lagSmoothing(0);
  }
  function stopLenis() {
    if (!lenis) return;
    gsap.ticker.remove(tick);
    lenis.destroy();
    lenis = null;
  }

  // Os links de âncora do site usam scrollIntoView: passam pelo Lenis.
  var nativeSIV = Element.prototype.scrollIntoView;
  Element.prototype.scrollIntoView = function (opt) {
    if (lenis && opt !== false) {
      var m = parseFloat(getComputedStyle(this).scrollMarginTop) || 0;
      lenis.scrollTo(this, { offset: -m, duration: 1.3 });
      return;
    }
    if (reduced()) return nativeSIV.call(this, { block: 'start', behavior: 'auto' });
    return nativeSIV.apply(this, arguments);
  };

  function lock() {
    root.classList.add('mb-lock');
    if (lenis) lenis.stop();
  }
  function unlock() {
    root.classList.remove('mb-lock');
    if (lenis) lenis.start();
  }

  /* ===================================================================
     Preloader
     =================================================================== */
  function runPreloader(cb) {
    var pre = document.getElementById('mbPre');
    if (!pre || root.classList.contains('mb-pre-seen')) { cb(); return; }
    try { sessionStorage.setItem('mb-pre', '1'); } catch (e) {}

    var logo = qs('.mb-pre-logo', pre);
    var line = qs('.mb-pre-line i', pre);
    var finished = false;

    function finish() {
      if (finished) return;
      finished = true;
      clearTimeout(failsafe);
      pre.style.display = 'none';
      cb();
    }
    var failsafe = setTimeout(finish, MB.preloader.failsafe * 1000);
    var tl = gsap.timeline({ onComplete: finish });

    if (reduced()) {
      // Movimento reduzido: fade simples e curto.
      gsap.set(logo, { opacity: 1, filter: 'none' });
      gsap.set(line, { scaleX: 1 });
      tl.to(pre, { opacity: 0, duration: 0.35, delay: 0.45, ease: 'power1.out' });
      return;
    }

    var P = MB.preloader;
    tl.to(logo, { opacity: 1, filter: 'blur(0px)', duration: 0.9, ease: 'power2.out' }, 0)
      .to(line, { scaleX: 1, duration: P.progress - 0.1, ease: 'power2.inOut' }, 0.1)
      // Se a página ainda estiver carregando, espera um pouco (o failsafe garante o limite).
      .add(function () {
        if (document.readyState === 'complete') return;
        tl.pause();
        var go = function () { tl.resume(); };
        window.addEventListener('load', go, { once: true });
        setTimeout(go, 1200);
      }, P.progress + 0.05)
      .to(logo, { opacity: 0, duration: 0.35, ease: 'power1.in' }, '>+=0.05')
      .to(pre, { yPercent: -100, duration: P.wipe, ease: 'power4.inOut' }, '<');
  }

  /* ===================================================================
     Hero de cada página (entra quando o preloader termina)
     =================================================================== */
  function setupHero(hero) {
    if (!hero) return null;
    var B = revealBlur(), Y = MB.reveal.y, H = MB.hero;
    var items = qa(HERO_TXT, hero);
    items = items.filter(function (e) { return !within(e, items); });
    var frames = qa('.arch-frame', hero);
    var arches = qa('.arch-frame .arch, .ph-photo', hero);
    var imgs = arches.map(imgOf).filter(Boolean);
    var bgs = qa('.hero-bg', hero);
    var bgImgs = bgs.map(imgOf).filter(Boolean);
    var tl = gsap.timeline({ paused: true });
    var each = items.length > 1 ? Math.min(H.stagger, 0.9 / (items.length - 1)) : 0;

    S.els = S.els.concat(items, frames, arches, bgs);
    S.imgs = S.imgs.concat(imgs, bgImgs);

    if (reduced()) {
      gsap.set(items.concat(frames, bgs), { opacity: 0 });
      tl.to(bgs, { opacity: 1, duration: 0.5, ease: 'power1.out', clearProps: 'opacity' }, 0)
        .to(items.concat(frames), { opacity: 1, duration: 0.5, stagger: 0.04, ease: 'power1.out', clearProps: 'opacity' }, 0);
      return tl;
    }

    gsap.set(items, { opacity: 0, filter: 'blur(' + B + 'px)', y: Y });
    gsap.set(frames, { opacity: 0 });
    gsap.set(arches, { opacity: 0 });
    gsap.set(imgs, { scale: MB.image.zoom });
    gsap.set(bgs, { opacity: 0 });
    gsap.set(bgImgs, { scale: MB.image.heroZoom });

    // Foto de fundo: aparece por baixo do degradê e se aproxima devagar.
    tl.to(bgs, { opacity: 1, duration: 1.1, ease: 'power2.out', clearProps: 'opacity' }, 0)
      .to(bgImgs, { scale: 1, duration: MB.image.heroDuration, ease: 'power2.out' }, 0);

    tl.to(items, {
      opacity: 1, filter: 'blur(0px)', y: 0,
      duration: H.duration, stagger: each, ease: 'power3.out',
      onStart: function () { wc(items, true); },
      onComplete: function () { gsap.set(items, { clearProps: 'filter,transform,willChange,opacity' }); }
    }, H.delay)
      .to(frames, { opacity: 1, duration: 0.9, ease: 'power2.out', clearProps: 'opacity' }, H.delay + 0.2)
      .to(arches, { opacity: 1, duration: MB.image.fade, ease: 'power2.out', clearProps: 'opacity' }, H.delay + 0.15)
      .to(imgs, { scale: 1, duration: MB.image.duration, ease: MB.image.ease }, H.delay + 0.15);

    return tl;
  }

  /* ===================================================================
     Revelações no scroll (foco/desfoque) — textos, cards e imagens
     =================================================================== */
  function showText(b) {
    gsap.to(b, {
      opacity: 1, filter: 'blur(0px)', y: 0,
      duration: MB.reveal.duration, ease: MB.reveal.ease, stagger: MB.reveal.stagger, overwrite: true,
      onStart: function () { wc(b, true); },
      onComplete: function () { gsap.set(b, { clearProps: 'filter,transform,willChange,opacity' }); }
    });
  }
  function hideText(b) {
    var B = revealBlur(), Y = MB.reveal.y;
    gsap.to(b, {
      opacity: 0, filter: 'blur(' + B + 'px)',
      y: function (i, el) { return el.classList.contains('anchor') ? 0 : Y; },
      duration: 0.5, ease: 'power2.out', overwrite: true
    });
  }
  function showImgs(b) {
    var ims = b.map(imgOf).filter(Boolean);
    var frames = b.map(function (w) { return w.closest('.arch-frame'); }).filter(Boolean);
    gsap.to(b, {
      opacity: 1, duration: MB.image.fade, ease: 'power2.out', stagger: 0.1, overwrite: true,
      onStart: function () { wc(b, true, 'opacity'); },
      onComplete: function () { gsap.set(b, { clearProps: 'opacity,willChange' }); }
    });
    gsap.to(ims, { scale: 1, duration: MB.image.duration, ease: MB.image.ease, stagger: 0.1, overwrite: 'auto' });
    if (frames.length) gsap.to(frames, { opacity: 1, duration: 0.9, stagger: 0.12, ease: 'power2.out', overwrite: true });
  }
  function resetImgs(b) {
    gsap.set(b, { opacity: 0 });
    gsap.set(b.map(imgOf).filter(Boolean), { scale: MB.image.zoom });
    var frames = b.map(function (w) { return w.closest('.arch-frame'); }).filter(Boolean);
    if (frames.length) gsap.set(frames, { opacity: 0 });
  }

  function setupReveals(scopes, hero) {
    var notHero = function (e) { return !(hero && hero.contains(e)); };

    var cards = inDom(scopes, CARD).filter(notHero);
    cards = cards.filter(function (e) { return !within(e, cards); });
    var imgs = inDom(scopes, IMG).filter(notHero).filter(function (e) { return !within(e, cards); });
    var texts = inDom(scopes, TXT).filter(notHero).filter(function (e) {
      return !within(e, cards) && !within(e, imgs);
    });
    texts = texts.filter(function (e) { return !within(e, texts); });

    var scrubbed = texts.filter(function (e) { return e.matches(SCRUB); });
    var small = texts.filter(function (e) { return !e.matches(SCRUB); }).concat(cards).sort(byDom);
    var frames = imgs.map(function (w) { return w.closest('.arch-frame'); }).filter(Boolean);

    S.els = S.els.concat(small, scrubbed, imgs, frames);
    S.imgs = S.imgs.concat(imgs.map(imgOf).filter(Boolean));

    /* ----- movimento reduzido: só fade ----- */
    if (reduced()) {
      var every = small.concat(scrubbed, imgs).sort(byDom);
      gsap.set(every, { opacity: 0 });
      var fade = function (b) {
        gsap.to(b, { opacity: 1, duration: 0.5, stagger: 0.05, overwrite: true, clearProps: 'opacity' });
      };
      ScrollTrigger.batch(every, { start: topAt(MB.reveal.start), onEnter: fade, onEnterBack: fade });
      return;
    }

    var B = revealBlur(), Y = MB.reveal.y;

    /* ----- textos pequenos e cards: disparo + stagger curto ----- */
    small.forEach(function (el) {
      var v = { opacity: 0, filter: 'blur(' + B + 'px)' };
      if (!el.classList.contains('anchor')) v.y = Y;   // âncoras não se deslocam (scroll preciso)
      gsap.set(el, v);
    });
    ScrollTrigger.batch(small, {
      interval: 0.08, start: topAt(MB.reveal.start),
      onEnter: showText, onEnterBack: showText, onLeaveBack: hideText
    });

    /* ----- títulos grandes: scrub suave, ficam nítidos no centro ----- */
    scrubbed.forEach(function (el) {
      gsap.fromTo(el,
        { opacity: 0, filter: 'blur(' + B + 'px)', y: Y },
        {
          opacity: 1, filter: 'blur(0px)', y: 0, ease: 'none',
          scrollTrigger: { trigger: el, start: topAt(MB.scrub.start), end: topEndAt(MB.scrub.start, MB.scrub.end), scrub: MB.scrub.smooth }
        });
    });

    /* ----- imagens: fade + zoom suave (sem máscara) ----- */
    gsap.set(imgs, { opacity: 0 });
    gsap.set(imgs.map(imgOf).filter(Boolean), { scale: MB.image.zoom });
    if (frames.length) gsap.set(frames, { opacity: 0 });
    ScrollTrigger.batch(imgs, {
      interval: 0.08, start: topAt(0.88),
      onEnter: showImgs, onEnterBack: showImgs, onLeaveBack: resetImgs
    });
  }

  /* ===================================================================
     Parallax leve (só desktop e sem movimento reduzido)
     =================================================================== */
  function setupParallax(page) {
    if (reduced() || mobile()) return;
    var P = MB.image.parallax / 2;
    qa(PARALLAX, page).forEach(function (w) {
      var im = imgOf(w);
      if (!im) return;
      w.classList.add('mb-px');
      S.px.push(w);
      S.imgs.push(im);
      gsap.fromTo(im, { yPercent: -P }, {
        yPercent: P, ease: 'none',
        scrollTrigger: { trigger: w, start: 'top bottom', end: 'bottom top', scrub: MB.scrub.smooth }
      });
    });
  }

  /* ===================================================================
     Profundidade: a seção que sai perde foco
     =================================================================== */
  function setupDepth(page) {
    if (reduced()) return;
    var secs = qa(':scope > .hero-wrap, :scope > .ph-wrap, :scope > .blk', page).sort(byDom);
    secs.pop(); // a última não tem "próxima"
    var B = depthBlur();
    secs.forEach(function (sec) {
      S.els.push(sec);
      gsap.fromTo(sec,
        { opacity: 1, filter: 'blur(0px)' },
        {
          opacity: MB.depth.opacity, filter: 'blur(' + B + 'px)', ease: 'none', immediateRender: false,
          scrollTrigger: {
            trigger: sec, scrub: MB.scrub.smooth,
            start: function () { return Math.max(0, absBottom(sec) - window.innerHeight * 0.62); },
            end: function () {
              var s = Math.max(0, absBottom(sec) - window.innerHeight * 0.62);
              return Math.max(s + 1, absBottom(sec) - window.innerHeight * 0.10);
            },
            onToggle: function (st) { sec.style.willChange = st.isActive ? 'filter, opacity' : ''; }
          }
        });
    });
  }

  /* ===================================================================
     Recalcular posições quando imagens/fontes terminam de carregar
     =================================================================== */
  var rt;
  function refreshSoon() { clearTimeout(rt); rt = setTimeout(function () { ScrollTrigger.refresh(); }, 120); }
  function watchImages(scopes) {
    scopes.forEach(function (s) {
      qa('img', s).forEach(function (im) {
        if (!im.complete) im.addEventListener('load', refreshSoon, { once: true });
      });
    });
  }

  /* ===================================================================
     Monta a página ativa
     =================================================================== */
  function build() {
    teardown();
    var page = qs('section.page.on');
    var footer = qs('footer');
    if (!page) return null;
    var scopes = footer ? [page, footer] : [page];
    var hero = qs(HERO, page);

    var tl = setupHero(hero);
    setupReveals(scopes, hero);
    setupParallax(page);
    setupDepth(page);
    watchImages(scopes);
    S.heroTl = tl;
    ScrollTrigger.refresh();
    return tl;
  }

  /* ===================================================================
     Filtros de clientes recriam os cards: anima os novos na hora
     =================================================================== */
  function watchClients() {
    var cEl = document.getElementById('clients');
    if (!cEl || !window.MutationObserver) return;
    new MutationObserver(function () {
      var items = qa('.client-card', cEl);
      if (!items.length) return;
      if (reduced()) {
        gsap.fromTo(items, { opacity: 0 }, { opacity: 1, duration: 0.4, stagger: 0.03, clearProps: 'opacity' });
      } else {
        var ims = items.map(imgOf).filter(Boolean);
        gsap.set(items, { opacity: 0 });
        gsap.set(ims, { scale: MB.image.zoom });
        gsap.to(items, { opacity: 1, duration: MB.image.fade, ease: 'power2.out', stagger: 0.04, clearProps: 'opacity' });
        gsap.to(ims, { scale: 1, duration: MB.image.duration, ease: MB.image.ease, stagger: 0.04 });
      }
      watchImages([cEl]);
      refreshSoon();
    }).observe(cEl, { childList: true });
  }

  /* ===================================================================
     Início
     =================================================================== */
  function init() {
    try {
      qa('dialog').forEach(function (d) { d.setAttribute('data-lenis-prevent', ''); });
      startLenis();

      var willPre = !!document.getElementById('mbPre') && !root.classList.contains('mb-pre-seen');
      if (willPre) lock();

      var tl = build();
      root.classList.add('mb-ready');
      watchClients();

      if (willPre) {
        runPreloader(function () {
          preDone = true;
          unlock();
          if (S.heroTl) S.heroTl.play();
        });
      } else {
        preDone = true;
        unlock();
        if (tl) tl.play();
      }

      window.addEventListener('load', refreshSoon);
      if (document.fonts && document.fonts.ready) document.fonts.ready.then(refreshSoon);
    } catch (err) {
      if (window.console) console.error('[motion]', err);
      teardown();
      showPlain();
    }
  }

  // Troca de página: o site já mostrou a nova seção; aqui só reconstruímos o motion.
  window.addEventListener('hashchange', function () {
    try {
      if (lenis && preDone) { lenis.stop(); lenis.start(); } // corta a inércia da rolagem anterior
      var tl = build();
      if (preDone && tl) tl.play();
    } catch (err) {
      if (window.console) console.error('[motion]', err);
      teardown();
      showPlain();
    }
  });

  // Preferências do sistema mudando (movimento reduzido / largura): reaplica sem repetir o hero.
  function reapply() {
    if (reduced()) stopLenis(); else if (preDone) startLenis();
    var tl = build();
    if (tl) tl.progress(1);
  }
  if (mqReduce.addEventListener) {
    mqReduce.addEventListener('change', reapply);
    mqMobile.addEventListener('change', reapply);
  }

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();
})();
