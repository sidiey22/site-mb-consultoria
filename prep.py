import sys, os
from PIL import Image, ImageOps
src='/home/claude/mb/zip'; out='/home/claude/mb/site/img'
FS=src+'/Fotos para o site/Fotos para o site/'; FM=src+'/FOTOS MB/'
def save(im,name,w,q=78):
    im=ImageOps.exif_transpose(im).convert('RGB')
    if im.width>w: im=im.resize((w,round(im.height*w/im.width)),Image.LANCZOS)
    im.save(f'{out}/{name}.jpg',quality=q,optimize=True,progressive=True)
def frame(n): return Image.open(FM+n+'.PNG').crop((0,96,2880,1702))
# office frames (Cuiabá)
for n,name in [('IMG_0834','cba-fachada'),('IMG_0832','cba-fachada-logo'),('IMG_0840','cba-recepcao'),('IMG_0853','cba-recepcao-2'),('IMG_0830','cba-sala-direcao'),('IMG_0823','cba-sala-direcao-2'),('IMG_0836','cba-reuniao'),('IMG_0849','cba-reuniao-2'),('IMG_0844','cba-lounge'),('IMG_0846','cba-lounge-2'),('IMG_0847','cba-estante'),('IMG_0848','cba-mapa'),('IMG_0813','cba-equipe-sala'),('IMG_0822','cba-certificados'),('IMG_0828','cba-arte')]:
    save(frame(n),name,1600)
for n,name in [('BIANCHI-171','f-porta'),('BIANCHI-205','f-duas'),('BIANCHI-288','f-mesa'),('BIANCHI-295','f-detalhe'),('BIANCHI-367','f-parede-h'),('BIANCHI-383','f-cafe'),('BIANCHI-404','f-estante'),('BIANCHI-412','f-direcao'),('BIANCHI-454','f-oculos'),('BIANCHI-89','f-fachada-h'),('NAD00737','f-reuniao'),('NAD00758','f-recepcao'),('NAD00793','f-parede-h2')]:
    save(Image.open(FS+n+'.jpg'),name,1400)
for n,name in [('Mariana Bonjour','eq-mariana'),('Felipe','eq-felipe'),('Hort#U00eancia Bragatto Vastos ','eq-hortencia'),('Joe Marcelo Siqueira Vidal ','eq-joe')]:
    save(Image.open(FS+n+'.jpg'),name,1000)
for n,name in [('Blairo Maggi','c-blairo-maggi'),('Cidinho Santos','c-cidinho-santos'),('Coronel Fernanda','c-coronel-fernanda'),('Edemilson Marino','c-edemilson-marino'),('Jayme Campos','c-jayme-campos'),('Juarez Costa','c-juarez-costa'),('Passarinho copiar','c-joao-bang'),('Passarinho','c-passarinho'),('Rafael Brito','c-rafael-brito'),('Valmir Moretto','c-valmir-moretto')]:
    save(Image.open(src+'/Mandatos/'+n+'.png'),name,425,85)
for n,name in [('Vertical_cor_ofcial','logo-linha'),('Horizontal_cor_ofcial','logo-bloco'),('MB-1','logo-simbolo')]:
    im=Image.open(src+'/Logomarcas/'+n+'.png').convert('RGBA'); im=im.crop(im.getbbox())
    if im.width>900: im=im.resize((900,round(im.height*900/im.width)),Image.LANCZOS)
    im.save(f'{out}/{name}.png',optimize=True)
