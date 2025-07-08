# STCMS - Modern PHP Hybrid CMS Library

> **NOTE:**
> For the most up-to-date and complete documentation, see [AI_ONBOARDING.md](../../AI_ONBOARDING.md) and [README.md](../../README.md) in the project root.

---

## Project Goals & Philosophy

STCMS is a Composer-installable PHP library for building modern, component-based frontends for GEMVC (or any API backend), using Twig for server-side templates and React (via Vite) for interactive UI components. It is designed for:
- Hybrid rendering (SEO-friendly, fast, and interactive)
- Security and maintainability
- Extensibility for both PHP and frontend developers
- Standardized project initialization and structure

---

## Architecture Overview

- **Twig** for server-side rendering of pages and templates
- **React** for interactive UI, mounted via a registry-based system (`assets/js/registry.js`)
- **Vite** for fast development and production builds of frontend assets
- **.htaccess** and **index.php** for routing, security, and application bootstrapping
- **Symfony Cache, Dotenv, Console, Filesystem, Guzzle, Twig** (all included via Composer)

---

## Project Structure (Current)

```
project-root/
├── assets/
│   ├── js/
│   │   ├── app.jsx                # Main React entry point
│   │   ├── registry.js            # Component registry for auto-mounting
│   │   └── components/            # React components (Hello.jsx, UserProfile.jsx, etc.)
│   └── css/                       # (Optional) Tailwind or custom CSS
├── pages/
│   ├── index.twig                 # Main landing page (Twig)
│   └── react.twig                 # Example page showing React integration
├── templates/
│   └── default.twig               # Base HTML layout (Twig)
├── public/
│   └── assets/build/app.js        # Built JS bundle (from Vite)
├── vite.config.js                 # Vite config for building frontend assets
├── package.json                   # NPM dependencies and scripts
├── composer.json                  # PHP dependencies
├── index.php                      # PHP entry point (loads Twig, routes, etc.)
├── .env                           # Environment config (Symfony Dotenv)
├── .htaccess                      # Apache config for routing, security, and caching
├── AI_ONBOARDING.md               # Full AI and user onboarding guide (read this!)
└── vendor/                        # Composer dependencies (including STCMS)
```

---

## Key Concepts

- **Registry-based React mounting:** All React components are registered in `assets/js/registry.js` and auto-mounted by `app.jsx`.
- **Hybrid rendering:** Use Twig for server-side, React for client-side interactivity.
- **Modern workflow:** Vite for frontend, Composer for backend, standardized setup.
- **Security:** .htaccess for routing, security headers, and static asset caching; index.php for app bootstrapping.

---

## Where to Learn More

- For all advanced usage, best practices, and AI learning, see [AI_ONBOARDING.md](../../AI_ONBOARDING.md).
- For a quick start and philosophy, see [README.md](../../README.md).

---

**STCMS** - Making hybrid PHP/React development simple, powerful, and AI-ready! 🚀 