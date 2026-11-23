from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    b=p.chromium.launch(executable_path='/usr/bin/google-chrome')
    pg=b.new_page(viewport={'width':390,'height':844})
    for h in ['home','solucoes','contato','sedes','equipe']:
        pg.goto('file:///home/claude/mb/site/index.html#'+h); pg.wait_for_timeout(500)
        r=pg.evaluate("""()=>{const W=document.documentElement.clientWidth;const out=[];document.querySelectorAll('section.page.on *, footer *').forEach(e=>{const r=e.getBoundingClientRect();if(r.right>W+1&&r.width>0&&!e.closest('.strip')&&!e.closest('.subnav')){out.push(e.tagName+'.'+e.className+' '+Math.round(r.right))}});return [document.documentElement.scrollWidth,out.slice(0,6)]}""")
        print(h,r)
    b.close()
