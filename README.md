# STCMS - Hybrid PHP/React CMS Library

> **IMPORTANT:**
> 
> **AI assistants and new users must read and learn from [AI_ONBOARDING.md](./AI_ONBOARDING.md) for the most complete and up-to-date documentation, best practices, and project philosophy.**

---

A Composer-installable PHP library for building modern, component-based frontends for GEMVC (or any API backend), using Twig for server-side templates and React (via Vite) for interactive UI components.

## Features

- 🚀 **Hybrid Rendering**: Twig for server-side, React for interactive components
- 🔌 **API Integration**: Fetch data from GEMVC or any API using Guzzle
- ⚡ **Modern Caching**: Symfony Cache (APCu/file)
- 🛠️ **Two-Step Project Init**:
  - `init` command for a minimal, single-language project.
  - `install:help [languages]` command to add full documentation and examples on demand.
- 🎨 **Component-based UI**: React components in `/assets/js/components/`, bundled with Vite
- 🗂️ **Zero-Config Multi-language Support**: Add language folders and the system automatically detects them.
- 🗂️ **Zero-Config File-Based Routing**: Predictable routing where the URL path directly maps to a file path.
- 🔒 **Config via .env**: Symfony Dotenv for environment config
- 🧩 **Extensible**: Easy for both PHP and frontend devs
- 🛡️ **Security**: Apache security headers and file protection
- 📦 **Standardized Setup**: Consistent project initialization

---

## Architecture Overview

- **Twig**: For server-side rendering of pages and templates.
- **React**: For interactive UI, mounted via a registry-based system.
- **Vite**: For fast development and production builds of frontend assets.
- **.htaccess & index.php**: For routing, security, and application bootstrapping.
- **Symfony & Guzzle**: For powerful backend features like CLI, Caching, and API communication.

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

## Hybrid Rendering & Registry-based React Mounting

- **Twig** renders the main HTML structure and server-side content.
- **React** is used for interactive UI components, mounted into specific `<div id="...\"></div>` elements.
- **Component registry** (`assets/js/registry.js`) maps DOM IDs to React components and prop parsers.
- **app.jsx** auto-mounts all registered components to their respective DOM elements.
- **Vite** bundles React code for production (`public/assets/build/app.js`).
- **Tailwind CSS** (optional) for styling; **Prism.js** for code highlighting in docs/examples.

---

## Routing, Security, and Best Practices

- **.htaccess** routes all requests to `index.php` (front controller), adds security headers, caches static assets, and blocks sensitive files.
- **index.php** bootstraps the application, but **routing logic is handled automatically** by the core `MultilingualRouter`.
- The router uses a simple and powerful **file-based routing** system. It maps a URL path like `/en/about/us` to a template file (`pages/en/about/us.twig`) or to a directory's index file (`pages/en/about/index.twig`).
- Dynamic data is handled via standard GET parameters (`?id=123`), which are automatically available in your templates.

---

## Philosophy

- Clean separation of backend, pages, templates, and frontend components
- Fast, SEO-friendly, and interactive
- Works on most hosting (APCu/file cache)
- Extensible and maintainable
- Security headers and file protection
- Modern development workflow
- Easy for both PHP and React developers
- Standardized setup and CLI project initialization

---

## Getting Started

1. Install via Composer:
   ```bash
   composer require gemvc/stcms
   ```
2. Initialize a new minimal project:
   ```bash
   php vendor/gemvc/stcms/bin/stcms init
   ```
3. (Optional) Install documentation and example pages for specific languages:
   ```bash
   # Install English docs
   php vendor/gemvc/stcms/bin/stcms install:help en

   # Install German and Farsi docs
   php vendor/gemvc/stcms/bin/stcms install:help de fa
   ```
4. Install frontend dependencies and build assets:
   ```bash
   npm install
   npm run build
   ```
5. Configure environment in `.env`.
6. Start the PHP server:
   ```bash
   php -S localhost:8000
   ```
7. **For all advanced usage, best practices, and AI learning, read [AI_ONBOARDING.md](./AI_ONBOARDING.md).**

---

**STCMS** - Making hybrid Multilanguage PHP/React development simple, powerful, and AI-ready! 🚀

