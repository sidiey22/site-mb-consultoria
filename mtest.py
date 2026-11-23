import sys, json
from playwright.sync_api import sync_playwright
L='/home/claude/mb/libs/'
LIBS={'gsap.min.js':L+'x-gsap-3.12.7.tgz/package/dist/gsap.min.js',
      'ScrollTrigger.min.js':L+'x-gsap-3.12.7.tgz/package/dist/ScrollTrigger.min.js',
      'lenis.min.js':L+'x-lenis-1.3.4.tgz/package/dist/lenis.min.js'}
URL='file:///home/claude/mb/site/index.html'
OUT='/home/claude/mb/mt/'
def wire(ctx, block_cdn=False):
    def h(route):
        u=route.request.url
        if 'fonts.g' in u: return route.abort()
        for k,v in LIBS.items():
            if u.endswith(k):
                if block_cdn: return route.abort()
                return route.fulfill(path=v, content_type='application/javascript')
        return route.abort()
    ctx.route('https://**/*', h)
def leftovers(pg):
    # elementos dentro da viewport ainda invisíveis/borrados depois de assentar
    return pg.evaluate("""()=>{const H=innerHeight,out=[];
      document.querySelectorAll('section.page.on *, footer *').forEach(e=>{
        const r=e.getBoundingClientRect(); if(r.width<2||r.height<2||r.bottom<0||r.top>H*0.8||e.matches('.hero-mark,.bg,.mk,.mb-pre *,.marquee *')) return;
        const cs=getComputedStyle(e); const op=parseFloat(cs.opacity);
        const bl=/blur\\(([\\d.]+)px/.exec(cs.filter); 
        if(op<0.98||(bl&&parseFloat(bl[1])>0.3)) out.push((e.tagName+'.'+(e.className||'')).slice(0,40)+' op='+op.toFixed(2)+' '+cs.filter);
      }); return out.slice(0,8)}""")
with sync_playwright() as p:
    b=p.chromium.launch(executable_path='/usr/bin/google-chrome')
    errs=[]
    mode=sys.argv[1]
    if mode=='desktop':
        ctx=b.new_context(viewport={'width':1440,'height':900}); wire(ctx)
        pg=ctx.new_page(); pg.on('pageerror',lambda e:errs.append(str(e))); pg.on('console',lambda m: errs.append(m.text) if m.type=='error' else None)
        pg.goto(URL); 
        for t in [300,1300,2600,4200]:
            pg.wait_for_timeout(t - (0 if t==300 else [300,1300,2600,4200][[300,1300,2600,4200].index(t)-1]))
            pg.screenshot(path=OUT+f'pre-{t}.png')
        print('classes',pg.evaluate("document.documentElement.className"), 'pre display', pg.evaluate("getComputedStyle(document.getElementById('mbPre')).display"))
        print('lenis?',pg.evaluate("document.documentElement.classList.contains('lenis')"))
        # rolar a home com a roda do mouse
        for i in range(6):
            pg.mouse.wheel(0,700); pg.wait_for_timeout(450)
            if i in (1,3): pg.screenshot(path=OUT+f'scroll-{i}.png')
        # testar cada página: rola até o fim em passos e confere sobras
        report={}
        for h in ['home','quem-somos','equipe','solucoes','experiencias','sedes','conteudos','contato','privacidade']:
            pg.evaluate(f"location.hash='#{h}'"); pg.wait_for_timeout(1900)
            bad=leftovers(pg)
            hgt=pg.evaluate("document.documentElement.scrollHeight"); y=0; worst=[]
            while y < hgt:
                pg.mouse.wheel(0,600); pg.wait_for_timeout(260); y+=600
            pg.wait_for_timeout(1800)
            worst=leftovers(pg)
            report[h]={'topo':bad,'fim':worst,'scrollY':pg.evaluate('scrollY'),'max':hgt-900}
        print(json.dumps(report,ensure_ascii=False,indent=1))
        # âncora de serviço a partir da home
        pg.evaluate("location.hash='#home'"); pg.wait_for_timeout(1500)
        pg.click('a.svc[data-anchor="s-campanhas"]'); pg.wait_for_timeout(2600)
        print('anchor campanhas top:',pg.evaluate("Math.round(document.getElementById('s-campanhas').getBoundingClientRect().top)"))
        pg.screenshot(path=OUT+'anchor.png')
        # filtro de clientes
        pg.evaluate("location.hash='#experiencias'"); pg.wait_for_timeout(1600)
        pg.click('#filters .chip[data-g="prefeitos"]'); pg.wait_for_timeout(1800)
        print('clientes após filtro:',leftovers(pg))
        # segunda visita na mesma sessão: sem preloader
        pg.goto(URL+'#equipe'); pg.wait_for_timeout(600)
        print('2a visita, preloader:',pg.evaluate("getComputedStyle(document.getElementById('mbPre')).display"), pg.evaluate("document.documentElement.className"))
    if mode=='mobile':
        ctx=b.new_context(viewport={'width':390,'height':844},is_mobile=True,has_touch=True); wire(ctx)
        pg=ctx.new_page(); pg.on('pageerror',lambda e:errs.append(str(e)))
        pg.goto(URL+'#solucoes'); pg.wait_for_timeout(4300); pg.screenshot(path=OUT+'m-top.png')
        print('px class count (deve ser 0):',pg.evaluate("document.querySelectorAll('.mb-px').length"))
        maxblur=pg.evaluate("""()=>Math.max(0,...[...document.querySelectorAll('section.page.on *')].map(e=>{const m=/blur\\(([\\d.]+)px/.exec(e.style.filter||'');return m?+m[1]:0}))""")
        print('blur máx inline no mobile:',maxblur)
        for i in range(12): pg.evaluate("window.scrollBy(0,500)"); pg.wait_for_timeout(250)
        pg.wait_for_timeout(2800); print('mobile sobras:',leftovers(pg)); pg.screenshot(path=OUT+'m-mid.png')
        print('scrollWidth',pg.evaluate("document.documentElement.scrollWidth"))
    if mode=='reduced':
        ctx=b.new_context(viewport={'width':1440,'height':900},reduced_motion='reduce'); wire(ctx)
        pg=ctx.new_page(); pg.on('pageerror',lambda e:errs.append(str(e)))
        pg.goto(URL); pg.wait_for_timeout(1500)
        print('reduced: preloader',pg.evaluate("getComputedStyle(document.getElementById('mbPre')).display"),'lenis',pg.evaluate("document.documentElement.classList.contains('lenis')"),'px',pg.evaluate("document.querySelectorAll('.mb-px').length"))
        print('blur inline no reduzido:',pg.evaluate("[...document.querySelectorAll('*')].filter(e=>/blur\\([1-9]/.test(e.style.filter||'')).length"))
        for i in range(8): pg.mouse.wheel(0,700); pg.wait_for_timeout(300)
        pg.wait_for_timeout(900); print('reduced sobras:',leftovers(pg))
    if mode=='nocdn':
        ctx=b.new_context(viewport={'width':1440,'height':900}); wire(ctx,block_cdn=True)
        pg=ctx.new_page()
        pg.goto(URL); pg.wait_for_timeout(1000)
        print('sem CDN 1s: main opacity',pg.evaluate("getComputedStyle(document.querySelector('main')).opacity"),'pre',pg.evaluate("getComputedStyle(document.getElementById('mbPre')).display"),pg.evaluate("document.documentElement.className"))
        pg.wait_for_timeout(3300)
        print('sem CDN 4.3s: pre visível?',pg.evaluate("getComputedStyle(document.getElementById('mbPre')).visibility"),'lock',pg.evaluate("document.documentElement.classList.contains('mb-lock')"))
        pg.screenshot(path=OUT+'nocdn.png')
    print('ERROS:',errs[:10])
    b.close()
