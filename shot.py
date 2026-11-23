import sys
from playwright.sync_api import sync_playwright
url='file:///home/claude/mb/site/index.html'
with sync_playwright() as p:
    b=p.chromium.launch(executable_path='/usr/bin/google-chrome')
    errs=[]
    for name,w,h,pages in [('d',1440,900,sys.argv[1].split(',')),('m',390,844,sys.argv[2].split(',') if len(sys.argv)>2 else [])]:
        pg=b.new_page(viewport={'width':w,'height':h})
        pg.on('console',lambda m: errs.append(m.text) if m.type=='error' else None)
        pg.on('pageerror',lambda e: errs.append(str(e)))
        for pgname in pages:
            if not pgname: continue
            pg.goto(url+'#'+pgname); pg.wait_for_timeout(1200)
            pg.screenshot(path=f'/home/claude/mb/shots/{name}-{pgname}.png',full_page=True)
    print('errors:',errs[:10])
    b.close()
