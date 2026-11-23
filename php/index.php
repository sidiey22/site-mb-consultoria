<?php
require __DIR__ . '/inc/bootstrap.php';
security_headers();
$S = site_settings();
$h = '';
?><!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>
(function(){var d=document.documentElement;d.classList.add('mb-js');
var seen=false;try{seen=sessionStorage.getItem('mb-pre')==='1'}catch(e){}
if(seen){d.classList.add('mb-pre-seen')}else{d.classList.add('mb-lock')}
setTimeout(function(){d.classList.remove('mb-lock')},4000);})();
</script>
<title>MB Consultoria e Assessoria Política</title>
<meta name="description" content="Comunicação política com propósito. Estratégia que aproxima. Consultoria em Comunicação Governamental e Marketing Político em Cuiabá e Brasília.">
<link rel="canonical" href="<?= e(abs_url('')) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="MB Consultoria e Assessoria Política">
<meta property="og:locale" content="pt_BR">
<meta property="og:title" content="MB Consultoria e Assessoria Política">
<meta property="og:description" content="Comunicação política com propósito. Estratégia que aproxima.">
<meta property="og:url" content="<?= e(abs_url('')) ?>">
<meta property="og:image" content="<?= e(abs_url('img/cba-fachada-logo.jpg')) ?>">
<meta name="twitter:card" content="summary_large_image">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/site.css">
<link rel="stylesheet" href="css/cms.css">
<link rel="stylesheet" href="motion.css">
</head>
<body>
<div class="mb-pre" id="mbPre" aria-hidden="true"><div class="mb-pre-in"><img class="mb-pre-logo" src="img/logo-bloco.png" alt="" width="900" height="646"><div class="mb-pre-line"><i></i></div></div></div>

<?php include __DIR__ . '/inc/partials/header.php'; ?>

<main>

<!-- ============ HOME ============ -->
<section class="page" data-page="home">
  <div class="hero-wrap">
    <div class="hero">
      <div class="hero-bg"><img src="img/cba-hero.jpg" alt="Recepção da MB Consultoria e Assessoria Política em Cuiabá" fetchpriority="high"></div>
      <div class="hero-in">
        <div class="hero-txt">
          <div class="label light"><i></i><i></i>Consultoria & Assessoria Política</div>
          <h1>Comunicação política com propósito. <span class="it gold-t">Estratégia que aproxima.</span></h1>
          <p class="lead">Acreditamos que por trás de cada figura pública existe uma história, uma identidade e um propósito que merecem ser comunicados de forma verdadeira.</p>
          <div class="btns">
            <a class="btn btn-gold" href="#solucoes">Conheça nossas soluções <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></a>
            <a class="btn btn-line" href="#contato">Converse com a MB</a>
          </div>
        </div>
        <div class="hero-chip">Cuiabá <i></i> Brasília</div>
      </div>
    </div>
  </div>

  <div class="blk">
    <div class="wrap about">
      <div class="collage">
        <div class="ph a"><img loading="lazy" src="img/cba-sala-direcao.jpg" alt="Sala de direção da MB em Cuiabá" style="object-position:40% 50%"></div>
        <div class="ph"><img loading="lazy" src="img/f-detalhe.jpg" alt="Caderno da MB sobre a mesa" style="object-position:50% 40%"></div>
        <div class="gold-tile"><img loading="lazy" src="img/logo-simbolo.png" alt=""></div>
      </div>
      <div>
        <div class="label"><i></i><i></i>Quem Somos</div>
        <p class="big">Na MB Consultoria e Assessoria Política, desenvolvemos estratégias que vão além da comunicação convencional. Nosso trabalho é compreender a essência de cada liderança, instituição ou projeto para construir posicionamentos autênticos, fortalecer reputações e aproximar pessoas.</p>
        <p class="mute">Unimos conhecimento político, planejamento estratégico e a comunicação para transformar propósitos em mensagens que fazem sentido para a sociedade.</p>
        <p style="margin-top:28px"><a class="btn btn-navy" href="#quem-somos">Quem somos <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></a></p>
      </div>
    </div>
  </div>

  <div class="blk band hatch dark">
    <div class="wrap">
      <div class="sec-head split">
        <div><div class="label"><i></i><i></i>Soluções</div><h2 style="margin-top:16px">Mais do que comunicar, é preciso construir conexões.</h2></div>
        <div class="side"><p>Atuamos em diferentes momentos da vida pública, oferecendo soluções personalizadas que acompanham desde a construção do posicionamento até a execução das estratégias de comunicação.</p><a class="btn btn-white" href="#solucoes">Conheça nossos serviços <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></a></div>
      </div>
      <div class="svc-grid">
        <a class="svc hl" href="#solucoes" data-anchor="s-comunicacao"><div class="in"><div class="top"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m3 11 18-5v12L3 14v-3z" /> <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" /> </svg></span><span class="n">01</span></div><h3>Comunicação Política</h3><p>Acompanhamento estratégico e contínuo para lideranças, mandatos, partidos e instituições.</p><div class="go"><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></div></a>
        <a class="svc" href="#solucoes" data-anchor="s-execucao"><div class="in"><div class="top"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z" /> <path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65" /> <path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65" /> </svg></span><span class="n">02</span></div><h3>Consultoria e Execução</h3><p>Estratégia e operação integradas, do planejamento à gestão dos canais.</p><div class="go"><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></div></a>
        <a class="svc" href="#solucoes" data-anchor="s-treinamentos"><div class="in"><div class="top"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M2 3h20" /> <path d="M21 3v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3" /> <path d="m7 21 5-5 5 5" /> </svg></span><span class="n">03</span></div><h3>Treinamentos e Workshops</h3><p>Preparação de pessoas e equipes que atuam no ambiente político e institucional.</p><div class="go"><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></div></a>
        <a class="svc" href="#solucoes" data-anchor="s-mandato"><div class="in"><div class="top"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <line x1="3" x2="21" y1="22" y2="22" /> <line x1="6" x2="6" y1="18" y2="11" /> <line x1="10" x2="10" y1="18" y2="11" /> <line x1="14" x2="14" y1="18" y2="11" /> <line x1="18" x2="18" y1="18" y2="11" /> <polygon points="12 2 20 7 4 7" /> </svg></span><span class="n">04</span></div><h3>Planejamento de Mandato</h3><p>Direção, organização e comunicação permanente ao longo da atuação parlamentar.</p><div class="go"><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></div></a>
        <a class="svc" href="#solucoes" data-anchor="s-campanhas"><div class="in"><div class="top"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m9 12 2 2 4-4" /> <path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z" /> <path d="M22 19H2" /> </svg></span><span class="n">05</span></div><h3>Campanhas Eleitorais</h3><p>Da análise de cenário à execução completa da comunicação de campanha.</p><div class="go"><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></div></a>
      </div>
    </div>
  </div>

  <div class="blk white">
    <div class="wrap">
      <div class="sec-head center">
        <div class="label"><i></i><i></i>Nossos diferenciais</div>
        <h2 class="h-wide">Uma comunicação mais estratégica, ética, autêntica e próxima das pessoas.</h2>
      </div>
      <div class="why">
        <div class="why-photo">
          <img loading="lazy" src="img/eq-mariana.jpg" alt="Mariana Bonjour na recepção da MB Consultoria e Assessoria Política">
          <span class="why-badge"><img loading="lazy" src="img/logo-simbolo.png" alt=""></span>
        </div>
        <div class="bento">
          <div class="bt navy c-a"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z" /> <circle cx="12" cy="12" r="10" /> </svg></span><span class="wm"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z" /> <circle cx="12" cy="12" r="10" /> </svg></span><h3>Propósito</h3><p>Antes de construir uma mensagem, buscamos compreender a história, os valores e aquilo que realmente move cada liderança.</p></div>
          <div class="bt soft c-b"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4" /> <path d="M14 13.12c0 2.38 0 6.38-1 8.88" /> <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02" /> <path d="M2 12a10 10 0 0 1 18-6" /> <path d="M2 16h.01" /> <path d="M21.8 16c.2-2 .131-5.354 0-6" /> <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2" /> <path d="M8.65 22c.21-.66.45-1.32.57-2" /> <path d="M9 6.8a6 6 0 0 1 9 5.2v2" /> </svg></span><span class="wm"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4" /> <path d="M14 13.12c0 2.38 0 6.38-1 8.88" /> <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02" /> <path d="M2 12a10 10 0 0 1 18-6" /> <path d="M2 16h.01" /> <path d="M21.8 16c.2-2 .131-5.354 0-6" /> <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2" /> <path d="M8.65 22c.21-.66.45-1.32.57-2" /> <path d="M9 6.8a6 6 0 0 1 9 5.2v2" /> </svg></span><h3>Autenticidade</h3><p>Acreditamos que uma comunicação consistente precisa respeitar a identidade de quem comunica.</p></div>
          <div class="bt soft c-c"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <circle cx="12" cy="12" r="6" /> <circle cx="12" cy="12" r="2" /> </svg></span><span class="wm"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <circle cx="12" cy="12" r="6" /> <circle cx="12" cy="12" r="2" /> </svg></span><h3>Estratégia</h3><p>Cada ação é orientada por planejamento, análise de cenário e objetivos definidos.</p></div>
          <div class="bt deep c-d"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m11 17 2 2a1 1 0 1 0 3-3" /> <path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4" /> <path d="m21 3 1 11h-2" /> <path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3" /> <path d="M3 4h8" /> </svg></span><span class="wm"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m11 17 2 2a1 1 0 1 0 3-3" /> <path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4" /> <path d="m21 3 1 11h-2" /> <path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3" /> <path d="M3 4h8" /> </svg></span><h3>Proximidade</h3><p>Valorizamos a escuta, o relacionamento e a construção de conexões reais com as pessoas.</p></div>
          <div class="bt navy c-e"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 12c-2-2.67-4-4-6-4a4 4 0 1 0 0 8c2 0 4-1.33 6-4Zm0 0c2 2.67 4 4 6 4a4 4 0 0 0 0-8c-2 0-4 1.33-6 4Z" /> </svg></span><span class="wm"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 12c-2-2.67-4-4-6-4a4 4 0 1 0 0 8c2 0 4-1.33 6-4Zm0 0c2 2.67 4 4 6 4a4 4 0 0 0 0-8c-2 0-4 1.33-6 4Z" /> </svg></span><h3>Atuação integrada</h3><p>Oferecemos desde o acompanhamento estratégico até a execução completa da comunicação.</p></div>
          <div class="bt gold c-f"><p>Nosso trabalho começa pela compreensão do verdadeiro propósito por trás de cada figura pública.</p><a class="btn btn-navy" href="#contato">Converse com a MB <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></a></div>
        </div>
      </div>
    </div>
  </div>

  <div class="marquee" data-marquee aria-hidden="true"></div>

  <div class="blk">
    <div class="wrap">
      <div class="sec-head split">
        <div><div class="label"><i></i><i></i>Experiências</div><h2 style="margin-top:16px">Experiências que fazem parte da nossa história.</h2></div>
        <div class="side"><p class="mute">Nossa trajetória reúne experiências com lideranças, partidos e projetos políticos.</p><a class="btn btn-navy" href="#experiencias">Ver todas as experiências <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></a></div>
      </div>
      <div class="rail" id="homeStrip" role="region" aria-roledescription="carrossel" aria-label="Experiências da MB"><div class="rail-track"></div></div>
      <div class="rail-ctl">
        <button type="button" class="rail-btn" id="railPrev" aria-label="Anterior"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m12 19-7-7 7-7" /> <path d="M19 12H5" /> </svg></button>
        <button type="button" class="rail-btn" id="railPause" aria-label="Pausar movimento" aria-pressed="false"><span class="i-pause"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <rect x="14" y="4" width="4" height="16" rx="1" /> <rect x="6" y="4" width="4" height="16" rx="1" /> </svg></span><span class="i-play"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <polygon points="6 3 20 12 6 21 6 3" /> </svg></span></button>
        <button type="button" class="rail-btn" id="railNext" aria-label="Próximo"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></button>
      </div>
    </div>
  </div>

  <div class="blk white">
    <div class="wrap">
      <div class="sec-head center">
        <div class="label"><i></i><i></i>Nossas sedes</div>
        <h2>Presença estratégica em Cuiabá e Brasília.</h2>
        <p class="mute">Dois importantes centros de articulação política e institucional do país.</p>
      </div>
      <div class="duo">
        <a class="place-card" href="#sedes" data-anchor="u-cuiaba"><div class="ph"><img loading="lazy" src="img/cba-fachada.jpg" alt="Fachada da MB em Cuiabá"></div><div class="row"><span class="pill">Mato Grosso</span></div><div class="row2"><div><h3>MB Cuiabá</h3><p>Avenida Roma, nº 19, Jardim Itália</p></div><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></a>
        <a class="place-card" href="#sedes" data-anchor="u-brasilia"><div class="ph"><img loading="lazy" src="img/bsb-sala.jpg" alt="Sala de reunião da MB em Brasília"></div><div class="row"><span class="pill">Distrito Federal</span></div><div class="row2"><div><h3>MB Brasília</h3><p>Edifício Terra Brasilis, SAUS, Quadra 1, Bloco N, Sala 712</p></div><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></a>
      </div>
    </div>
  </div>

  <div class="blk" data-cta></div>
</section>

<!-- ============ QUEM SOMOS ============ -->
<section class="page" data-page="quem-somos">
  <div class="ph-wrap">
    <div class="ph-hdr hatch">
      <img loading="lazy" class="hero-mark" src="img/logo-simbolo.png" alt="">
      <div class="ph-in">
        <div>
          <div class="label light"><i></i><i></i>Quem Somos</div>
          <h1>Uma forma diferente de fazer <span class="it gold-t">comunicação.</span></h1>
          <p class="lead">A MB Consultoria e Assessoria Política é uma empresa especializada em marketing político e comunicação governamental que acredita em uma forma diferente de fazer comunicação: mais estratégica, ética, autêntica e próxima das pessoas.</p>
        </div>
        <div class="ph-photo"><img src="img/f-duas.jpg" alt="Consultoras da MB no escritório"></div>
      </div>
    </div>
  </div>

  <div class="blk">
    <div class="wrap">
      <div class="big-card">
        <div class="two">
          <div class="photo tall"><img loading="lazy" src="img/f-direcao.jpg" alt="Sala de direção da MB em Cuiabá"></div>
          <div class="measure">
            <div class="label"><i></i><i></i>Nossa atuação</div>
            <h2 style="margin:16px 0 24px">Tudo começa pelo propósito.</h2>
            <p>Nosso trabalho começa pela compreensão do verdadeiro propósito por trás de cada figura pública. Buscamos entender histórias, valores, trajetórias e objetivos para desenvolver estratégias que respeitem a identidade de cada liderança e fortaleçam sua conexão com a sociedade.</p>
            <p>Atuamos ao lado de políticos, candidatos, partidos, instituições e gestões públicas, oferecendo soluções que integram planejamento, posicionamento, construção de narrativas e execução de comunicação.</p>
            <p>Com sedes em Cuiabá e Brasília, reunimos o conhecimento político e a expertise em comunicação necessárias para desenvolver projetos personalizados, respeitando os desafios e as particularidades de cada cenário.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="marquee" data-marquee aria-hidden="true"></div>

  <div class="blk band hatch dark">
    <div class="wrap">
      <div class="sec-head center"><div class="label"><i></i><i></i>Nossos diferenciais</div><h2>O que guia cada projeto.</h2></div>
      <div class="diff-grid">
        <div class="diff"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m16.24 7.76-1.804 5.411a2 2 0 0 1-1.265 1.265L7.76 16.24l1.804-5.411a2 2 0 0 1 1.265-1.265z" /> <circle cx="12" cy="12" r="10" /> </svg></span><h3>Propósito</h3><p>Antes de construir uma mensagem, buscamos compreender a história, os valores e aquilo que realmente move cada liderança.</p></div>
        <div class="diff"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4" /> <path d="M14 13.12c0 2.38 0 6.38-1 8.88" /> <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02" /> <path d="M2 12a10 10 0 0 1 18-6" /> <path d="M2 16h.01" /> <path d="M21.8 16c.2-2 .131-5.354 0-6" /> <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2" /> <path d="M8.65 22c.21-.66.45-1.32.57-2" /> <path d="M9 6.8a6 6 0 0 1 9 5.2v2" /> </svg></span><h3>Autenticidade</h3><p>Acreditamos que uma comunicação consistente precisa respeitar a identidade de quem comunica.</p></div>
        <div class="diff"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <circle cx="12" cy="12" r="6" /> <circle cx="12" cy="12" r="2" /> </svg></span><h3>Estratégia</h3><p>Cada ação é orientada por planejamento, análise de cenário e objetivos definidos.</p></div>
        <div class="diff"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m11 17 2 2a1 1 0 1 0 3-3" /> <path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4" /> <path d="m21 3 1 11h-2" /> <path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3" /> <path d="M3 4h8" /> </svg></span><h3>Proximidade</h3><p>Valorizamos a escuta, o relacionamento e a construção de conexões reais com as pessoas.</p></div>
        <div class="diff"><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 12c-2-2.67-4-4-6-4a4 4 0 1 0 0 8c2 0 4-1.33 6-4Zm0 0c2 2.67 4 4 6 4a4 4 0 0 0 0-8c-2 0-4 1.33-6 4Z" /> </svg></span><h3>Atuação integrada</h3><p>Oferecemos desde o acompanhamento estratégico até a execução completa da comunicação, conforme as necessidades de cada projeto.</p></div>
      </div>
    </div>
  </div>

  <div class="blk">
    <div class="wrap">
      <div class="duo">
        <a class="place-card" href="#sedes" data-anchor="u-cuiaba"><div class="ph"><img loading="lazy" src="img/cba-recepcao.jpg" alt="Recepção da MB em Cuiabá"></div><div class="row"><span class="pill">Mato Grosso</span></div><div class="row2"><div><h3>MB Cuiabá</h3><p>Conheça a sede</p></div><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></a>
        <a class="place-card" href="#sedes" data-anchor="u-brasilia"><div class="ph"><img loading="lazy" src="img/bsb-recepcao.jpg" alt="Recepção da MB em Brasília"></div><div class="row"><span class="pill">Distrito Federal</span></div><div class="row2"><div><h3>MB Brasília</h3><p>Conheça a sede</p></div><span class="circle"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></span></div></a>
      </div>
    </div>
  </div>

  <div class="blk" data-cta style="padding-top:0"></div>
</section>

<!-- ============ EQUIPE ============ -->
<section class="page" data-page="equipe">
  <div class="ph-wrap">
    <div class="ph-hdr solo hatch">
      <img loading="lazy" class="hero-mark" src="img/logo-simbolo.png" alt="">
      <div class="ph-in">
        <div>
          <div class="label light"><i></i><i></i>MB Consultoria</div>
          <h1>Nossa <span class="it gold-t">Equipe.</span></h1>
          <p class="lead">Por trás de cada estratégia, existem pessoas que acreditam no poder de uma comunicação construída com propósito.</p>
          <p style="margin-top:16px;max-width:62ch">Nossa equipe reúne diferentes competências para compreender diferentes cenários, desenvolver soluções personalizadas e transformar planejamento em comunicação.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="blk">
    <div class="wrap">
      <div class="big-card">
        <div class="lead-member">
          <div class="ph"><img loading="lazy" src="img/eq-mariana.jpg" alt="Mariana Bonjour"></div>
          <div class="measure">
            <div class="label"><i></i><i></i>Sócia-fundadora</div>
            <h2 style="margin-top:16px">Mariana Bonjour</h2>
            <p class="role">Sócia-fundadora · Consultora-chefe de Comunicação Governamental e Marketing Político</p>
            <p>À frente da MB Consultoria e Assessoria Política, Mariana Bonjour atua no desenvolvimento de estratégias de posicionamento, reputação e comunicação para lideranças e projetos políticos.</p>
            <p>Sua trajetória profissional reúne experiência em comunicação política e governamental, planejamento estratégico e conhecimento das dinâmicas institucionais.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="blk tight" style="padding-top:0">
    <div class="wrap"><div class="quote-card"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M16 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z" /> <path d="M5 3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2 1 1 0 0 1 1 1v1a2 2 0 0 1-2 2 1 1 0 0 0-1 1v2a1 1 0 0 0 1 1 6 6 0 0 0 6-6V5a2 2 0 0 0-2-2z" /> </svg><p>Não vendemos fórmulas mágicas. <span class="it">Aplicamos métodos testados e aprovados.</span></p></div></div>
  </div>

  <div class="blk">
    <div class="wrap">
      <div class="sec-head center"><div class="label"><i></i><i></i>Consultores e direção de arte</div></div>
      <div class="team" id="team"></div>
    </div>
  </div>

  <div class="blk" data-cta style="padding-top:0"></div>
</section>

<!-- ============ SOLUÇÕES ============ -->
<section class="page" data-page="solucoes">
  <div class="ph-wrap">
    <div class="ph-hdr hatch">
      <img loading="lazy" class="hero-mark" src="img/logo-simbolo.png" alt="">
      <div class="ph-in">
        <div>
          <div class="label light"><i></i><i></i>Nossas Soluções</div>
          <h1>Cada projeto tem uma história. <span class="it gold-t">Cada desafio exige uma estratégia.</span></h1>
          <p class="lead">Na MB Consultoria e Assessoria Política, desenvolvemos soluções personalizadas para diferentes momentos da vida política.</p>
          <p style="margin-top:16px">Nossa atuação combina conhecimento político, planejamento e comunicação, oferecendo desde o acompanhamento estratégico até a execução completa das ações.</p>
        </div>
        <div class="ph-photo"><img src="img/f-mesa.jpg" alt="Consultora trabalhando no escritório da MB"></div>
      </div>
    </div>
  </div>

  <nav class="subnav" aria-label="Soluções">
    <div class="wrap">
      <a href="#solucoes" data-anchor="s-comunicacao">Comunicação Política</a>
      <a href="#solucoes" data-anchor="s-execucao">Consultoria e Execução</a>
      <a href="#solucoes" data-anchor="s-treinamentos">Treinamentos e Workshops</a>
      <a href="#solucoes" data-anchor="s-mandato">Planejamento de Mandato</a>
      <a href="#solucoes" data-anchor="s-campanhas">Campanhas Eleitorais</a>
    </div>
  </nav>

  <div class="blk">
    <div class="wrap">
      <div class="sec-head split">
        <div><div class="label"><i></i><i></i>Consultoria</div><h2 style="margin-top:16px">Estratégia contínua ou estratégia com execução.</h2></div>
        <div class="side"><p class="mute">Você escolhe o nível de envolvimento da MB: acompanhamento estratégico, ou a estratégia somada à equipe que produz e gerencia a comunicação.</p></div>
      </div>
      <div class="compare">
        <div id="s-comunicacao" class="anchor">
          <span class="tag">Consultoria estratégica</span>
          <h3>Comunicação Política</h3>
          <p>Acompanhamento estratégico e contínuo para lideranças, mandatos, partidos e instituições que desejam construir uma comunicação consistente e alinhada aos seus objetivos.</p>
          <p>O trabalho envolve planejamento, definição de estratégias, detalhamento de ações, posicionamento, construção de narrativas, orientação para produção de conteúdo e reuniões periódicas de alinhamento.</p>
          <p class="deliv-h">Principais entregas</p>
          <ul class="deliv">
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Planejamento estratégico de comunicação</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Análise de cenário e posicionamento</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Construção de narrativas</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Direcionamento e detalhamento de ações</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Orientação para produção de conteúdo</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Reuniões de acompanhamento e alinhamento</li>
          </ul>
        </div>
        <div id="s-execucao" class="exec anchor">
          <span class="tag">Estratégia + execução</span>
          <h3>Consultoria e Execução de Comunicação</h3>
          <p>Para projetos que precisam de estratégia e operação integradas, a MB oferece acompanhamento estratégico aliado à execução das ações de comunicação.</p>
          <p>Além do planejamento e direcionamento, nossa equipe desenvolve e gerencia os materiais e canais necessários para colocar a estratégia em prática.</p>
          <p class="deliv-h">Principais entregas</p>
          <ul class="deliv">
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Planejamento e consultoria estratégica</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Produção de vídeos</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Criação de peças gráficas</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Gestão de redes sociais</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Impulsionamento e tráfego pago</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Assessoria de imprensa</li>
            <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Acompanhamento e alinhamento das ações</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="marquee" data-marquee aria-hidden="true"></div>

  <div class="blk anchor" id="s-treinamentos">
    <div class="wrap">
      <div class="big-card">
        <div class="two">
          <div class="photo"><img loading="lazy" src="img/g-workshop.jpg" alt="Workshop de comunicação política com palestrante e participantes"></div>
          <div class="measure">
            <span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M2 3h20" /> <path d="M21 3v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3" /> <path d="m7 21 5-5 5 5" /> </svg></span>
            <h2 style="margin:22px 0 20px">Treinamentos e Workshops</h2>
            <p class="lead" style="font-size:18px;font-weight:500;color:var(--navy)">A comunicação política também se fortalece por meio da preparação de pessoas e equipes.</p>
            <p>Desenvolvemos treinamentos e workshops voltados ao aprimoramento da comunicação, ao alinhamento estratégico e à qualificação de profissionais que atuam no ambiente político e institucional.</p>
            <p>Os conteúdos são adaptados às necessidades e aos objetivos de cada grupo.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="blk anchor" id="s-mandato" style="padding-top:0">
    <div class="wrap">
      <div class="big-card">
        <div class="two rev">
          <div class="photo"><img loading="lazy" src="img/cba-mapa.jpg" alt="Mapa de Mato Grosso na parede da MB em Cuiabá"></div>
          <div class="measure">
            <span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <line x1="3" x2="21" y1="22" y2="22" /> <line x1="6" x2="6" y1="18" y2="11" /> <line x1="10" x2="10" y1="18" y2="11" /> <line x1="14" x2="14" y1="18" y2="11" /> <line x1="18" x2="18" y1="18" y2="11" /> <polygon points="12 2 20 7 4 7" /> </svg></span>
            <h2 style="margin:22px 0 20px">Planejamento de Mandato</h2>
            <p class="lead" style="font-size:18px;font-weight:500;color:var(--navy)">Um mandato consistente precisa de direção, organização e comunicação permanente.</p>
            <p>A MB desenvolve planejamentos que auxiliam lideranças a organizar prioridades, definir posicionamentos e estruturar a comunicação ao longo de sua atuação parlamentar.</p>
            <p>Nosso trabalho busca conectar os objetivos políticos às demandas da população, fortalecendo a identidade do mandato e sua relação com a sociedade.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="blk band hatch dark anchor" id="s-campanhas">
    <div class="wrap">
      <div class="sec-head split">
        <div><span class="chip-ic"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m9 12 2 2 4-4" /> <path d="M5 7c0-1.1.9-2 2-2h10a2 2 0 0 1 2 2v12H5V7Z" /> <path d="M22 19H2" /> </svg></span><h2 style="margin-top:20px">Campanhas Eleitorais</h2><p class="lead" style="margin-top:18px;color:#e2dfd6">Uma campanha exige estratégia, planejamento e capacidade de execução.</p></div>
        <div class="side">
          <p>A MB atua em diferentes etapas do processo eleitoral, desde a análise de cenário e construção do posicionamento até o planejamento e a execução completa da comunicação.</p>
          <p>Desenvolvemos estratégias personalizadas que consideram a trajetória do candidato, o contexto político, os objetivos da campanha e a conexão com o eleitorado.</p>
        </div>
      </div>
      <p class="deliv-h" style="margin:0 0 6px">Nossa atuação pode envolver</p>
      <ul class="camp-list">
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Diagnóstico e análise de cenário</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Planejamento estratégico eleitoral</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Posicionamento e construção de narrativas</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Planejamento de comunicação</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Produção de conteúdo e materiais de campanha</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Gestão de redes sociais</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Tráfego pago e impulsionamento</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Assessoria de imprensa</li>
        <li><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <circle cx="12" cy="12" r="10" /> <path d="m9 12 2 2 4-4" /> </svg>Acompanhamento estratégico e execução da comunicação</li>
      </ul>
    </div>
  </div>

  <div class="blk" data-cta data-title="Converse com nossos consultores." data-btn="Converse com nossos consultores"></div>
</section>

<!-- ============ EXPERIÊNCIAS ============ -->
<section class="page" data-page="experiencias">
  <div class="ph-wrap">
    <div class="ph-hdr solo hatch">
      <img loading="lazy" class="hero-mark" src="img/logo-simbolo.png" alt="">
      <div class="ph-in">
        <div>
          <div class="label light"><i></i><i></i>Clientes e Experiências</div>
          <h1>Experiências que fazem parte <span class="it gold-t">da nossa história.</span></h1>
          <p class="lead">Cada projeto representa um novo cenário, um desafio e uma oportunidade de construir estratégias de comunicação.</p>
          <p style="margin-top:16px;max-width:62ch">Nossa trajetória reúne experiências com lideranças, partidos e projetos políticos que reforçam a importância de uma comunicação planejada, autêntica e conectada aos objetivos de cada cliente.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="blk">
    <div class="wrap">
      <div class="filters" id="filters" role="group" aria-label="Filtrar clientes"></div>
      <div class="clients" id="clients"></div>
    </div>
  </div>
  <div class="marquee" data-marquee aria-hidden="true"></div>
  <div class="blk" data-cta></div>
</section>

<!-- ============ SEDES ============ -->
<section class="page" data-page="sedes">
  <div class="ph-wrap">
    <div class="ph-hdr hatch">
      <img loading="lazy" class="hero-mark" src="img/logo-simbolo.png" alt="">
      <div class="ph-in">
        <div>
          <div class="label light"><i></i><i></i>Nossas Sedes</div>
          <h1>Presença estratégica em <span class="it gold-t">Cuiabá e Brasília.</span></h1>
          <p class="lead">A MB Consultoria e Assessoria Política está presente em dois importantes centros de articulação política e institucional do país.</p>
          <p style="margin-top:16px">Com estruturas em Cuiabá e Brasília, fortalecemos nossa presença e ampliamos as possibilidades de relacionamento, acompanhamento e desenvolvimento de projetos.</p>
        </div>
        <div class="ph-photo"><img src="img/f-fachada-h.jpg" alt="Fachada da MB em Cuiabá" style="object-position:62% 50%"></div>
      </div>
    </div>
  </div>

  <div class="blk anchor" id="u-cuiaba">
    <div class="wrap unit">
      <div class="unit-info">
        <span class="pill gold">Cuiabá · Mato Grosso</span>
        <h2>MB Cuiabá</h2>
        <p class="mute">Nossa estrutura em Cuiabá representa a conexão com o cenário político regional e o desenvolvimento de projetos de comunicação e consultoria.</p>
        <div class="addr"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" /> <circle cx="12" cy="10" r="3" /> </svg><div><b>Endereço</b>Avenida Roma, nº 19, Jardim Itália<br>Cuiabá — MT</div></div>
        <a class="btn btn-navy" href="https://www.google.com/maps/search/?api=1&amp;query=Avenida%20Roma%2019%20Jardim%20It%C3%A1lia%20Cuiab%C3%A1%20MT" target="_blank" rel="noopener">Ver no mapa <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></a>
      </div>
      <div>
        <p class="gal-h">Conheça a MB Cuiabá</p>
        <div class="gallery" id="gal-cba"></div>
      </div>
    </div>
  </div>

  <div class="blk band hatch dark anchor" id="u-brasilia">
    <div class="wrap unit">
      <div class="unit-info">
        <span class="pill gold">Brasília · Distrito Federal</span>
        <h2>MB Brasília</h2>
        <p>Nossa estrutura em Brasília amplia a presença institucional da consultoria na capital federal, aproximando a MB de importantes espaços de articulação política.</p>
        <div class="addr"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" /> <circle cx="12" cy="10" r="3" /> </svg><div><b>Endereço</b>Edifício Terra Brasilis<br>SAUS, Quadra 1, Bloco N, Sala 712 — Asa Sul<br>Brasília — DF</div></div>
        <a class="btn btn-gold" href="https://www.google.com/maps/search/?api=1&amp;query=Edif%C3%ADcio%20Terra%20Brasilis%20SAUS%20Quadra%201%20Bloco%20N%20Bras%C3%ADlia" target="_blank" rel="noopener">Ver no mapa <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg></a>
      </div>
      <div>
        <p class="gal-h">Conheça a MB Brasília</p>
        <div class="gallery" id="gal-bsb"></div>
      </div>
    </div>
  </div>

  <div class="blk" data-cta data-title="Agende uma visita ou uma conversa." data-btn="Agende uma conversa com a MB" data-img="img/bsb-lounge-cta.jpg" data-alt="Recepção da MB em Brasília, com a marca na parede e poltronas de couro" data-pos="38% 50%"></div>
</section>

<!-- ============ CONTEÚDOS ============ -->
<section class="page" data-page="conteudos">
  <div class="ph-wrap">
    <div class="ph-hdr solo hatch">
      <img loading="lazy" class="hero-mark" src="img/logo-simbolo.png" alt="">
      <div class="ph-in">
        <div>
          <div class="label light"><i></i><i></i>Conteúdos e Análises</div>
          <h1>A política está em constante movimento. <span class="it gold-t">Nossa análise também.</span></h1>
          <p class="lead">Acompanhe reflexões, tendências e conteúdos sobre comunicação política, marketing eleitoral, gestão pública e posicionamento.</p>
          <p style="margin-top:16px;max-width:62ch">Um espaço para compartilhar conhecimento, interpretar cenários e discutir os desafios da comunicação no ambiente político.</p>
        </div>
      </div>
    </div>
  </div>
  <div class="blk">
    <div class="wrap">
      <?= render_conteudos_block() ?>
    </div>
  </div>
  <div class="blk tight" style="padding-top:0">
    <div class="wrap">
      <div class="insta hatch">
        <div><div class="label light"><i></i><i></i>Redes sociais</div><h2>Acompanhe a MB no Instagram</h2><p><?= e($S['instagram_handle']) ?></p></div>
        <a class="btn btn-gold" href="<?= e($S['instagram']) ?>" target="_blank" rel="noopener"><svg class="ic ic-b" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7.0301.084c-1.2768.0602-2.1487.264-2.911.5634-.7888.3075-1.4575.72-2.1228 1.3877-.6652.6677-1.075 1.3368-1.3802 2.127-.2954.7638-.4956 1.6365-.552 2.914-.0564 1.2775-.0689 1.6882-.0626 4.947.0062 3.2586.0206 3.6671.0825 4.9473.061 1.2765.264 2.1482.5635 2.9107.308.7889.72 1.4573 1.388 2.1228.6679.6655 1.3365 1.0743 2.1285 1.38.7632.295 1.6361.4961 2.9134.552 1.2773.056 1.6884.069 4.9462.0627 3.2578-.0062 3.668-.0207 4.9478-.0814 1.28-.0607 2.147-.2652 2.9098-.5633.7889-.3086 1.4578-.72 2.1228-1.3881.665-.6682 1.0745-1.3378 1.3795-2.1284.2957-.7632.4966-1.636.552-2.9124.056-1.2809.0692-1.6898.063-4.948-.0063-3.2583-.021-3.6668-.0817-4.9465-.0607-1.2797-.264-2.1487-.5633-2.9117-.3084-.7889-.72-1.4568-1.3876-2.1228C21.2982 1.33 20.628.9208 19.8378.6165 19.074.321 18.2017.1197 16.9244.0645 15.6471.0093 15.236-.005 11.977.0014 8.718.0076 8.31.0215 7.0301.0839m.1402 21.6932c-1.17-.0509-1.8053-.2453-2.2287-.408-.5606-.216-.96-.4771-1.3819-.895-.422-.4178-.6811-.8186-.9-1.378-.1644-.4234-.3624-1.058-.4171-2.228-.0595-1.2645-.072-1.6442-.079-4.848-.007-3.2037.0053-3.583.0607-4.848.05-1.169.2456-1.805.408-2.2282.216-.5613.4762-.96.895-1.3816.4188-.4217.8184-.6814 1.3783-.9003.423-.1651 1.0575-.3614 2.227-.4171 1.2655-.06 1.6447-.072 4.848-.079 3.2033-.007 3.5835.005 4.8495.0608 1.169.0508 1.8053.2445 2.228.408.5608.216.96.4754 1.3816.895.4217.4194.6816.8176.9005 1.3787.1653.4217.3617 1.056.4169 2.2263.0602 1.2655.0739 1.645.0796 4.848.0058 3.203-.0055 3.5834-.061 4.848-.051 1.17-.245 1.8055-.408 2.2294-.216.5604-.4763.96-.8954 1.3814-.419.4215-.8181.6811-1.3783.9-.4224.1649-1.0577.3617-2.2262.4174-1.2656.0595-1.6448.072-4.8493.079-3.2045.007-3.5825-.006-4.848-.0608M16.953 5.5864A1.44 1.44 0 1 0 18.39 4.144a1.44 1.44 0 0 0-1.437 1.4424M5.8385 12.012c.0067 3.4032 2.7706 6.1557 6.173 6.1493 3.4026-.0065 6.157-2.7701 6.1506-6.1733-.0065-3.4032-2.771-6.1565-6.174-6.1498-3.403.0067-6.156 2.771-6.1496 6.1738M8 12.0077a4 4 0 1 1 4.008 3.9921A3.9996 3.9996 0 0 1 8 12.0077"/></svg> Seguir no Instagram</a>
      </div>
    </div>
  </div>
</section>

<!-- ============ CONTATO ============ -->
<section class="page" data-page="contato">
  <div class="blk tight">
    <div class="wrap contact">
      <div class="side hatch">
        <div class="label light"><i></i><i></i>Contato</div>
        <h1>Vamos construir sua próxima <span class="it gold-t">estratégia?</span></h1>
        <p class="lead" style="color:#e2dfd6">Toda boa comunicação começa com uma conversa.</p>
        <p style="margin-top:14px">Entre em contato com a equipe da MB Consultoria e Assessoria Política e descubra como podemos contribuir para o desenvolvimento do seu projeto.</p>
        <p class="subhead">Contato comercial</p>
        <ul class="clist" style="margin-top:14px">
          <li><span class="ico"><svg class="ic ic-b" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg></span><div><small>Telefone / WhatsApp</small><a href="https://wa.me/<?= e($S['whatsapp']) ?>" target="_blank" rel="noopener"><?= e($S['phone']) ?></a></div></li>
          <li><span class="ico"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <rect width="20" height="16" x="2" y="4" rx="2" /> <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" /> </svg></span><div><small>E-mail</small><a href="mailto:<?= e($S['email']) ?>"><?= e($S['email']) ?></a></div></li>
          <li><span class="ico"><svg class="ic ic-b" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7.0301.084c-1.2768.0602-2.1487.264-2.911.5634-.7888.3075-1.4575.72-2.1228 1.3877-.6652.6677-1.075 1.3368-1.3802 2.127-.2954.7638-.4956 1.6365-.552 2.914-.0564 1.2775-.0689 1.6882-.0626 4.947.0062 3.2586.0206 3.6671.0825 4.9473.061 1.2765.264 2.1482.5635 2.9107.308.7889.72 1.4573 1.388 2.1228.6679.6655 1.3365 1.0743 2.1285 1.38.7632.295 1.6361.4961 2.9134.552 1.2773.056 1.6884.069 4.9462.0627 3.2578-.0062 3.668-.0207 4.9478-.0814 1.28-.0607 2.147-.2652 2.9098-.5633.7889-.3086 1.4578-.72 2.1228-1.3881.665-.6682 1.0745-1.3378 1.3795-2.1284.2957-.7632.4966-1.636.552-2.9124.056-1.2809.0692-1.6898.063-4.948-.0063-3.2583-.021-3.6668-.0817-4.9465-.0607-1.2797-.264-2.1487-.5633-2.9117-.3084-.7889-.72-1.4568-1.3876-2.1228C21.2982 1.33 20.628.9208 19.8378.6165 19.074.321 18.2017.1197 16.9244.0645 15.6471.0093 15.236-.005 11.977.0014 8.718.0076 8.31.0215 7.0301.0839m.1402 21.6932c-1.17-.0509-1.8053-.2453-2.2287-.408-.5606-.216-.96-.4771-1.3819-.895-.422-.4178-.6811-.8186-.9-1.378-.1644-.4234-.3624-1.058-.4171-2.228-.0595-1.2645-.072-1.6442-.079-4.848-.007-3.2037.0053-3.583.0607-4.848.05-1.169.2456-1.805.408-2.2282.216-.5613.4762-.96.895-1.3816.4188-.4217.8184-.6814 1.3783-.9003.423-.1651 1.0575-.3614 2.227-.4171 1.2655-.06 1.6447-.072 4.848-.079 3.2033-.007 3.5835.005 4.8495.0608 1.169.0508 1.8053.2445 2.228.408.5608.216.96.4754 1.3816.895.4217.4194.6816.8176.9005 1.3787.1653.4217.3617 1.056.4169 2.2263.0602 1.2655.0739 1.645.0796 4.848.0058 3.203-.0055 3.5834-.061 4.848-.051 1.17-.245 1.8055-.408 2.2294-.216.5604-.4763.96-.8954 1.3814-.419.4215-.8181.6811-1.3783.9-.4224.1649-1.0577.3617-2.2262.4174-1.2656.0595-1.6448.072-4.8493.079-3.2045.007-3.5825-.006-4.848-.0608M16.953 5.5864A1.44 1.44 0 1 0 18.39 4.144a1.44 1.44 0 0 0-1.437 1.4424M5.8385 12.012c.0067 3.4032 2.7706 6.1557 6.173 6.1493 3.4026-.0065 6.157-2.7701 6.1506-6.1733-.0065-3.4032-2.771-6.1565-6.174-6.1498-3.403.0067-6.156 2.771-6.1496 6.1738M8 12.0077a4 4 0 1 1 4.008 3.9921A3.9996 3.9996 0 0 1 8 12.0077"/></svg></span><div><small>Instagram</small><a href="<?= e($S['instagram']) ?>" target="_blank" rel="noopener"><?= e($S['instagram_handle']) ?></a></div></li>
          <?php if ($S['linkedin'] !== ''): ?><li><span class="ico"><svg class="ic ic-b" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></span><div><small>LinkedIn</small><a href="<?= e($S['linkedin']) ?>" target="_blank" rel="noopener">Ver perfil</a></div></li><?php endif; ?>
        </ul>
        <p class="subhead">Nossas unidades</p>
        <div class="units-mini" style="margin-top:14px">
          <div><b>Cuiabá — MT</b>Avenida Roma, nº 19, Jardim Itália.</div>
          <div><b>Brasília — DF</b>Edifício Terra Brasilis, SAUS, Quadra 1, Bloco N, Sala 712 — Asa Sul.</div>
        </div>
      </div>
      <div class="formside">
        <div class="label"><i></i><i></i>Formulário</div>
        <h2>Agende uma conversa com a MB</h2>
        <p class="mute">Preencha os campos e nossa equipe retorna o contato.</p>
        <form id="form" novalidate>
          <div class="hp" aria-hidden="true"><label>Não preencha este campo<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
          <div class="f full"><label for="f-nome">Nome completo</label><input id="f-nome" name="nome" autocomplete="name" required></div>
          <div class="f"><label for="f-email">E-mail</label><input id="f-email" name="email" type="email" autocomplete="email" required></div>
          <div class="f"><label for="f-tel">Telefone / WhatsApp</label><input id="f-tel" name="telefone" type="tel" autocomplete="tel" required></div>
          <div class="f"><label for="f-inst">Instituição ou projeto</label><input id="f-inst" name="instituicao"></div>
          <div class="f"><label for="f-assunto">Assunto</label>
            <select id="f-assunto" name="assunto">
              <option>Comunicação Política</option>
              <option>Consultoria e Execução</option>
              <option>Treinamentos e Workshops</option>
              <option>Planejamento de Mandato</option>
              <option>Campanhas Eleitorais</option>
              <option>Outro assunto</option>
            </select></div>
          <div class="f full"><label for="f-msg">Mensagem</label><textarea id="f-msg" name="mensagem"></textarea></div>
          <label class="consent" for="f-ok"><input type="checkbox" id="f-ok" required><span>Concordo com o uso dos meus dados para retorno do contato, conforme a <a href="#privacidade">Política de Privacidade</a>.</span></label>
          <div style="grid-column:1/-1"><button class="btn btn-navy" type="submit" style="border:0">Agende uma conversa com a MB <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></button></div>
          <div class="formmsg" id="formmsg" role="status"></div>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- ============ PRIVACIDADE ============ -->
<section class="page" data-page="privacidade">
  <div class="ph-wrap"><div class="ph-hdr solo hatch"><div class="ph-in"><div><div class="label light"><i></i><i></i>Institucional</div><h1>Política de Privacidade</h1></div></div></div></div>
  <div class="blk">
    <div class="wrap legal">
      <p class="pending">O texto da Política de Privacidade será fornecido pela MB Consultoria e Assessoria Política. Ele deve explicar quais dados o formulário de contato coleta, para que são usados, por quanto tempo ficam guardados e como o titular pode pedir a exclusão, conforme a LGPD.</p>
    </div>
  </div>
</section>

</main>

<div class="marquee" data-marquee aria-hidden="true"></div>
<?php include __DIR__ . '/inc/partials/footer.php'; ?>



<dialog id="bioDlg" aria-labelledby="bioName"><div class="bio"><div class="p"><img id="bioImg" alt=""></div><div class="t"><button class="x" type="button" data-close aria-label="Fechar"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M18 6 6 18" /> <path d="m6 6 12 12" /> </svg></button><div class="label"><i></i><i></i>Equipe MB</div><h3 id="bioName"></h3><p class="role" id="bioRole"></p><div id="bioText"></div></div></div></dialog>

<dialog id="lbDlg" aria-label="Galeria de fotos"><div class="lb"><img id="lbImg" alt=""><button class="x" type="button" data-close aria-label="Fechar"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M18 6 6 18" /> <path d="m6 6 12 12" /> </svg></button><button class="nv prev" type="button" id="lbPrev" aria-label="Foto anterior"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m15 18-6-6 6-6" /> </svg></button><button class="nv next" type="button" id="lbNext" aria-label="Próxima foto"><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="m9 18 6-6-6-6" /> </svg></button><p id="lbCap"></p></div></dialog>

<template id="ctaTpl">
  <div class="wrap">
    <div class="cta-card hatch">
      <div class="t">
        <div class="label light"><i></i><i></i>Contato</div>
        <h2 data-t>Vamos construir sua próxima estratégia?</h2>
        <p>Toda boa comunicação começa com uma conversa.</p>
        <div class="btns"><a class="btn btn-gold" href="#contato" data-b>Agende uma conversa com a MB <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M5 12h14" /> <path d="m12 5 7 7-7 7" /> </svg></a><a class="btn btn-line" href="https://wa.me/<?= e($S['whatsapp']) ?>" target="_blank" rel="noopener"><svg class="ic ic-b" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg> WhatsApp</a></div>
      </div>
      <div class="ph"><img data-ci loading="lazy" src="img/g-conversa.jpg" alt="Conversa estratégica entre consultores e cliente" style="object-position:28% 40%"></div>
    </div>
  </div>
</template>

<template id="mqTpl"><span>Comunicação Política</span><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 6v12" /> <path d="M17.196 9 6.804 15" /> <path d="m6.804 9 10.392 6" /> </svg><span>Consultoria e Execução</span><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 6v12" /> <path d="M17.196 9 6.804 15" /> <path d="m6.804 9 10.392 6" /> </svg><span>Treinamentos e Workshops</span><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 6v12" /> <path d="M17.196 9 6.804 15" /> <path d="m6.804 9 10.392 6" /> </svg><span>Planejamento de Mandato</span><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 6v12" /> <path d="M17.196 9 6.804 15" /> <path d="m6.804 9 10.392 6" /> </svg><span>Campanhas Eleitorais</span><svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M12 6v12" /> <path d="M17.196 9 6.804 15" /> <path d="m6.804 9 10.392 6" /> </svg></template>

<script>
(function(){
  var ARROW = document.querySelector('#ctaTpl').content.querySelector('[data-b] svg').outerHTML;
  // CTA
  document.querySelectorAll('[data-cta]').forEach(function(el){
    el.appendChild(document.getElementById('ctaTpl').content.cloneNode(true));
    if(el.dataset.title) el.querySelector('[data-t]').textContent = el.dataset.title;
    if(el.dataset.btn) el.querySelector('[data-b]').innerHTML = el.dataset.btn + ' ' + ARROW;
    if(el.dataset.img){ var ci = el.querySelector('[data-ci]'); ci.src = el.dataset.img; ci.alt = el.dataset.alt || ''; ci.style.objectPosition = el.dataset.pos || '50% 50%'; }
  });
  // Faixas de serviços (conteúdo repetido para o loop contínuo)
  var MQ = document.getElementById('mqTpl').innerHTML;
  document.querySelectorAll('[data-marquee]').forEach(function(el){
    el.innerHTML = '<div class="track">' + MQ + MQ + MQ + MQ + '</div>';
  });

  // Clientes
  var clients = [
    {n:'Jayme Campos', c:'Senador', g:'senado', img:'c-jayme-campos'},
    {n:'Blairo Maggi', c:'Senador', g:'senado', img:'c-blairo-maggi'},
    {n:'Cidinho Santos', c:'Senador', g:'senado', img:'c-cidinho-santos'},
    {n:'Juarez Costa', c:'Deputado Federal', g:'deputados', img:'c-juarez-costa'},
    {n:'Coronel Fernanda', c:'Deputada Federal', g:'deputados', img:'c-coronel-fernanda'},
    {n:'Rafael Brito', c:'Deputado Federal', g:'deputados', img:'c-rafael-brito'},
    {n:'Valmir Moretto', c:'Deputado Estadual', g:'deputados', img:'c-valmir-moretto'},
    {n:'João Bang', c:'Prefeito', g:'prefeitos', img:'c-joao-bang'},
    {n:'Passarinho', c:'Prefeito', g:'prefeitos', img:'c-passarinho'},
    {n:'Edemilson Marino', c:'Prefeito', g:'prefeitos', img:'c-edemilson-marino'},
    {n:'Max Russi', c:'Imagem a inserir', g:'outros'},
    {n:'Janaina Riva', c:'Imagem a inserir', g:'outros'},
    {n:'Wilson Santos', c:'Imagem a inserir', g:'outros'},
    {n:'Podemos / MT', c:'Imagem a inserir', g:'partidos'}
  ];
  function card(x){
    if(x.img) return '<figure class="client-card"><div class="ph"><img loading="lazy" src="img/'+x.img+'.jpg" alt="'+x.n+', '+x.c+'"></div></figure>';
    return '<figure class="client-card ph-only"><div class="ph"><b>'+x.n+'</b><small>'+x.c+'</small></div></figure>';
  }
  document.addEventListener('pointermove', function(e){
    var b=e.target.closest&&e.target.closest('.bt'); if(!b) return;
    var r=b.getBoundingClientRect(); b.style.setProperty('--mx',(e.clientX-r.left)+'px'); b.style.setProperty('--my',(e.clientY-r.top)+'px');
  });
  (function(){
    var rail=document.getElementById('homeStrip'), track=rail.querySelector('.rail-track');
    var one=clients.filter(function(x){return x.img}).map(card).join('');
    track.innerHTML = one + one.split('<figure class="client-card"').join('<figure aria-hidden="true" class="client-card"');
    var N=track.children.length/2, x=0, setW=0, step=0, cur=0, nudge=0, last=0, raf=0;
    var hover=false, drag=false, stopped=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var SPEED=38; // px por segundo
    var pauseBtn=document.getElementById('railPause');
    function sync(){pauseBtn.setAttribute('aria-pressed', stopped);pauseBtn.setAttribute('aria-label', stopped?'Retomar movimento':'Pausar movimento')}
    function measure(){ if(!track.offsetWidth) return false; setW=track.children[N].offsetLeft-track.children[0].offsetLeft; step=track.children[1].offsetLeft-track.children[0].offsetLeft; return setW>0 }
    function wrap(){ if(!setW) return; while(x<=-setW) x+=setW; while(x>0) x-=setW }
    function paint(){ track.style.transform='translate3d('+x+'px,0,0)' }
    function tick(t){
      raf=requestAnimationFrame(tick);
      var dt=Math.min(60,t-(last||t)); last=t;
      var want=(stopped||hover||drag)?0:SPEED;
      cur+=(want-cur)*Math.min(1,dt*0.006);
      if(!drag){ x-=cur*dt/1000; var d=nudge*Math.min(1,dt*0.008); nudge-=d; x-=d; if(Math.abs(nudge)<.3) nudge=0 }
      wrap(); paint();
    }
    function start(){ if(raf) return; if(!measure()) return; last=0; raf=requestAnimationFrame(tick) }
    function stop(){ cancelAnimationFrame(raf); raf=0 }
    new IntersectionObserver(function(en){ en[0].isIntersecting?start():stop() },{threshold:0}).observe(rail);
    window.addEventListener('resize', function(){ if(measure()){wrap();paint()} });
    window.addEventListener('load', function(){ if(raf && measure()){wrap();paint()} });
    rail.addEventListener('pointerenter', function(e){ if(e.pointerType==='mouse') hover=true });
    rail.addEventListener('pointerleave', function(){ hover=false });
    var sx=0, sxPos=0;
    track.addEventListener('pointerdown', function(e){ if(e.pointerType==='mouse'&&e.button!==0) return; drag=true; sx=e.clientX; sxPos=x; nudge=0; track.classList.add('drag'); try{track.setPointerCapture(e.pointerId)}catch(_){} });
    track.addEventListener('pointermove', function(e){ if(!drag) return; x=sxPos+(e.clientX-sx); wrap(); paint() });
    function up(){ drag=false; track.classList.remove('drag') }
    track.addEventListener('pointerup', up); track.addEventListener('pointercancel', up);
    track.addEventListener('dragstart', function(e){e.preventDefault()});
    function go(dir){ if(!step) measure(); nudge += dir*step*2; if(!raf) { x-=nudge; nudge=0; wrap(); paint() } }
    document.getElementById('railPrev').addEventListener('click', function(){go(-1)});
    document.getElementById('railNext').addEventListener('click', function(){go(1)});
    pauseBtn.addEventListener('click', function(){ stopped=!stopped; sync() });
    sync();
  })();
  var groups = [['todos','Todos'],['senado','Senadores'],['deputados','Deputados'],['prefeitos','Prefeitos'],['partidos','Partidos'],['outros','Outros projetos']];
  var fEl = document.getElementById('filters'), cEl = document.getElementById('clients');
  fEl.innerHTML = groups.map(function(g,i){return '<button type="button" class="chip" data-g="'+g[0]+'" aria-pressed="'+(i===0)+'">'+g[1]+'</button>'}).join('');
  function renderClients(g){ cEl.innerHTML = clients.filter(function(x){return g==='todos'||x.g===g}).map(card).join(''); }
  fEl.addEventListener('click', function(e){
    var b = e.target.closest('.chip'); if(!b) return;
    fEl.querySelectorAll('.chip').forEach(function(c){c.setAttribute('aria-pressed', c===b)});
    renderClients(b.dataset.g);
  });
  renderClients('todos');

  // Equipe
  var MORE = '<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"> <path d="M7 7h10v10" /> <path d="M7 17 17 7" /> </svg>';
  var team = [
    {n:'Felipe Fabre Figueiredo', r:'Consultor de Comunicação Governamental e Marketing Político', img:'eq-felipe',
     b:['Integra a equipe de consultoria da MB, contribuindo para o desenvolvimento de estratégias, organização de projetos e acompanhamento de demandas de comunicação política e governamental.','Sua atuação combina capacidade analítica, organização e compreensão das dinâmicas do ambiente político.']},
    {n:'Hortência Bragatto Vastos', r:'Consultora de Comunicação Governamental e Marketing Político', img:'eq-hortencia',
     b:['Atua no planejamento e acompanhamento de projetos de comunicação política e governamental, contribuindo para análises de cenário, organização estratégica e desenvolvimento de soluções de comunicação.','Sua atuação valoriza o relacionamento interpessoal, a escuta e a compreensão das particularidades de cada cliente.']},
    {n:'Joe Marcelo Siqueira Vidal', r:'Diretor de Arte', img:'eq-joe',
     b:['Responsável pela direção de arte dos projetos da MB, contribuindo para o desenvolvimento de soluções visuais alinhadas às estratégias de comunicação.','Seu trabalho conecta criatividade, identidade visual e posicionamento, transformando conceitos estratégicos em materiais de comunicação.']}
  ];
  var tEl = document.getElementById('team');
  tEl.innerHTML = team.map(function(m,i){
    return '<button class="member" type="button" data-i="'+i+'"><div class="ph"><img loading="lazy" src="img/'+m.img+'.jpg" alt="'+m.n+'"></div><div class="in"><h3>'+m.n+'</h3><p class="role">'+m.r+'</p><span class="more">Ver biografia '+MORE+'</span></div></button>';
  }).join('');
  var bio = document.getElementById('bioDlg');
  tEl.addEventListener('click', function(e){
    var b = e.target.closest('.member'); if(!b) return;
    var m = team[+b.dataset.i];
    document.getElementById('bioImg').src = 'img/'+m.img+'.jpg';
    document.getElementById('bioImg').alt = m.n;
    document.getElementById('bioName').textContent = m.n;
    document.getElementById('bioRole').textContent = m.r;
    document.getElementById('bioText').innerHTML = m.b.map(function(p){return '<p>'+p+'</p>'}).join('');
    bio.showModal();
  });

  // Galerias (Cuiabá e Brasília) com lightbox compartilhado
  var galCba = [
    ['cba-fachada','Fachada','g-a'],['cba-recepcao','Recepção','g-b'],['cba-sala-direcao','Sala de direção','g-c'],
    ['cba-reuniao','Sala de reunião','g-c'],['cba-lounge','Espaço de convivência','g-c'],['cba-fachada-logo','Fachada','g-c'],
    ['cba-estante','Estante','g-c'],['cba-equipe-sala','Sala da equipe','g-c'],['cba-reuniao-2','Sala de reunião','g-c'],
    ['cba-lounge-2','Espaço de convivência','g-c'],['cba-certificados','Reconhecimentos','g-c']
  ];
  var galBsb = [
    ['bsb-sala','Sala de reunião','g-a'],['bsb-recepcao','Recepção','g-b'],['bsb-logo','Painel com a marca','g-c','43% 50%'],
    ['bsb-reuniao','Sala de reunião','g-c'],['bsb-lounge-2','Espaço de convivência','g-c'],['bsb-sala-2','Sala de reunião','g-c'],
    ['bsb-mapa-df','Mapa do Distrito Federal','g-c'],['bsb-mapa-mt','Mapa de Mato Grosso','g-c'],['bsb-reuniao-2','Sala de reunião','g-c'],
    ['bsb-detalhe','Detalhe da mesa','g-c'],['bsb-mesa','Detalhe da mesa','g-c']
  ];
  var gal = galCba, galCity = 'Cuiabá';
  function fillGal(id, arr, city){
    document.getElementById(id).innerHTML = arr.map(function(g,i){
      return '<button type="button" class="'+g[2]+'" data-i="'+i+'" aria-label="Ampliar foto: '+g[1]+'"><img loading="lazy" src="img/'+g[0]+'.jpg" alt="'+g[1]+' da MB '+city+'"'+(g[3]?' style="object-position:'+g[3]+'"':'')+'></button>';
    }).join('');
    document.getElementById(id).addEventListener('click', function(e){ var b=e.target.closest('button'); if(!b) return; gal=arr; galCity=city; show(+b.dataset.i); lb.showModal(); });
  }
  var lb = document.getElementById('lbDlg'), cur = 0;
  function show(i){ cur = (i+gal.length)%gal.length; document.getElementById('lbImg').src='img/'+gal[cur][0]+'.jpg'; document.getElementById('lbImg').alt=gal[cur][1]; document.getElementById('lbCap').textContent = gal[cur][1]+' · MB '+galCity+' · '+(cur+1)+' de '+gal.length; }
  fillGal('gal-cba', galCba, 'Cuiabá');
  fillGal('gal-bsb', galBsb, 'Brasília');
  document.getElementById('lbPrev').onclick = function(){show(cur-1)};
  document.getElementById('lbNext').onclick = function(){show(cur+1)};
  lb.addEventListener('keydown', function(e){ if(e.key==='ArrowLeft') show(cur-1); if(e.key==='ArrowRight') show(cur+1); });
  document.querySelectorAll('dialog').forEach(function(d){
    d.addEventListener('click', function(e){ if(e.target===d || e.target.closest('[data-close]')) d.close(); });
  });

  // Formulário: envia para api/lead.php e responde dentro do próprio formulário
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

  // Rotas
  var pages = Array.prototype.map.call(document.querySelectorAll('section.page'), function(s){return s.dataset.page});
  var nav = document.getElementById('nav'), menuBtn = document.getElementById('menuBtn');
  var pendingAnchor = null;
  function route(){
    var h = (location.hash||'').slice(1); if(pages.indexOf(h)<0) h='home';
    document.querySelectorAll('section.page').forEach(function(s){ s.classList.toggle('on', s.dataset.page===h); });
    nav.querySelectorAll('a').forEach(function(a){ if(a.getAttribute('href')==='#'+h) a.setAttribute('aria-current','page'); else a.removeAttribute('aria-current'); });
    nav.classList.remove('open'); menuBtn.setAttribute('aria-expanded','false');
    var T={'quem-somos':'Quem Somos','equipe':'Equipe','solucoes':'Soluções','experiencias':'Experiências','sedes':'Sedes','conteudos':'Conteúdos','contato':'Contato','privacidade':'Política de Privacidade'};
    document.title = h==='home' ? 'MB Consultoria e Assessoria Política' : T[h]+' · MB Consultoria e Assessoria Política';
    if(pendingAnchor){ var t=document.getElementById(pendingAnchor); pendingAnchor=null; if(t){ setTimeout(function(){t.scrollIntoView({behavior:'smooth'})},60); return; } }
    window.scrollTo(0,0);
  }
  document.addEventListener('click', function(e){
    var a = e.target.closest('a[data-anchor]'); if(!a) return;
    pendingAnchor = a.dataset.anchor;
    if(location.hash === a.getAttribute('href')){ e.preventDefault(); route(); }
  });
  window.addEventListener('hashchange', route);
  menuBtn.addEventListener('click', function(){ var o = nav.classList.toggle('open'); menuBtn.setAttribute('aria-expanded', o); });
  route();
})();
</script>
<script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/ScrollTrigger.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/lenis@1.3.4/dist/lenis.min.js"></script>
<script defer src="motion.js"></script>
<script defer src="js/cms.js"></script>
</body>
</html>
