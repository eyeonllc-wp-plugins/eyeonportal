=== EyeOn Portal ===
Contributors: eyeon
Tags: elementor, mall, center, stores, deals, events, map, careers, news
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.34
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WordPress site to EyeOn Portal and display live center content with Elementor widgets.

== Description ==

EyeOn Portal is the platform where mall and center teams manage stores, deals, events, news, careers, banners, hours, and more. This WordPress plugin connects your site to EyeOn Portal via an API access token and renders that content on your public website.

Content is always fetched live from EyeOn Portal — no duplicate data entry. The API environment (production, staging, or local dev) is detected automatically from your token prefix.

= Elementor widgets =

Add widgets from the **EyeOn Portal** category in Elementor:

* **Stores** — retailer directory with categories, tags, and single store pages
* **Deals** — promotions and offers listing
* **Events** — calendar and event listings
* **Careers** — job listings
* **News** — blog and news articles
* **Banners** — homepage slider managed in EyeOn Portal
* **Center Hours** — opening hours display
* **Map** — interactive 3D floor map
* **Search** — site-wide center content search

= Additional features =

* Single detail pages for stores, deals, events, careers, and news
* Optional AI chatbot when enabled for your center in EyeOn Portal
* SEO and Open Graph meta tags for single pages (Rank Math integration with fallbacks)
* Automatic plugin updates from GitHub releases
* Remote fleet updates via EyeOn Portal Manage WP (for staff-managed sites)

Manage all center content at [EyeOn Portal](https://eyeonportal.com/).

== Installation ==

1. Download the latest `eyeonportal.zip` from [GitHub Releases](https://github.com/mycenterportal/wp-eyeonportal-plugin/releases).
2. In WordPress, go to **Plugins → Add New → Upload Plugin**, select the zip, and install.
3. Activate the plugin. It must live in `wp-content/plugins/eyeonportal`.
4. Go to **EyeOn Portal → Settings** (under Appearance) and paste your API access token from EyeOn Portal.
5. Assign listing pages for Stores, Deals, Events, News, Careers, and Map in plugin settings.
6. Edit your pages in Elementor and add EyeOn Portal widgets from the widget panel.

= Shortcodes =

The following shortcodes are available under **EyeOn Portal → Shortcodes** in plugin settings:

* `[mcp_site_name]` — WordPress site title
* `[mcp_site_url]` — WordPress site URL
* `[mcp_site_domain]` — WordPress site domain

== Changelog ==

= 1.0.34 =
* Updated plugin documentation (README and readme)
* Switched update source to mycenterportal/wp-eyeonportal-plugin repository
