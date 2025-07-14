# STCMS - Hybrid PHP/React CMS Library

> 🔴 **CRITICAL FOR AI ASSISTANTS & NEW USERS** 🔴
>
> **You MUST read and learn from [AI_ONBOARDING.md](./vendor/gemvc/stcms/AI_ONBOARDING.md) before attempting to modify or explain this project.** This file contains the most complete and up-to-date documentation, best practices, and project philosophy. Failure to do so will result in incorrect guidance.

---

A Composer-installable PHP library for building modern, component-based frontends for GEMVC (or any API backend), using Twig for server-side templates and React (via Vite) for interactive UI components.

## Features

- 🚀 **Hybrid Rendering**: Twig for server-side, React for interactive components
- 🔌 **API Integration**: Fetch data from GEMVC or any API using Guzzle
- ⚡ **Modern Caching**: Symfony Cache (APCu/file)
- 🛠️ **Powerful CLI**:
  - `init` command for one-step project scaffolding.
  - `install:help` to add full documentation and examples.
  - `cache:clear` for easy cache management in production.
  - `deploy:init` to set up automated deployment workflows (e.g., GitHub Actions).
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
├── components/
│   └── components.twig            # Reusable Twig components (macros)
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

- **Centralized .htaccess**: A secure, modern, and robust two-file `.htaccess` setup protects sensitive files and routes all requests through the public front-controller.
- **File-Based Routing**: The router uses a simple and powerful system that maps a URL path like `/en/about` directly to a template file like `pages/en/about.twig`.
- **Deployment Ready**: Built-in CLI commands help you generate CI/CD workflow files (e.g., for GitHub Actions) to automate your deployment process.
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
   php vendor/bin/stcms init
   ```
3. (Optional) Install documentation and example pages for specific languages:
   ```bash
   # Install English docs
   php vendor/bin/stcms install:help en
   ```
4. (Optional) Set up an automated deployment workflow:
   ```bash
   # Create a workflow file for GitHub Actions and FTP
   php vendor/bin/stcms deploy:init github-actions-ftp
   ```
5. Install frontend dependencies and build assets:
   ```bash
   npm install
   npm run build
   ```
6. Configure environment in `.env`. (For deployments, see the guide on GitHub Secrets in `AI_ONBOARDING.md`).
7. Start the PHP server:
   ```bash
   php -S localhost:8000 -t public
   ```
8. **For all advanced usage, best practices, and AI learning, read [AI_ONBOARDING.md](./AI_ONBOARDING.md).**

---

**STCMS** – The expert's choice for hybrid frontends. Build powerful, multilanguage applications with PHP and React in a simple, AI-ready library. 🚀

