# RK Developers — WordPress version

| Path | What it is |
|---|---|
| `rk-developers-theme.zip` | Install this in WordPress (Appearance → Themes → Upload Theme). Contains the import data and all photos/videos. |
| `SETUP-GUIDE.md`, `SETUP-GUIDE.pdf` | Step-by-step guide for the site owner (Hostinger install, theme upload, one-click import, editing). |
| `rk-developers-theme/` | Theme source (same as the zip). |
| `tools/` | Scripts that rebuild the import data from the static pages and build the zip. |
| `screenshots/` | Static site vs WordPress theme, desktop and mobile, plus gallery/menu/form checks. |

Rebuild after changing the static pages or the theme:

```bash
python3 wordpress/tools/extract_pages.py     # page content, page CSS/JS
python3 wordpress/tools/build_demo_data.py   # content.json + media
sh wordpress/tools/build_zip.sh              # rk-developers-theme.zip
```

Note: `extract_pages.py` regenerates `demo-content/pages/*.html`, `assets/css/pages/*.css` and `assets/js/pages/*.js` from the static HTML.
