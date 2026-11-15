#!/usr/bin/env python3
"""
Convert the static RK Developers pages into WordPress page content.

For every page it writes:
  demo-content/pages/<slug>.html   page body as Gutenberg "Custom HTML" blocks
  assets/css/pages/<slug>.css      the page's inline <style> blocks
  assets/js/pages/<slug>.js        the page's inline <script> blocks

Tokens left in the page content (resolved by the theme):
  {{url:<slug>}}      link to a page   (resolved when the content is imported)
  {{asset:<path>}}    image or video   (imported into the Media Library)
  [rk_phone] ...      site settings    (live shortcodes, see inc/shortcodes.php)

Run from the repository root:  python3 wordpress/tools/extract_pages.py
The output is a starting point: dynamic sections (project grid, contact form,
galleries) are then swapped for shortcodes by hand, see the README.
"""
import re
import sys
import textwrap
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
THEME = ROOT / 'wordpress' / 'rk-developers-theme'

PAGES = {
    'home': 'index.html',
    'about': 'about.html',
    'projects': 'projects.html',
    'contact': 'contact.html',
    'metro-greens': 'metro-greens.html',
    'rk-avenue': 'rk-avenue.html',
    'rk-revenue': 'rk-revenue.html',
}

LINK_SLUGS = {
    'index': 'home', 'about': 'about', 'contact': 'contact', 'projects': 'projects',
    'metro-greens': 'metro-greens', 'rk-avenue': 'rk-avenue', 'rk-revenue': 'rk-revenue',
}

STYLE_RE = re.compile(r'<style[^>]*>(.*?)</style>', re.S | re.I)
SCRIPT_RE = re.compile(r'<script(?![^>]*ld\+json)[^>]*>(.*?)</script>', re.S | re.I)

ADDRESS_VARIANTS = [
    'RK Developers, 3rd Floor, 1st Cross, 15th Main, E Block, Beside Nandana Palace, Sahakarnagar, Bengaluru &#8211; 560092',
    'RK Developers, 3rd Floor, 1st Cross, 15th Main, E Block, Beside Nandana Palace, Sahakarnagar, Bengaluru - 560092',
    'RK Developers, 3rd Floor, 15th Main, E Block, Beside Nandana Palace, Sahakarnagar, Bengaluru - 560092',
]
ADDRESS_LINES = ('RK Developers,<br />3rd Floor, 1st Cross,<br />15th Main, E Block,<br />'
                 'Beside Nandana Palace,<br />Sahakarnagar, Bengaluru - 560092')


def tokenise(html):
    # Page links -> {{url:slug}}
    def link(m):
        slug = LINK_SLUGS[m.group(2)]
        return '%s"{{url:%s}}%s"' % (m.group(1), slug, m.group(3) or '')
    html = re.sub(r'(href=)"(index|about|contact|projects|metro-greens|rk-avenue|rk-revenue)\.html([?#][^"]*)?"',
                  link, html)
    html = re.sub(r"window\.location\.href='(about|contact)\.html'",
                  lambda m: "window.location.href='{{url:%s}}'" % m.group(1), html)

    # Logo -> live setting (Customizer logo)
    html = html.replace('assets/rk-logo.png', '[rk_logo_url]')
    # Every other asset -> Media Library import
    html = re.sub(r"""(["'(])assets/([^"']+?\.(?:png|jpe?g|webp|gif|svg|mp4))(?=["')])""",
                  lambda m: '%s{{asset:%s}}' % (m.group(1), m.group(2)), html)

    # Contact details -> live settings
    html = html.replace('tel:+919663108999', 'tel:[rk_phone_link]')
    html = html.replace('+91 9663108999', '[rk_phone]')
    html = re.sub(r'wa\.me/919663108999', 'wa.me/[rk_whatsapp]', html)
    html = html.replace('mailto:rkdeveloper12345@gmail.com', 'mailto:[rk_email]')
    html = html.replace('rkdeveloper12345@gmail.com', '[rk_email]')
    for a in ADDRESS_VARIANTS:
        html = html.replace(a, '[rk_address]')
    html = html.replace(ADDRESS_LINES, '[rk_address_lines]')
    html = html.replace('Monday – Sunday<br />9:30 AM – 7:00 PM IST', '[rk_hours_lines]')
    html = html.replace('Trusted Since 2016', 'Trusted Since [rk_since]')
    return html


def split_chunks(body):
    """Split the page body into top-level chunks (one Custom HTML block each)."""
    lines = body.split('\n')
    chunks, cur = [], []
    prev_was_comment = False
    for line in lines:
        top_comment = re.match(r'^  <!--', line) is not None
        top_elem = re.match(r'^  <(section|div)\b', line) is not None
        if (top_comment or (top_elem and not prev_was_comment)) and ''.join(cur).strip():
            chunks.append('\n'.join(cur))
            cur = []
        cur.append(line)
        if line.strip():
            prev_was_comment = top_comment
    if ''.join(cur).strip():
        chunks.append('\n'.join(cur))
    return [c.strip('\n') for c in chunks if c.strip()]


def dedent(text, n=2):
    return '\n'.join(l[n:] if l.startswith(' ' * n) else l for l in text.split('\n'))


def replace_element(html, start_marker, replacement, drop_comment=None):
    """Replace the element that starts with start_marker (incl. nested children)."""
    i = html.index(start_marker)
    tag = re.match(r'<(\w+)', start_marker).group(1)
    depth, pos = 0, i
    tag_re = re.compile(r'<(/?)%s\b[^>]*>' % tag)
    for m in tag_re.finditer(html, i):
        depth += -1 if m.group(1) else 1
        if depth == 0:
            pos = m.end()
            break
    if drop_comment:
        c = html.rfind(drop_comment, 0, i)
        if c != -1 and html[c + len(drop_comment):i].strip() == '':
            i = c
    return html[:i] + replacement + html[pos:]


GRID = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px;margin-top:52px;">'

# Sections that become live, dashboard-driven shortcodes
DYNAMIC = {
    'home': [('<div class="projects-duo">', '[rk_ongoing_projects]', None),
             (GRID, '[rk_projects_grid]', None)],
    'projects': [(GRID, '[rk_projects_grid]', None)],
    'contact': [('<form class="contact-form" id="contactForm"', '[rk_contact_form]', None),
                ('<div class="form-success" id="formSuccess"', '', None)],
    'metro-greens': [('<div id="mgGallery"', '[rk_project_slider project="metro-greens"]',
                      '<!-- Metro Greens Gallery Slideshow -->')],
    'rk-avenue': [('<div id="avGallery"', '[rk_project_slider project="rk-avenue"]',
                   '<!-- RK Avenue Gallery Slideshow -->')],
}
# Inline scripts that the theme replaces with its own code (assets/js/slider.js)
DROP_SCRIPTS = ('var mgImgs', 'var avImgs')


# Page scripts that sat after the footer in the static pages (the shared scroll-progress,
# cursor-glow and popup scripts live in assets/js/site.js instead)
EXTRA_JS = {
    'metro-greens': """// Parallax hero
window.addEventListener('scroll', () => {
  const bg = document.getElementById('mgHeroBg');
  if (bg) bg.style.transform = `scale(1.05) translateY(${window.scrollY * 0.3}px)`;
});
// Plot filter
document.querySelectorAll('.filter-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;
    document.querySelectorAll('.plot-card').forEach(card => {
      card.style.display = (filter === 'all' || card.dataset.size === filter) ? '' : 'none';
    });
  });
});
""",
    'rk-avenue': """/* ── Hero slideshow (3.5s per slide, 1.2s crossfade) ── */
(function(){
  var slides = document.querySelectorAll('.av-hero-slide');
  if(!slides.length) return;
  var idx = 0;
  setInterval(function(){
    slides[idx].classList.remove('active');
    idx = (idx + 1) % slides.length;
    slides[idx].classList.add('active');
  }, 3500);
})();
""",
}


def fix_page_js(slug, js):
    if slug == 'metro-greens':
        # Crop showcase images ship with the theme (assets/img/metro-greens-crops/)
        js = js.replace("'assets/metro greens/Copy of Copy of Copy of Green and White Modern Healthy Breakfast Idea Nutrition Tips List Instagram Post.png'",
                        "RK_THEME.img + 'metro-greens-crops/crop-collage.png'")
        js = re.sub(r"'assets/metro greens/(\d+)\.png'", r"RK_THEME.img + 'metro-greens-crops/\1.png'", js)
    return js


def main():
    for slug, fname in PAGES.items():
        src = (ROOT / fname).read_text(encoding='utf-8')
        head, body = src.split('<body', 1)
        css = [s.strip('\n') for s in STYLE_RE.findall(head)]

        start = body.index('</nav>') + len('</nav>')
        end = body.index('<footer')
        content = body[start:end]
        # drop a trailing "<!-- FOOTER -->" style comment
        content = re.sub(r'\n\s*<!--[^>]*FOOTER[^>]*-->\s*$', '\n', content)

        css += [s.strip('\n') for s in STYLE_RE.findall(content)]
        js = [s.strip('\n') for s in SCRIPT_RE.findall(content)]
        content = STYLE_RE.sub('', content)
        content = SCRIPT_RE.sub('', content)
        content = re.sub(r'\n[ \t]*\n(?:[ \t]*\n)+', '\n\n', content)

        if slug == 'metro-greens':
            joined = '\n'.join(css)
            joined = joined.replace("background-image: url('assets/metro green hero section.png');\n", '')
            css = [joined]
            content = content.replace('<div class="mg-hero-bg" id="mgHeroBg"></div>',
                                      '<div class="mg-hero-bg" id="mgHeroBg" style="background-image:url(\'assets/metro green hero section.png\')"></div>')

        for marker, repl, comment in DYNAMIC.get(slug, []):
            content = replace_element(content, marker, repl, comment)
        js = [fix_page_js(slug, j) for j in js if not any(d in j for d in DROP_SCRIPTS)]
        if slug == 'contact':
            # The form is now handled by WordPress itself, not FormSubmit
            content = content.replace('such as Google Analytics and FormSubmit to process', 'such as Google Analytics to process')
        if slug in EXTRA_JS:
            js.append(EXTRA_JS[slug])
        content = tokenise(content)
        blocks = []
        for chunk in split_chunks(content):
            blocks.append('<!-- wp:html -->\n%s\n<!-- /wp:html -->' % dedent(chunk))
        (THEME / 'demo-content' / 'pages' / ('%s.html' % slug)).write_text('\n\n'.join(blocks) + '\n', encoding='utf-8')

        header = '/* RK Developers — %s page styles (moved from the static %s) */\n' % (slug, fname)
        (THEME / 'assets' / 'css' / 'pages' / ('%s.css' % slug)).write_text(
            header + '\n\n'.join(dedent(c, 4) for c in css if c.strip()) + '\n', encoding='utf-8')
        if js:
            header = '/* RK Developers — %s page scripts (moved from the static %s) */\n' % (slug, fname)
            (THEME / 'assets' / 'js' / 'pages' / ('%s.js' % slug)).write_text(
                header + '\n\n'.join(textwrap.dedent(j) for j in js) + '\n', encoding='utf-8')
        print('%-13s %2d blocks  %3d css lines  %d scripts' % (
            slug, len(blocks), sum(c.count('\n') for c in css), len(js)))


if __name__ == '__main__':
    sys.exit(main())
