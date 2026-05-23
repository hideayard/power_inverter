# Astro Migration TODO

- [x] Initialize Astro project structure in `astro/`
- [x] Add base layout and global navigation menu (including Dashboard V2 + Dark)
- [x] Migrate `index.php` to `astro/src/pages/index.astro`
- [x] Migrate `dashboard-inverter.php` to `astro/src/pages/dashboard-inverter.astro`
- [x] Migrate `dashboard-v2.php` to `astro/src/pages/dashboard-v2.astro`
- [x] Migrate `dashboard-v2-dark.php` to `astro/src/pages/dashboard-v2-dark.astro`
- [x] Migrate `ma.php` to `astro/src/pages/ma.astro`
- [ ] Copy static assets to `astro/public/assets`
- [x] Update internal links/menu routes from `.php` to Astro routes
- [ ] Install dependencies and run Astro dev server
- [ ] Verify pages load and scripts/styles work
- [x] Migrate `auth/login.php` to `astro/src/pages/auth/login.astro`
- [ ] Migrate `auth/register.php` to `astro/src/pages/auth/register.astro`
- [ ] Migrate `auth/forgot-password.php` to `astro/src/pages/auth/forgot-password.astro`
- [ ] Migrate `auth/terms.php` to `astro/src/pages/auth/terms.astro`
- [ ] Migrate `auth/privacy.php` to `astro/src/pages/auth/privacy.astro`
- [ ] Update auth redirects/links from `/auth/*.php` to Astro routes `/auth/*`
- [ ] Build-check Astro app after auth migration
