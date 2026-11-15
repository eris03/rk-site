#!/usr/bin/env python3
"""
Build the import data for the theme's one-click setup:

  demo-content/content.json   pages (title, template, SEO), 14 projects, menus
  demo-content/media/...      every image/video the pages and projects use

Run from the repository root:  python3 wordpress/tools/build_demo_data.py
"""
import html
import json
import re
import shutil
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
DEMO = ROOT / 'wordpress' / 'rk-developers-theme' / 'demo-content'

PAGES = [
    # slug, title, template, source file
    ('home', 'Home', 'home', 'index.html'),
    ('about', 'About Us', 'about', 'about.html'),
    ('projects', 'Projects', 'projects', 'projects.html'),
    ('contact', 'Contact', 'contact', 'contact.html'),
    ('metro-greens', 'Metro Greens', 'metro-greens', 'metro-greens.html'),
    ('rk-avenue', 'RK Avenue', 'rk-avenue', 'rk-avenue.html'),
    ('rk-revenue', 'RK Revenue', 'rk-revenue', 'rk-revenue.html'),
]


def seo(fname):
    src = (ROOT / fname).read_text(encoding='utf-8')
    def meta(name):
        m = re.search(r'<meta name="%s" content="([^"]*)"' % name, src)
        return html.unescape(m.group(1)) if m else ''
    title = html.unescape(re.search(r'<title>([^<]*)</title>', src).group(1))
    out = {'title': title, 'description': meta('description'), 'keywords': meta('keywords')}
    # The founding year is a site setting (see the open question in the README)
    for k in out:
        out[k] = re.sub(r'([Ss]ince) 2004', r'\1 [rk_since]', out[k])
    return out


def g(folder, *files):
    return ['%s/%s' % (folder, f) if folder else f for f in files]


# Order = position in the "Projects We've Delivered" grid (same as the static site)
PROJECTS = [
    dict(slug='metro-greens', title='Metro Greens', status='ongoing', icon='fas fa-tree',
         size='Phase 1 of 200 Acres', location='Near Chikkaballapura', price='₹999/sq.ft',
         page='metro-greens', featured=True, accent='green',
         card_badge='Phase 1 Open', card_type='Farm Plots', card_type_icon='fas fa-tree',
         card_location='Chikkaballapur, Beechaganahalli village',
         description="A monumental 200-acre ecological paradise offering premium farm plots. Phase 1 spanning 11.5 acres — own your piece of nature near Bangalore's airport corridor.",
         card_features='fas fa-expand-arrows-alt | 5.5–1 Acre Plots\nfas fa-rupee-sign | {price}\nfas fa-leaf | 30+ Amenities',
         image='metro green hero section.png', logo='metro green logo.png',
         gallery=g('metro-green-gallery', 'mg1.jpeg', 'mg2.jpeg', 'mg3.jpeg', 'mg4.jpeg', 'mg5.jpeg', 'mg6.jpeg', 'mg7.png')),
    dict(slug='rk-avenue', title='RK Avenue', status='ongoing', icon='fas fa-map-pin',
         size='36 Premium Residential Plots', location='Alkapura Village, Gauribidanur', price='₹1,899/sq.ft',
         page='rk-avenue', featured=True, accent='orange',
         card_badge='Ready to Register', card_type='Residential Plots', card_type_icon='fas fa-map-pin',
         card_location='Alkapura Village, Gauribidanur',
         description='An exclusive gated community of just 36 premium residential plots across 2.5 acres. Prime land, fully serviced, ready for registration and construction today.',
         card_features='fas fa-home | 36 Exclusive Plots\nfas fa-rupee-sign | {price}\nfas fa-shield-alt | Gated Community',
         image='rk avenue/avunue 104.png',
         gallery=g('rk avenue', 'avnue 1 (1).jpg', 'avunue 2.png', 'avunue 3 (2).png', 'avunue 4.png',
                   'avunue 5 (2).png', 'avunue 6.png', 'avunue 7 (1).png', '1a.png')),
    dict(slug='reality-residential-layout', title='Reality Residential Layout', icon='fas fa-map-marked-alt',
         size='94 Residential Plots · 4 Acres', location='Bagalur, North Bengaluru',
         gallery=g('sj layout', 's1.webp', 's2.webp', 's3.webp', 's5.jpeg')),
    dict(slug='srk-grand', title='SRK Grand', icon='fas fa-map-marked-alt',
         size='2 Acres', location='Behind REVA University · BBMP B-Katha Property',
         gallery=g('srk-grand', 'srk1.jpg', 'srk2.jpg', 'srk3.jpg')),
    dict(slug='annapooreshwari-enclave', title='Annapooreshwari Enclave', icon='fas fa-home',
         size='Residential Layout', location='Nelamangala–Doddaballapura Road',
         gallery=g('ana', 'ana1.jpg', 'ana2.jpg', 'ana3.jpg')),
    dict(slug='royal-orchid', title='Royal Orchid', icon='fas fa-tree',
         size='3.5 Acres Residential Layout',
         location='Near Padukone-Dravid Centre for Sports Excellence · Prestige Marigold & Soundarya',
         gallery=g('royalorchid', 'ro1.jpg', 'ro2.jpg', 'ro3.jpg')),
    dict(slug='soundarya-garden', title='Soundarya Garden', icon='fas fa-leaf',
         size='1 Acre Residential Layout', location='Kukkanahalli Village, Hesaraghatta',
         gallery=g('saun', 'sg1.jpg', 'sg2.jpg', 'sg3.jpg')),
    dict(slug='soundarya-enclave', title='Soundarya Enclave', icon='fas fa-leaf',
         size='Residential Layout · 1.5 Acres', location='Kukkanahalli Village, Hesaraghatta',
         gallery=g('saungar', 'se1.jpg', 'se2.jpg', 'se3.jpg')),
    dict(slug='basaveshwara-layout', title='Basaveshwara Layout', icon='fas fa-map-pin',
         size='Residential Layout · 11.7 Acres', location='Kukkanahalli Village, Hesaraghatta',
         gallery=g('basva', 'basva1.png', 'bl1.jpg', 'bl2.jpg', 'bl3.jpg')),
    dict(slug='skyline', title='Skyline', icon='fas fa-building',
         size='1.5 Acres Residential Layout', location='Near REVA University, Yelahanka Taluk, Bengaluru',
         gallery=g('skyline', 'sky1.jpg', 'sky2.jpg', 'sky3.jpg')),
    dict(slug='golden-gate', title='Golden Gate', icon='fas fa-star',
         size='6.5 Acres Residential Layout', location='IVC Road, Near Thindlu Circle',
         gallery=g('golden', 'gg1.jpg', 'gg2.jpg')),
    dict(slug='aj-layout', title='AJ Layout', icon='fas fa-store',
         size='3 Acres Commercial Layout', location='Near REVA University, Bagalur',
         gallery=g('', 'aj-layout.png')),
    dict(slug='garden-city', title='Garden City', icon='fas fa-city',
         size='25 Acres Township Project', location='Attached to STRR Highway, Near Devanahalli Town'),
    dict(slug='north-metro-city', title='North Metro City', icon='fas fa-road',
         size='30 Acres Township Project', location='Near Devanahalli DC Office'),
]

MENUS = {
    'primary': {'name': 'Main Menu', 'items': [
        {'title': 'Home', 'page': 'home'},
        {'title': 'Our Projects', 'url': '#', 'description': 'Choose a Project', 'children': [
            {'title': 'Metro Greens', 'page': 'metro-greens'},
            {'title': 'RK Avenue', 'page': 'rk-avenue'},
        ]},
        {'title': 'About Us', 'page': 'about'},
        {'title': 'Contact', 'page': 'contact'},
    ]},
    'footer': {'name': 'Footer Quick Links', 'items': [
        {'title': 'Home', 'page': 'home'},
        {'title': 'Our Projects', 'url': '#'},
        {'title': 'About Us', 'page': 'about'},
        {'title': 'Contact', 'page': 'contact'},
    ]},
}


def main():
    pages = []
    media = set()
    for slug, title, template, fname in PAGES:
        content = (DEMO / 'pages' / ('%s.html' % slug)).read_text(encoding='utf-8')
        media.update(re.findall(r'\{\{asset:([^}]+)\}\}', content))
        pages.append({'slug': slug, 'title': title, 'template': template, 'seo': seo(fname)})

    projects = []
    for order, p in enumerate(PROJECTS):
        p = dict(p)
        p.setdefault('status', 'delivered')
        p['order'] = order
        for key in ('image', 'logo'):
            if p.get(key):
                media.add(p[key])
        media.update(p.get('gallery', []))
        projects.append(p)

    media = sorted(media)
    out_media = DEMO / 'media'
    for rel in media:
        src = ROOT / 'assets' / rel
        dst = out_media / rel
        dst.parent.mkdir(parents=True, exist_ok=True)
        if not dst.exists() or dst.stat().st_size != src.stat().st_size:
            shutil.copy2(src, dst)

    data = {'version': 1, 'pages': pages, 'projects': projects, 'menus': MENUS, 'media': media}
    (DEMO / 'content.json').write_text(json.dumps(data, ensure_ascii=False, indent=1) + '\n', encoding='utf-8')
    size = sum((out_media / m).stat().st_size for m in media)
    print('%d pages, %d projects, %d media files (%.1f MB)' % (len(pages), len(projects), len(media), size / 1048576))


if __name__ == '__main__':
    main()
