# Instant Images (schulverwalter fork)

One-click image uploads from Unsplash, Openverse, Pixabay, Pexels, and Giphy straight into the WordPress media library — without ever leaving WordPress.

> **This is a fork.** The original [Instant Images](https://wordpress.org/plugins/instant-images/) plugin was created and is maintained by [Darren Cooney](https://github.com/dcooney) / [Connekt Media](https://connekthq.com). All credit for the plugin goes to them — thank you for building and open-sourcing it under the GPL.
>
> This fork exists only to adapt the plugin to our own needs. It is **not** affiliated with, endorsed by, or supported by Connekt Media. Please do not send bug reports about this fork upstream.

Upstream repository: <https://github.com/dcooney/instant-images>

## What is different in this fork

- **No paid add-on promotion.** All references to, and gating for, the commercial "Extended" add-on are gone, along with the license key screen and the add-on updater.
- **No advertising.** The "Our Plugins" section in the settings and the sponsored image slots in the results grid have been removed.
- **Native WordPress styling.** The plugin now uses standard wp-admin markup and styling (`.wrap`, `.form-table`, `.button`, `.wp-list-table`, Dashicons) instead of its own design system. The Font Awesome CDN dependency is gone, and the stylesheet shrank by roughly 40%.
- **Search term is kept when switching providers.** Searching for something on Unsplash and then switching to Pexels re-runs the same search instead of resetting to the default listing. Provider-specific search filters are reset, since they are not portable between APIs.
- **Search history for everyone.** Recent searches are stored in the browser's local storage and offered in a dropdown. Previously this required the paid add-on.
- **The Instant Images block is always available.** It used to be locked behind an add-on license.

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer

## Installation

This plugin is **not** published on wordpress.org, so it is installed from a ZIP file.

### Option 1: Install a release ZIP (recommended)

1. Open the [Releases page](https://github.com/schulverwalter/wp-instant-images/releases) and download `instant-images.zip` from the latest release.
2. In WordPress, go to **Plugins → Add New Plugin → Upload Plugin**.
3. Choose the ZIP file, click **Install Now**, then **Activate**.
4. Head to **Media → Instant Images** to start uploading.

The release ZIP contains only the files the plugin needs at runtime, and unpacks into an `instant-images/` folder — so an update replaces the previous install cleanly instead of creating a second copy.

To update, download the newer release ZIP and upload it the same way. WordPress will ask whether to replace the existing plugin; confirm, and your settings are preserved (they live in the database, not in the plugin folder).

### Option 2: Install straight from the repository

If you want the current state of a branch rather than a release, use GitHub's **Code → Download ZIP** button and upload that file the same way. Two caveats:

- The folder is named after the branch (`wp-instant-images-main/`), so a later install from a release ZIP lands next to it rather than replacing it. Pick one method and stick with it.
- The archive also contains the development sources (`src/`, `webpack/`, `composer.json`, …). They are harmless but unnecessary on a production server.

### Option 3: Install with WP-CLI

```bash
wp plugin install https://github.com/schulverwalter/wp-instant-images/releases/latest/download/instant-images.zip --activate
```

### Option 4: Deploy with Git or Composer

For servers managed with Git, the repository can also be checked out directly into `wp-content/plugins/instant-images`. The `build/` directory is committed, so no build step is required on the server:

```bash
git clone https://github.com/schulverwalter/wp-instant-images.git wp-content/plugins/instant-images
```

### A note on automatic updates

Plugins installed from a ZIP get no update notices in wp-admin. If you want them, the two common approaches for GitHub-only plugins are:

- Bundle [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) into this plugin and point it at this repository's releases. Update notices then appear in wp-admin exactly like for wordpress.org plugins.
- Install [Git Updater](https://git-updater.com/) on the site and add a `GitHub Plugin URI` header to this plugin. One extra plugin on the site, but it handles every GitHub-hosted plugin at once.

Neither is set up yet — releases are installed manually for now.

## API keys

Access to the image providers goes through Instant Images' proxy at `proxy.getinstantimages.com`, using shared default keys. You can replace them with your own under **Settings → Instant Images**, or define them in `wp-config.php`:

```php
define( 'INSTANT_IMAGES_UNSPLASH_KEY', '...' );
define( 'INSTANT_IMAGES_PIXABAY_KEY', '...' );
define( 'INSTANT_IMAGES_PEXELS_KEY', '...' );
define( 'INSTANT_IMAGES_GIPHY_KEY', '...' );
```

Keys set this way are shown as read-only in the settings screen.

## Development

```bash
npm install        # also runs composer install
npm run dev        # watch mode
npm run build      # production build into build/
npm run lint       # eslint, stylelint and phpcs
npm run zip        # build, then package dist/instant-images.zip
```

Build output in `build/` is committed so the repository can be installed as-is.

Creating a release: push a tag, publish a GitHub Release, and the [`Build release ZIP`](.github/workflows/release.yml) workflow attaches an installable `instant-images.zip` to it. `npm run zip` produces the same archive locally.

## Supported image providers

- [Unsplash](https://unsplash.com)
- [Openverse](https://wordpress.org/openverse/)
- [Pixabay](https://pixabay.com)
- [Pexels](https://pexels.com)
- [Giphy](https://giphy.com)

## License

GPL-2.0-or-later, same as the upstream plugin. See [LICENSE.txt](LICENSE.txt).
