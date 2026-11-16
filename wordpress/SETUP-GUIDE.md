# RK Developers website: WordPress setup guide

This guide is for the person who will run the RK Developers website. You don't need to know any code. Follow the steps in order. Setting up takes about 20 minutes.

**What you received**

| File | What it is |
|---|---|
| `rk-developers-theme.zip` | The website theme: the design, animations, pop-ups, floating WhatsApp/Call/Message/AI buttons, galleries. It also contains the import file and every photo and video. |
| `SETUP-GUIDE.md` / `SETUP-GUIDE.pdf` | This guide. |

The import file is inside the theme (`demo-content/content.json` plus the photos). You run it with one button, **Appearance → RK Setup** (step 2). It creates all 7 pages, the 14 projects with their photos, both menus and the home page.

---

## Before you start

WordPress is already installed on Hostinger. Check two things first:

1. You can log in at `https://your-domain/wp-admin` with an **Administrator** account.
2. **SSL (the padlock):** in hPanel, **Security → SSL** shows SSL as *Active* for the domain. In WordPress, **Settings → General** shows both addresses starting with `https://`.

> **Already have pages or another theme on this WordPress?** If pages with the addresses `/home/`, `/about/`, `/projects/`, `/contact/`, `/metro-greens/`, `/rk-avenue/` or `/rk-revenue/` already exist, the first import **replaces their text** with the RK pages. It also makes **Home** the front page. All other pages and posts are left alone. Take a backup first (hPanel → **Websites → Backups**).

## Step 1: Upload and activate the theme

1. In WordPress go to **Appearance → Themes → Add New Theme → Upload Theme**.
2. Click **Choose File** and select `rk-developers-theme.zip`, then **Install Now**.
3. When it finishes, click **Activate**.

> **"The uploaded file exceeds the upload_max_filesize directive"?** The theme is about 65 MB because it carries all the photos. Do one of these:
>
> - **Option A:** In hPanel go to **Advanced → PHP Configuration → PHP options**. Set `upload_max_filesize` and `post_max_size` to **256M** and save. Then try the upload again.
> - **Option B:** In hPanel open **Files → File Manager** and go to `public_html/wp-content/themes/`. Upload the zip there, right-click it and choose **Extract**, then delete the zip. Then go to **Appearance → Themes** in WordPress and activate **RK Developers**.

## Step 2: Import the content (one click)

1. Go to **Appearance → RK Setup**. A blue box at the top of the dashboard also links there.
2. Click **Import demo content**.
3. Keep the tab open. A progress bar shows the photos being imported. It takes 1 to 3 minutes.
4. When it says **"Done! Your site is ready."**, click **View the website**.

The import also:

- sets **Home** as the front page,
- turns on clean web addresses (`/about/`, `/contact/` and so on),
- creates the **Main Menu** (top bar) and the **Footer Quick Links** menu,
- sets the timezone to India.

If the import stops (slow hosting), click the button again. It continues where it stopped and never creates duplicates.

> **Running it again later is safe.** It keeps everything you have edited. Only tick **"Reset page texts and projects to the original website content"** if you really want to undo all your changes.

## Step 3: Check the home page and menus

Normally there is nothing to do here, because the import already set everything up. To check:

- **Settings → Reading**: *Your homepage displays* is set to **A static page**, and *Homepage* is set to **Home**.
- **Appearance → Menus**: **Main Menu** is in the *Main menu (top navigation bar)* location, and **Footer Quick Links** is in the *Footer "Quick Links"* location.

**How the top menu works:**

- A menu item that has sub-items becomes the **"Our Projects"** drop-down. Its sub-items show the project logo.
- The text above the sub-items ("Choose a Project") is the parent item's **Description**. If you can't see the Description field, open *Screen Options* at the top of the Menus page and tick *Description*.
- To add a project to the drop-down, drag its page in as a sub-item of "Our Projects".
- The orange **Book a Visit / Enquire Now** button is added automatically and always links to the Contact page.

The **Projects** and **RK Revenue** pages exist but are not in the menu, the same as on the old site. To show them, add them in **Appearance → Menus**.

## Step 4: Your contact details (one place for everything)

Go to **Appearance → Customize → RK Site Settings**:

| Section | What you can change |
|---|---|
| **Contact details** | Phone, WhatsApp number, e-mail address, the address that receives the contact form, office address (one line per row), Google Maps link, footer map picture, office hours |
| **Social media links** | Facebook, Instagram, YouTube, LinkedIn, X. An icon only appears when its link is filled in. |
| **Footer & company** | Founding year, footer tagline, copyright text |
| **Pop-up** | Turn the "Interested in a Property?" pop-up on or off, and how often it appears |

Click **Publish** to save. Each value appears everywhere on the site at once: the header and footer, the contact page, the call/WhatsApp buttons, the floating buttons, the AI assistant's answers, the pop-ups and the Google information.

- **Logo:** **Appearance → Customize → Site Identity → Logo**. If you don't set one, the RK logo shipped with the theme is used.
- **Browser tab icon:** **Appearance → Customize → Site Identity → Site Icon**.

## Managing projects

Click **Projects** in the left menu. The list shows each project's status, location, number of photos and order.

**To edit a project,** click its name and change the fields in **Project details**:

| Field | Where it shows |
|---|---|
| **Title** (top of the screen) | Project name everywhere |
| **Status**: Ongoing / Delivered | Ongoing projects get a green card with an "Ongoing" badge |
| **Size / plots** and **Location** | The line under the name on the project card, e.g. *94 Residential Plots · 4 Acres · Bagalur, North Bengaluru* |
| **Price** | Shown on the home page card through `{price}` in *Home card: features* |
| **Short description** | Home page "Ongoing Developments" card |
| **Card icon** | The small icon on the card. Pick a name from [fontawesome.com/icons](https://fontawesome.com/search?o=r&m=free), e.g. `fas fa-tree` |
| **Detail page** | The page the card links to (e.g. Metro Greens) |
| **Project logo** | Shown in the "Our Projects" drop-down menu |
| **Gallery images** | **Add / choose images** opens the Media Library. Drag thumbnails to change the order; × removes one. Delivered projects show these in the "View Photos" pop-up. Metro Greens and RK Avenue show them in the slideshow on their page. |
| **Show in "Ongoing Developments"** + *Home card* fields | The two big cards near the top of the home page |
| **Project image** (right-hand column) | The picture on the home page card |
| **Order** (in *Page Attributes*, right-hand column) | Position in the grid: lower numbers come first |

**To add a project,** click **Projects → Add Project**, fill in the fields, add photos and click **Publish**. It appears straight away in the "Projects We've Delivered" grid on the home page and on the Projects page.

**To hide a project,** set it back to **Draft**, or move it to the Trash.

> **Tip:** Make photos at most about 2000 px wide before uploading. Phone photos are often 5 to 10 MB, which slows the site down. Free tools like [squoosh.app](https://squoosh.app) shrink them without visible loss.

## Editing page text

1. Go to **Pages** and open a page, for example **About Us**.
2. Each section of the page (hero, story, services and so on) is a **Custom HTML** block. Click a block and edit the text between the tags. For example, in `<h4>Our Vision</h4>`, change only `Our Vision`.
3. Click **Preview** in the block toolbar to see it, then click **Update** at the top right.

Rules that keep the design intact:

- Change **only the words** between `>` and `<`. Don't delete the `<…>` tags or `class="…"` and `style="…"` attributes.
- **To change a picture,** go to **Media → Add New**, upload the picture, click it, use **Copy URL to clipboard**, and paste it over the old address inside `src="…"`.
- These codes fill in live content, so leave them in place:

| Code | Shows |
|---|---|
| `[rk_projects_grid]` | The "Projects We've Delivered" grid (from **Projects**) |
| `[rk_ongoing_projects]` | The two "Ongoing Developments" cards |
| `[rk_project_slider project="metro-greens"]` | A project's photo slideshow |
| `[rk_contact_form]` | The contact form |
| `[rk_phone]`, `[rk_email]`, `[rk_whatsapp]`, `[rk_address]`, `[rk_hours_lines]`, `[rk_since]` | Values from **RK Site Settings** |

You can type these codes into any page or text to show the same live value.

- **Made a mistake?** On the right of the editor, open **Revisions** and restore an earlier version.
- **The page template** (right-hand column → *Template*, e.g. "RK – Metro Greens") loads that page's special design and animations. Keep it as it is.
- **Search engines:** under the editor, the **Search engine (Google) title & description** box sets the Google title and description for each page. If you later install an SEO plugin such as Yoast or Rank Math, the theme steps aside automatically.

**Adding a new simple page** (for example a separate Privacy Policy): **Pages → Add New**, write normally and publish. It gets the site header and footer automatically.

## The contact form and e-mails

Three things on the site send messages:

- the **Contact page form**,
- the floating **Message** button,
- the **"Book a free site visit"** pop-up.

Every message is:

1. **e-mailed** to the address in **RK Site Settings → Contact details** (`rkdeveloper12345@gmail.com` unless you change it), and
2. **saved** under **Enquiries** in the left menu, so nothing is lost even if an e-mail goes astray.

**Make e-mail delivery reliable (strongly recommended).** Web servers are often treated as spam senders, so set up proper sending once:

1. In hPanel, create a mailbox on your domain, e.g. `info@therkdevelopers.com` (**Emails → Email Accounts**).
2. In WordPress, go to **Plugins → Add New**, search for **WP Mail SMTP**, then click **Install** and **Activate**.
3. In its setup wizard choose **Other SMTP** and enter Hostinger's mail settings:
   - Host `smtp.hostinger.com`
   - Encryption **SSL**, port **465**
   - Username: the full mailbox address
   - Password: the mailbox password
   - From e-mail: that mailbox
4. Use **WP Mail SMTP → Tools → Email Test** to send a test e-mail to `rkdeveloper12345@gmail.com`.
5. Send a test message from the website's Contact page. Check the Gmail inbox, including the spam folder, and the **Enquiries** list.

*Alternative:* if you prefer **Contact Form 7**, install it, create a form, and replace `[rk_contact_form]` on the Contact page with the Contact Form 7 shortcode. The theme's own form is already styled to match the site, so this isn't needed.

## Moving from the old site

- Point the domain at Hostinger: **hPanel → Domains → DNS**, or use Hostinger's nameservers.
- Old addresses keep working. Links such as `about.html` or `contact.html?project=rk-avenue` redirect automatically to the new pages, so Google results and shared links don't break.
- After the move, submit `https://your-domain/wp-sitemap.xml` in Google Search Console.

## Keeping the site healthy

- **Backups:** hPanel → **Websites → Backups**. Hostinger keeps weekly or daily backups depending on the plan. Take a manual backup before big changes.
- **Updates:** update WordPress and plugins from **Dashboard → Updates** about once a month.
- **Users:** give staff who only edit projects or text the **Editor** role (**Users → Add New**). Keep the Administrator account for yourself.
- **Don't edit the theme files** (Appearance → Theme File Editor). Everything is meant to be changed from the dashboard. If you ever need design changes, hand the theme folder to a developer.

## Troubleshooting

| Problem | Fix |
|---|---|
| Pages show "Page not found" | **Settings → Permalinks**, choose **Post name**, click **Save Changes**. |
| The home page shows blog posts | **Settings → Reading** → *A static page* → Homepage **Home**. |
| A page looks plain or unstyled | Open the page, then in the right-hand column under **Template**, choose its "RK – …" template. |
| Contact e-mails don't arrive | See *Make e-mail delivery reliable* above. Messages are always saved under **Enquiries**. |
| A photo is missing in a gallery | **Projects** → open the project → **Gallery images** → add it again. |
| A text edit broke a section's layout | In the page editor, open **Revisions** and restore the previous version. |
| I want the original texts back | **Appearance → RK Setup**, tick **Reset…**, then **Import demo content**. |

---

### For developers

- **Theme structure:**
  - `inc/` holds the PHP: settings, the Projects post type, shortcodes, contact handler, menus, SEO and the importer.
  - `page-templates/` has one template per original page.
  - `assets/` holds the original CSS/JS, the self-hosted Font Awesome, AOS, Swiper and fonts (no CDN), videos and logos.
  - `demo-content/` holds the import data.
- **Assets:** loaded with `wp_enqueue_style` / `wp_enqueue_script`. `rk_layouts()` in `inc/setup.php` decides which files each page template loads. Settings reach the scripts as `window.RK_THEME`.
- **Rebuilding the import data:** `wordpress/tools/extract_pages.py` and `wordpress/tools/build_demo_data.py` (in the source repository) rebuild the page content and `content.json` from the static HTML.
