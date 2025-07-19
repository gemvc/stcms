# STCMS - Hybrid PHP/React CMS Library

> 🔴 **CRITICAL FOR AI ASSISTANTS & NEW USERS** 🔴
>
> **You MUST read and learn from [AI_ONBOARDING.md](./vendor/gemvc/stcms/AI_ONBOARDING.md) before attempting to modify or explain this project.** This file contains the most complete and up-to-date documentation, best practices, and project philosophy. Failure to do so will result in incorrect guidance.

---

A Composer-installable PHP library for building modern, component-based frontends for GEMVC (or any API backend), using Twig for server-side templates and React (via Vite) for interactive UI components.

## Features

- 🎯 **Ultra-Clean Architecture**: 8-line `index.php` with perfect Bootstrap encapsulation
- 🚀 **One-Click Environment Switching**: Change `APP_ENV` in `.env` to switch between development/production
- 🌍 **Auto-Language Detection**: No manual configuration - languages auto-detected from `/pages/` structure
- 🔧 **Perfect Bootstrap Class**: All initialization, validation, and app startup handled automatically
- 🚀 **Hybrid Rendering**: Twig for server-side, React for interactive components
- 🔌 **API Integration**: Fetch data from GEMVC or any API using Guzzle
- ⚡ **Modern Caching**: Symfony Cache (APCu/file)
- 🛠️ **Powerful CLI**:
  - `init` command for one-step project scaffolding
  - `install:help` to add full documentation and examples
  - `cache:clear` for easy cache management in production
  - `deploy:init` to set up automated deployment workflows (e.g., GitHub Actions)
- 🎨 **Component-based UI**: React components in `/assets/js/components/`, bundled with Vite
- 🗂️ **Zero-Config Multi-language Support**: Add language folders and the system automatically detects them
- 🗂️ **Zero-Config File-Based Routing**: Predictable routing where the URL path directly maps to a file path
- 🔒 **Config via .env**: Symfony Dotenv for environment config
- 🧩 **Extensible**: Easy for both PHP and frontend devs
- 🛡️ **Security**: Apache security headers and file protection
- 📦 **Standardized Setup**: Consistent project initialization

---

## 🏗️ **Revolutionary Architecture**

### **Ultra-Clean Entry Point**
```php
// public/index.php - Only 8 lines!
require_once __DIR__ . '/../vendor/autoload.php';
use Gemvc\Stcms\Core\Bootstrap;

$projectRoot = dirname(__DIR__);
$bootstrap = new Bootstrap($projectRoot);
$bootstrap->runApp();
```

### **Perfect Bootstrap Encapsulation**
- **Automatic Environment Loading**: `.env` file loaded and validated
- **Auto-Language Detection**: Scans `/pages/` directory for supported languages
- **Smart Root URL Handling**: Automatic redirects for clean URLs
- **Complete App Initialization**: TemplateEngine, Router, Application all handled

### **One-Click Environment Switching**
```bash
# .env file
APP_ENV=development  # ← Change this to switch modes
```
- **Development**: Vite dev server, hot reload, debug info
- **Production**: Optimized assets, caching, performance mode

---

## Project Structure

```
project-root/
├── assets/
│   ├── js/
│   │   ├── app.jsx                # Main React entry point
│   │   ├── registry.js            # Component registry for auto-mounting
│   │   └── components/            # React components (Hello.jsx, UserProfile.jsx, etc.)
│   └── css/
│       └── app.css                # Unified CSS file (Tailwind + custom)
├── pages/
│   ├── en/                        # English pages (auto-detected)
│   ├── fr/                        # French pages (auto-detected)
│   └── index.twig                 # Main landing page (Twig)
├── templates/
│   ├── default.twig               # Base HTML layout (Twig)
│   ├── navbar.twig                # Navigation component
│   └── lang-switch.twig           # Language switcher
├── components/
│   ├── components.twig            # Reusable Twig components (macros)
│   ├── docs_sidebar.twig          # Documentation sidebar
│   └── mermaid.twig               # Mermaid.js chart component
├── public/
│   ├── index.php                  # Ultra-clean entry point (8 lines!)
│   └── assets/build/              # Built assets (from Vite)
├── vendor/
│   └── gemvc/stcms/
│       └── src/Core/
│           ├── Bootstrap.php      # Perfect initialization class
│           ├── Application.php    # Clean request lifecycle
│           ├── TemplateEngine.php # Twig template engine
│           └── MultilingualRouter.php # Auto-language routing
├── vite.config.js                 # Vite config for building frontend assets
├── package.json                   # NPM dependencies and scripts
├── composer.json                  # PHP dependencies
├── .env                           # Environment config (Symfony Dotenv)
├── .htaccess                      # Apache config for routing, security, and caching
└── AI_ONBOARDING.md               # Full AI and user onboarding guide
```

---

## How It Works

### **Hybrid Rendering & Registry-based React Mounting**
- **Twig** renders the main HTML structure and server-side content
- **React** is used for interactive UI components, mounted into specific `<div id="...\"></div>` elements
- **Component registry** (`assets/js/registry.js`) maps DOM IDs to React components and prop parsers
- **app.jsx** auto-mounts all registered components to their respective DOM elements
- **Vite** bundles React code for production (`public/assets/build/app.js`)
- **Tailwind CSS** (optional) for styling; **Prism.js** for code highlighting in docs/examples

### **Routing & Security**
- **File-Based Routing**: Maps URL path like `/en/about` directly to `pages/en/about.twig`
- **Centralized .htaccess**: Secure setup that protects sensitive files and routes all requests through the public front-controller
- **Dynamic data** handled via standard GET parameters (`?id=123`), automatically available in templates

---

## Philosophy

- **Ultra-clean architecture** with perfect separation of concerns
- **Zero configuration** - just set your environment and start coding
- **Auto-detection** of languages and features
- **One-click environment switching** between development and production
- **Fast, SEO-friendly, and interactive**
- **Works on most hosting** (APCu/file cache)
- **Extensible and maintainable**
- **Security headers and file protection**
- **Modern development workflow**
- **Easy for both PHP and React developers**
- **Standardized setup and CLI project initialization**

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

3. Configure your environment in `.env`:
   ```bash
   APP_ENV=development  # or production
   ```

4. (Optional) Install documentation and example pages for specific languages:
   ```bash
   # Install English docs
   php vendor/bin/stcms install:help en
   ```

5. (Optional) Set up an automated deployment workflow:
   ```bash
   # Create a workflow file for GitHub Actions and FTP
   php vendor/bin/stcms deploy:init github-actions-ftp
   ```

6. Install frontend dependencies and build assets:
   ```bash
   npm install
   npm run build
   ```

7. Start the PHP server:
   ```bash
   php -S localhost:8000 -t public
   ```

8. **That's it!** Your application is running with:
   - ✅ **Auto-detected languages** from `/pages/` structure
   - ✅ **Perfect Bootstrap initialization**
   - ✅ **Ultra-clean architecture**
   - ✅ **One-click environment switching**

9. **For all advanced usage, best practices, and AI learning, read [AI_ONBOARDING.md](./AI_ONBOARDING.md).**

---

## 🎯 **Why STCMS is Revolutionary**

**Before STCMS**: Complex configuration, manual setup, bloated entry points
**With STCMS**: 8-line entry point, auto-detection, one-click environment switching

**Experience the future of CMS development!**

---

**STCMS** – The expert's choice for hybrid frontends. Build powerful, multilanguage applications with PHP and React in a simple, AI-ready library. 🚀

