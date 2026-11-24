# Connekt India: Website (Frontend + Backend, Hostinger-ready)

**IDEAS · CONTENT · GROWTH**

A complete, production-ready website for Connekt India, a digital marketing, creative and technology agency in Bhubaneswar, Odisha. It is built in plain PHP 8.2 + MySQL with hand-written CSS and vanilla JavaScript, so it runs on any Hostinger shared hosting plan with no build step.

> This README is written for non-developers. Follow it top to bottom and your site will be live.

---

## 1. Project overview

### What's included

| Area | Features |
|---|---|
| **Pages** | Home, About, Services (6 services with anchors), Our Team (tree layout), Work (filters + case studies), Blog (8 SEO articles, search, filters, pagination), Blog post, Contact (Project / Career form + map), Privacy Policy, Terms, custom 404 |
| **Global** | Logo preloader (full on first visit, quick fade after), animated sticky header, mobile menu, WhatsApp button, "Connekt" chatbot (assistant Dibya, 30-question knowledge base), footer with newsletter |
| **Animations** | Scroll reveals, word-by-word headings, count-up stats, 3D card tilt, icon line-draw, animated tiles behind the logo, team frames with rotating gradient borders, custom cursor (desktop), `prefers-reduced-motion` respected |
| **Backend** | MySQL storage for enquiries, careers (with resume upload), newsletter and chatbot leads; CSRF tokens, honeypot, rate limit (5 per IP per 10 min), optional reCAPTCHA v3; branded HTML emails to the admin + auto-replies to the visitor |
| **Admin** | `/admin`: secure login (5-try lockout), dashboard, tables with search/filters/status/delete, resume download, CSV export |
| **SEO** | Unique titles and descriptions, Open Graph/Twitter cards, canonical URLs, LocalBusiness + Article + FAQPage + Breadcrumb JSON-LD, dynamic `sitemap.xml`, `robots.txt`, clean URLs |

### Folder structure

```
public_html/
├── index.php  about.php  services.php  team.php  work.php
├── blog.php  blog-post.php  contact.php  careers.php (→ contact Career tab)
├── privacy-policy.php  terms.php  404.php
├── sitemap.php (served as /sitemap.xml)  sitemap.xml (static fallback)  robots.txt
├── .htaccess  .user.ini  database.sql  README.md
├── router.php            ← only for local testing with "php -S" (blocked online)
├── includes/             ← shared parts (edit once, changes everywhere)
│   ├── bootstrap.php     ← core helpers + list of the 6 services
│   ├── head-meta.php  header.php  footer.php  preloader.php  whatsapp.php  chatbot.php
│   ├── components.php    ← FAQ, CTA, process, team card, tiles…
│   ├── api.php  Mailer.php  mail-templates.php  icon-data.php
├── config/  config.php  config.sample.php   ← passwords live here (web-blocked)
├── api/     contact.php  career.php  newsletter.php  chatbot-log.php
├── admin/   login.php  setup.php  dashboard.php  enquiries.php  careers.php
│            newsletter.php  leads.php  export.php  download.php  logout.php
├── data/    team.json  clients.json  testimonials.json  stats.json  work.json
│            chatbot-faq.json  blogs.json
├── css/     style.css  admin.css
├── js/      main.js  animations.js  chatbot.js  forms.js  team.js  work.js  blog.js
├── vendor/  (optional PHPMailer)      logs/  (error log, web-blocked)
├── uploads/resumes/  (private: no web access, no script execution)
└── assets/
    ├── logo/   logo.svg logo.png logo-white.png favicon.png favicon-32.png apple-touch-icon.png
    ├── video/  hero.mp4 (1080p, 6 MB) hero-mobile.mp4 (720p, 3 MB) hero-poster.jpg
    ├── team/  clients/  testimonials/  work/  blog/  icons/ (success.json Lottie)
```

---

## 2. How to add or replace content

Everything is file-based. Edit a file, upload it, refresh. **File names must match exactly** (small letters, hyphens, no spaces).

### Logo
- Files in `assets/logo/`. The header, footer, preloader and chatbot draw the logo from vector code (crisp at every size), traced from your logo.
- To replace the PNG versions (used in emails and Google results), upload a new `logo.png` (transparent, ≥1000px), `logo-white.png` and a square 512×512 `favicon.png`.

### Hero video
- Replace `assets/video/hero.mp4` (MP4/H.264, 1920×1080, under 8 MB, no sound) and `hero-poster.jpg` (one still frame).
- Optional: `hero-mobile.mp4` (1280×720, ~3 MB) is used on phones to save data. If you only upload `hero.mp4`, delete the old `hero-mobile.mp4` so phones don't show the previous video.
- The site plays it at **half speed (slow motion)** automatically, so upload it at normal speed. On "Data Saver" or 2G connections only the poster is shown.
- Compress with HandBrake (preset "Fast 1080p30", RF 28–30, remove audio) or freeconvert.com.

### Team members (`data/team.json` + `assets/team/`)
- Photo name = the member's `id` + `.jpg`. Example: **Dibyaranjan Das → `assets/team/dibyaranjan-das.jpg`**. Use square 800×800 photos with the face centred and the same background style for everyone.

  | Person | Photo file |
  |---|---|
  | Abhisek Mitra | `abhisek-mitra.jpg` |
  | Dibyaranjan Das | `dibyaranjan-das.jpg` |
  | Sudhansu Sekhar Sahoo | `sudhansu-sekhar-sahoo.jpg` |
  | Chitaranjan Sahoo | `chitaranjan-sahoo.jpg` |
  | Himanshu Sekhar Dash | `himanshu-sekhar-dash.jpg` |
  | Chinmaya Ranjan Pradhan | `chinmaya-ranjan-pradhan.jpg` |

- No photo yet? The site shows an animated initials avatar, never a broken image.
- **Add a member:** copy one `{ … }` block in `team.json`, change `id`, `name`, `role`, `level` (1 = top, 2 = middle row, 3 = bottom row), `order`, `photo`, `skills`, `shortBio`, `bio`. Leave `linkedin`, `instagram`, `email` as `""` to hide those icons.
- **Please review the bios.** They were drafted from each person's role and should be checked by the team.

### Client logos (`assets/clients/` + `data/clients.json`)
Transparent PNGs, at least 400px wide: `warzone.png`, `iron-empire.png`, `fitness-hour.png`, `novus.png`, `carbon.png`, `akhandalamani-jewellers.png`. Until a logo is uploaded, the name is shown as a styled wordmark. Add a client by adding a line to `clients.json`.

### Portfolio / case studies (`data/work.json`)
- Each project has `challenge`, `solution`, `results` and `gallery`. **Results currently show `[ADD]` placeholders.** Replace them with real numbers before launch (or remove the results lines).
- Optional thumbnails: upload `assets/work/warzone.jpg` and set `"thumb": "warzone.jpg"`. Gallery: `"gallery": ["assets/work/warzone-1.jpg", "assets/work/warzone-2.jpg"]`.
- Filter categories (`cats`): `social`, `branding`, `performance`, `web`, `software`.

### Testimonials (`data/testimonials.json` + `assets/testimonials/`)
- The 4 included testimonials are **samples** (`"sample": true`), and they show a yellow "Sample" badge on the site. **Replace them with real client quotes (with permission) and set `"sample": false`.** Never publish sample testimonials as real.
- Photos: `person-1.jpg`, `person-2.jpg` … (square 400×400). If missing, initials are shown.

### Stats counter (`data/stats.json`)
The numbers (120+ projects, 45+ clients, 12+ years, 5000+ creatives) are **placeholders**. Change them to your real figures.

### Blog posts (`data/blogs.json` + `assets/blog/`)
- 8 full SEO posts are included. Each has `slug`, `title`, `metaDescription`, `keywords`, `category`, `date`, `readTime`, `image`, `imageAlt`, `excerpt`, `faq` and `content` (HTML).
- In `content`, internal links are written as `{{base}}/services#social-media`. Keep that format.
- **Cover images:** branded covers are included at `assets/blog/<slug>.jpg`. For a premium Indian look, replace them with free photos from **Unsplash** or **Pexels** (search: *"indian business team office"*, *"indian entrepreneur laptop"*, *"indian jewellery store"*, *"indian gym"*, *"indian cafe owner"*). Crop to **1200×675**, compress under **200 KB** (squoosh.app), and save with the **same file name**.
- **New post:** copy a post block, give it a new unique `slug`, and upload `assets/blog/<slug>.jpg`. It appears on the blog page, in related posts and in the sitemap automatically.

### Chatbot Q&A (`data/chatbot-faq.json`)
- Each entry: `{ "q": "question", "keywords": ["word", "two words"], "a": "answer", "actions": ["contact","whatsapp"] }`.
- `actions` (optional) add buttons: `whatsapp`, `contact`, `career`, `services`, `work`, `map`, `lead` (call-back form).
- Use `{{hours}}`, `{{email}}` or `{{phone}}` inside answers to insert values from `config.php`.
- Hinglish and synonyms (price/cost/kitna/rate…) are handled in `js/chatbot.js` (the `SYN` list). Add more words there if needed.
- Unknown questions get the WhatsApp button + a name/phone form, saved to **Admin → Chatbot leads**.

### Social links, WhatsApp number, phone, hours
All in `config/config.php`:
```php
const WHATSAPP_NUMBER = '+91 9556610339';   // any format, digits are extracted
const PHONE_DISPLAY   = '+91 95566 10339';
const WORKING_HOURS   = 'Monday – Saturday, 10:00 AM – 7:00 PM';   // confirm!
const SOCIAL_X = ''; const SOCIAL_YOUTUBE = ''; const SOCIAL_LINKEDIN = '';
const SOCIAL_INSTAGRAM = ''; const SOCIAL_FACEBOOK = '';
```
Paste full URLs (e.g. `'https://instagram.com/uconnekt'`). Empty links still show the icon but open nothing.

### Services
The 6 services (names, descriptions, deliverables, icons) are defined once in `includes/bootstrap.php` → `function services()`. They feed the home page, services page, footer, contact form dropdown and SEO data.

### Lottie animations (optional)
`lottie-web` is loaded from a CDN. Any element like `<div data-lottie="/assets/icons/my-icon.json"></div>` plays when scrolled into view (add `data-lottie-hover` to play on hover). Download free JSON icons from lottiefiles.com into `assets/icons/`. The form success checkmark uses `assets/icons/success.json`.

---

## 3. Test on your computer (optional)

**Option A: XAMPP / Laragon (recommended)**
1. Install XAMPP (apachefriends.org) or Laragon (laragon.org). Start **Apache** and **MySQL**.
2. Copy all files into `htdocs/connekt/` (XAMPP) or `www/connekt/` (Laragon).
3. Open `http://localhost/phpmyadmin` → **New** → create database `connekt` → **Import** → choose `database.sql` → Go.
4. Edit `config/config.php`: `DB_NAME = 'connekt'`, `DB_USER = 'root'`, `DB_PASS = ''` (XAMPP default). Set `DEV_MODE = true` to see errors and the side-gap check in the browser console.
5. Open `http://localhost/connekt/`. Clean URLs work through `.htaccess`. In a sub-folder, change `ErrorDocument 404 /404.php` to `/connekt/404.php`.
6. Visit `http://localhost/connekt/admin/setup.php` to create an admin.

**Option B: PHP's built-in server** (pages only, needs a database for forms):
```bash
php -S localhost:8000 router.php
```

---

## 4. Hostinger deployment A–Z

### Step 1: Domain
- **Bought the domain at Hostinger?** It is already connected. Skip to Step 2.
- **Domain elsewhere (GoDaddy, BigRock…)?** In your registrar, change the nameservers to `ns1.dns-parking.com` and `ns2.dns-parking.com` (confirm the current values in hPanel → **Domains**). Propagation takes up to 24 hours.

### Step 2: Upload the website
1. hPanel → **Websites** → **Manage** (next to `uconnekt.in`) → **File Manager**.
2. Open **`public_html`**. Delete the default files (e.g. `default.php`, `index.html`).
3. Click **Upload** → choose the website ZIP → when done, right-click the ZIP → **Extract** → extract into `public_html`.
4. Make sure `index.php`, `.htaccess` and the folders sit **directly inside `public_html`**, not inside an extra sub-folder. If they landed in `public_html/connekt-india/`, select everything inside it → **Move** → `public_html`.
5. Hidden files: `.htaccess` and `.user.ini` start with a dot. If you don't see them, enable "Show hidden files" in File Manager settings.
6. Delete the uploaded ZIP afterwards.

### Step 3: Create the database
1. hPanel → **Databases** → **Management** (MySQL Databases).
2. Create a database: name (e.g. `connekt`), username, and a strong password. Hostinger adds a prefix, so note the **full** names shown, e.g. `u123456789_connekt`.
3. Click **Enter phpMyAdmin** next to the new database → **Import** tab → **Choose file** → `database.sql` → **Import**. You should see 6 tables.

### Step 4: Edit `config/config.php`
In File Manager, open `public_html/config/config.php` (right-click → **Edit**):
```php
const DB_HOST = 'localhost';
const DB_NAME = 'u123456789_connekt';
const DB_USER = 'u123456789_connekt';
const DB_PASS = 'your-db-password';
```
Save.

### Step 5: Business email + SMTP
1. hPanel → **Emails** → **Email Accounts** → create **`info@uconnekt.in`** with a strong password.
2. In `config.php`:
```php
const SMTP_HOST   = 'smtp.hostinger.com';
const SMTP_PORT   = 465;
const SMTP_SECURE = 'ssl';
const SMTP_USER   = 'info@uconnekt.in';
const SMTP_PASS   = 'the-mailbox-password';
const MAIL_FROM   = 'info@uconnekt.in';           // must be the same as SMTP_USER
const MAIL_TO_ADMIN = 'uconnekt.india@gmail.com'; // where enquiries arrive
```
3. hPanel → **Emails** → **Email Accounts** → DNS settings: make sure **SPF, DKIM and DMARC** show as active (click "Fix" if not). This keeps your emails out of spam.

### Step 6: PHP settings
hPanel → **Advanced** → **PHP Configuration**:
- **PHP version:** 8.2 (or newer).
- **PHP options:** `upload_max_filesize` = **10M**, `post_max_size` = **12M**. (`.user.ini` already requests these.)
- **Extensions:** make sure `pdo_mysql`, `fileinfo`, `openssl` and `mbstring` are enabled (they are by default).

### Step 7: SSL + HTTPS
hPanel → **Security** → **SSL** → install the free SSL for `uconnekt.in` → turn on **Force HTTPS**. The `.htaccess` also forces HTTPS.

### Step 8: Create your admin account
1. Open **`https://uconnekt.in/admin/setup.php`**.
2. Create a username + strong password (10+ characters, letters and numbers).
3. **Delete `admin/setup.php`** in File Manager right away. The dashboard reminds you until it's gone.
4. Log in at **`https://uconnekt.in/admin/`**.

### Step 9: Test everything
- [ ] Contact form (Project Enquiry) → success screen, row in **Admin → Enquiries**, email to `uconnekt.india@gmail.com`, auto-reply to the sender.
- [ ] Career form with a PDF resume → row in **Admin → Careers**, resume downloads, email with the resume attached.
- [ ] Chatbot: ask "do you sell pizza" → leave name + phone → **Admin → Chatbot leads**.
- [ ] Newsletter in the footer → **Admin → Newsletter**.
- [ ] WhatsApp button opens a chat with +91 95566 10339.
- [ ] `https://uconnekt.in/about` works without `.php`. `/blog/website-design-cost-india` opens a post. A random URL shows the 404 page.
- [ ] Check on your phone and on a laptop.

### Step 10: File permissions
File Manager → select folders → **Permissions**: folders **755**, files **644**. `uploads/` and `uploads/resumes/` must be writable by PHP (755 works on Hostinger; use 775 only if uploads fail). `logs/` 755.

### Step 11: Google
1. **Google Search Console** (search.google.com/search-console) → add property `https://uconnekt.in` → verify (DNS TXT record via hPanel → **Domains** → **DNS / Nameservers**) → **Sitemaps** → submit `sitemap.xml`.
2. **Google Business Profile** (business.google.com) → create or claim "Connekt India" at 446, Saheed Nagar, Bhubaneswar 751007 → add the website, phone, hours and photos → ask clients for reviews.
3. Optional: add Google Analytics 4 / Meta Pixel snippets in `includes/head-meta.php` (and update the Privacy Policy's cookies section).

### Step 12: Backups
hPanel → **Files** → **Backups**: download a full backup after launch and before big changes. Also download `config/config.php` and a phpMyAdmin **Export** of the database occasionally.

### Troubleshooting

| Problem | Fix |
|---|---|
| **Emails not sending** | Check `logs/error.log` (download via File Manager). `MAIL_FROM` must equal `SMTP_USER`. Re-check the mailbox password. Try port `587` with `SMTP_SECURE = 'tls'`. Make sure SPF/DKIM are active. Check Gmail spam and mark "Not spam". |
| **500 Internal Server Error** | Set PHP to 8.2. Temporarily set `DEV_MODE = true` in `config.php` to see the error. If it started after editing `.htaccess`, re-upload the original. Check JSON files for a missing comma (paste into jsonlint.com). |
| **"Database connection failed"** | Use the full prefixed DB name/user from hPanel; `DB_HOST` is `localhost`; import `database.sql`. |
| **Forms say "session expired"** | Refresh the page. If it persists, disable any LiteSpeed/page cache for this site (hPanel → **Advanced** → **Cache Manager**) and clear the cache. |
| **Video doesn't autoplay on mobile** | Phones in Data Saver / Low Power mode block autoplay. The poster image shows instead, which is expected. Make sure the MP4 is H.264 and has no audio track. |
| **A side gap / sideways scroll appears** | Usually caused by an image or embed you added with a fixed width. Set `DEV_MODE = true`, open the browser console, and the side-gap check lists the overflowing element. Never use `100vw` widths. |
| **404 on clean URLs (`/about`)** | `.htaccess` is missing or not in `public_html` root. Enable hidden files and re-upload it. |
| **Resume upload fails** | Raise `upload_max_filesize` (Step 6). Make sure `uploads/resumes` exists and is writable. Only PDF/DOC/DOCX under 5 MB are accepted. |
| **Locked out of admin** | Wait 15 minutes. If you forgot the password, in phpMyAdmin delete your row in `admins`, re-upload `admin/setup.php`, create a new admin, then delete `setup.php` again. |

---

## 5. Updating the website later

- **Text and lists:** edit the matching JSON in `/data` (team, clients, testimonials, stats, work, blogs, chatbot) and upload the single file. Use jsonlint.com to check for typos.
- **Page text:** edit the page's `.php` file (e.g. `about.php`). The HTML is readable; change the words between the tags.
- **Images and video:** upload with the same file name to replace. Browsers pick up changes automatically (cache-busting is built in).
- **Colours, spacing, fonts:** `css/style.css` (the brand colours are at the top under "Tokens").
- **Contact details, WhatsApp, socials, hours:** `config/config.php`.
- Always keep a copy of a file before editing it.

---

## Technical notes

- **No build step:** custom CSS instead of Tailwind. Tailwind's CDN script adds about 300 KB of JavaScript at runtime, which would cost Lighthouse points, so the design system lives in `css/style.css` (fluid `clamp()` type, `minmax()` grids, no `100vw`, full-bleed sections with a `.wrap` container, `100svh` hero, safe-area insets).
- **Animations:** the core scroll reveals use IntersectionObserver, so content still appears even if a CDN is blocked. GSAP + ScrollTrigger (CDN) add parallax and scrubbed effects. Only `transform` and `opacity` are animated.
- **Icons:** Lucide (ISC licence) and Font Awesome brand icons (CC BY 4.0), inlined as SVG.
- **Email:** built-in SMTP client (SSL/STARTTLS, attachments). If `vendor/PHPMailer/src/PHPMailer.php` exists, PHPMailer is used instead.
- **Security:** PDO prepared statements, CSRF tokens, honeypot, IP rate limiting, server-side validation, upload MIME + extension + signature checks with random file names, private uploads folder, `password_hash`, admin lockout, session regeneration, CSV-injection guard, security headers.
- **Tested** at 320, 360, 375, 390, 412, 430, 768, 820, 1024, 1280, 1366, 1536, 1920 and 2560px: no horizontal overflow on any page, and no JavaScript errors.
