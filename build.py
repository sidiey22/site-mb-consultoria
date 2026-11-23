import re,sys
L='/home/claude/mb/tools/node_modules/lucide-static/icons/'
S='/home/claude/mb/tools/node_modules/simple-icons/icons/'
src=open('/home/claude/mb/src/index.src.html',encoding='utf-8').read()
def lucide(m):
    s=open(L+m.group(1)+'.svg').read()
    s=re.sub(r'<!--.*?-->','',s,flags=re.S).strip()
    s=re.sub(r'<svg[^>]*>','<svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">',s,count=1)
    return re.sub(r'\s*\n\s*',' ',s)
def brand(m):
    s=open(S+m.group(1)+'.svg').read()
    d=re.search(r'<path d="([^"]+)"',s).group(1)
    return f'<svg class="ic ic-b" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="{d}"/></svg>'
out=re.sub(r'\{\{i:([a-z0-9-]+)\}\}',lucide,src)
out=re.sub(r'\{\{b:([a-z0-9-]+)\}\}',brand,out)
assert '{{' not in out, re.findall(r'\{\{[^}]*\}\}',out)[:5]
open('/home/claude/mb/site/index.html','w',encoding='utf-8').write(out)
print(len(out))
