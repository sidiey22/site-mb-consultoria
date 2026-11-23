import sys,os
from PIL import Image, ImageDraw
out, cols, tw = sys.argv[1], int(sys.argv[2]), int(sys.argv[3])
files = sys.argv[4:]
ths=[]
for f in files:
    im=Image.open(f).convert('RGB'); im.thumbnail((tw,tw)); ths.append((os.path.basename(f),im))
th=max(i.size[1] for _,i in ths)+18
rows=(len(ths)+cols-1)//cols
S=Image.new('RGB',(cols*(tw+6),rows*th),'white'); d=ImageDraw.Draw(S)
for k,(n,i) in enumerate(ths):
    x=(k%cols)*(tw+6); y=(k//cols)*th
    S.paste(i,(x,y)); d.text((x+2,y+th-16),n[:30],fill='black')
S.save(out)
