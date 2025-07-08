# AI Onboarding Guide for STCMS

Welcome to the STCMS (Static/Server-side + React Hybrid CMS) project! This guide is designed to help AI assistants (and humans) quickly understand, extend, and use this CMS. It covers the project structure, workflows, and best practices for creating pages, templates, and React components.

---

## 1. What is STCMS?

STCMS is a Composer-installable PHP library for building modern, component-based frontends for GEMVC (or any API backend), using Twig for server-side templates and React (via Vite) for interactive UI components. It is designed for hybrid rendering, security, extensibility, and a modern workflow for both PHP and frontend developers.

---

## 2. Composer Dependencies Provided by STCMS

When you install STCMS via Composer, you automatically get a suite of powerful PHP packages that enable modern, secure, and scalable web development:

| Package                | Purpose/Role                                                                 |
|------------------------|------------------------------------------------------------------------------|
| `php >=8.0`            | Requires PHP 8.0 or higher                                                   |
| `symfony/cache`        | Modern caching (APCu or file-based) for performance and scalability          |
| `symfony/dotenv`       | Environment variable management via `.env` files                             |
| `symfony/console`      | CLI commands (for project initialization, cache clearing, etc.)              |
| `symfony/filesystem`   | File and directory management utilities                                      |
| `guzzlehttp/guzzle`    | HTTP client for API integration (fetching data from GEMVC or other APIs)     |
| `twig/twig`            | Templating engine for server-side rendering                                  |
| `ext-apcu` (optional)  | In-memory caching for best performance (recommended, but not required)        |

**What this means:**
- You do not need to install these packages separately—they are included with STCMS.
- You have access to best-in-class tools for caching, environment config, API integration, templating, and CLI operations out of the box.
- All code examples and best practices in this guide assume these dependencies are available.

---

## 3. Project Structure Overview

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
└── vendor/                        # Composer dependencies (including STCMS)
```

---

## 4. Entry Point and Web Server Configuration: index.php & .htaccess

### .htaccess
- Ensures all requests (except for real files/folders) are routed to `index.php` (front controller pattern).
- Enables clean URLs and centralized routing in your PHP app.
- Adds security headers to protect against common web vulnerabilities.
- Caches static assets (CSS, JS, images, fonts) for performance.
- Prevents web access to sensitive files (e.g., .env, composer.json).

**Example:**
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
# ... (security headers, cache, file protection)
```

### index.php
- Main entry point for all requests routed by `.htaccess`.
- Loads Composer autoloading and environment variables.
- Initializes STCMS core classes (Application, Router, TemplateEngine, ApiClient, etc.).
- Bootstraps the application and handles all routing and rendering.

**Example:**
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\Router;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\ApiClient;
use Symfony\Component\Dotenv\Dotenv;
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');
$apiClient = new ApiClient($_ENV['API_BASE_URL']);
$templateEngine = new TemplateEngine([
    __DIR__ . '/pages',
    __DIR__ . '/templates',
]);
$router = new Router();
$app = new Application($router, $templateEngine, $apiClient);
$app->run();
```

**Best Practices:**
- Use `.htaccess` for routing, security, and caching.
- Keep all application bootstrapping and routing logic in `index.php`.
- This setup is a best practice for modern PHP apps, ensuring security, flexibility, and a clean separation of concerns.

---

## 5. Core STCMS PHP Classes & Features

### Application.php
- Main orchestrator: manages session, JWT authentication, and coordinates all components.
- Handles request/response flow and provides authentication helpers.

### Router.php
- URL routing and request handling.
- Supports exact and dynamic routes (`/user/{id}`), automatic template rendering, and route parameter extraction.

### TemplateEngine.php
- Twig integration with custom functions:
  - `asset()` - Generate asset URLs
  - `route()` - Generate route URLs
  - `json_encode()` - JSON encoding helper
  - `is_authenticated()` - Authentication check

### ApiClient.php
- HTTP API communication using Guzzle.
- Supports GET, POST, PUT, DELETE, JWT authentication, error handling, and timeouts.

### Request.php & Response.php
- Encapsulate HTTP request/response.
- Support for JSON, AJAX, redirects, and header management.

---

## 6. Hybrid Rendering: Twig + React

- **Twig** renders the main HTML structure and server-side content.
- **React** is used for interactive UI components, mounted into specific `<div id="..."></div>` elements.
- Data is passed from PHP/Twig to React via `data-*` attributes (e.g., `data-user`, `data-jwt`).
- Vite bundles React code for production.
- Example:
  ```twig
  <div id="user-profile-root" data-user='{{ user|json_encode }}' data-jwt="{{ jwt }}"></div>
  ```
  ```jsx
  const el = document.getElementById('user-profile-root');
  if (el) {
    const user = JSON.parse(el.dataset.user);
    const jwt = el.dataset.jwt;
    createRoot(el).render(<UserProfile user={user} jwt={jwt} />);
  }
  ```

---

## 7. Authentication & Security Model

- **JWT is only exposed to React if the user is authenticated** (JWT is present in PHP session).
- **No JWT if not authenticated**—React knows to show login or restrict access.
- **React uses JWT for API requests** (e.g., via Axios/fetch, in Authorization header).
- **JWT is never generated or verified in the frontend**—all JWT logic is handled by the backend (GEMVC API).
- **Session management and login/logout handled by PHP backend.**
- **Best practice:** Always validate JWTs on the backend for every API request.

---

## 8. CLI Project Initialization & Setup Folder

- Use `composer require gemvc/stcms` to install.
- Run `php vendor/gemvc/stcms/bin/stcms init` to scaffold a new project (copies files from `src/setup/`).
- The setup folder includes example templates, pages, React entry, and components for a quick start.

---

## 9. Creating Pages
- **Location:** `pages/`
- **File Type:** `.twig`
- **How:**
  1. Create a new file, e.g., `about.twig`.
  2. Start with `{% extends "default.twig" %}` to use the base layout.
  3. Define content blocks:
     ```twig
     {% extends "default.twig" %}
     {% block title %}About Us{% endblock %}
     {% block content %}
       <h1>About Our Company</h1>
       <div id="hello-root"></div> {# Mounts the Hello React component #}
     {% endblock %}
     ```
  4. Add any React mount points as needed.

---

## 10. Creating Templates (Layouts)
- **Location:** `templates/`
- **File Type:** `.twig`
- **Purpose:** Layouts, partials, reusable blocks.
- **How:**
  1. Create a new file, e.g., `custom-layout.twig`.
  2. Use Twig blocks for extensibility:
     ```twig
     <!DOCTYPE html>
     <html>
     <head>
       <title>{% block title %}My Site{% endblock %}</title>
     </head>
     <body>
       {% block content %}{% endblock %}
     </body>
     </html>
     ```
  3. Extend this template in your pages.

---

## 11. Creating React Components
- **Location:** `assets/js/components/`
- **File Type:** `.jsx`
- **How:**
  1. Create a new file, e.g., `MyComponent.jsx`.
  2. Export as default:
     ```jsx
     import React from 'react';
     export default function MyComponent() {
       return <div>Hello from MyComponent!</div>;
     }
     ```
  3. Register in `assets/js/registry.js`:
     ```js
     import MyComponent from './components/MyComponent';
     export const registry = {
       'my-component-root': {
         component: MyComponent,
         getProps: (el) => ({}),
       },
       // ...other components
     };
     ```
  4. Add a mount point in your Twig page:
     ```html
     <div id="my-component-root"></div>
     ```
  5. Rebuild assets: `npm run build`

---

## 12. Registry & Auto-Mounting System
- **Purpose:** Maps DOM element IDs to React components and prop-parsing functions.
- **How it works:**
  - `registry.js` defines which components to mount and how to parse their props from `data-*` attributes.
  - `app.jsx` loops through the registry, finds each mount point, and renders the component with props.
- **Example:**
  ```js
  import Hello from './components/Hello';
  export const registry = {
    'hello-root': {
      component: Hello,
      getProps: (el) => ({}),
    },
    // ...
  };
  // In app.jsx:
  Object.entries(registry).forEach(([id, { component: Component, getProps }]) => {
    const el = document.getElementById(id);
    if (el) {
      const props = getProps ? getProps(el) : {};
      createRoot(el).render(<Component {...props} />);
    }
  });
  ```

---

## 13. Passing Data from PHP/Twig to React
- Use `data-*` attributes in your HTML:
  ```twig
  <div id="user-profile-root" data-user='{{ user|json_encode }}' data-jwt="{{ jwt }}"></div>
  ```
- Parse these in the registry:
  ```js
  getProps: (el) => ({
    user: JSON.parse(el.dataset.user),
    jwt: el.dataset.jwt,
  })
  ```

---

## 14. Building and Serving
- **Development:** `npm run dev` (Vite dev server)
- **Production:** `npm run build` (outputs to `public/assets/build/app.js`)
- **PHP Server:** `php -S localhost:8000` (from project root)

---

## 15. Advanced Features & Best Practices

### Security & Authentication
- JWT is only exposed to React if authenticated.
- All sensitive logic (JWT, session, API) is handled server-side.
- Use HTTPS and secure headers (see `.htaccess`).

### API Integration & Caching
- Use `ApiClient.php` for API calls (with Guzzle, JWT, and caching).
- Use Symfony Cache (APCu/file) for performance.

### Multi-language Support
- Add new languages via config and templates.
- Use translation files and Twig blocks for i18n.

### Custom Routing
- Use `Router.php` for custom routes and dynamic parameters.
- Fallback to template-based routing for simple pages.

### Extensibility & Maintainability
- Clean separation of backend, pages, templates, and frontend components.
- Standardized project initialization and folder structure.
- Easy for both PHP and React developers to collaborate.

---

## 16. Example Q&A for AI Assistant

**Q: How do I create a new page with a React component?**
> 1. Create `pages/about.twig` and extend `default.twig`.
> 2. Add `<div id="hello-root"></div>` where you want the component.
> 3. Make sure `Hello` is registered in `registry.js`.
> 4. Run `npm run build`.
> 5. Visit `/about` in your browser.

**Q: How do I pass data from PHP to React?**
> Use `data-*` attributes in your HTML and parse them in the registry.

**Q: How do I add a new layout?**
> Create a new Twig file in `templates/`, use blocks, and extend it in your pages.

**Q: How do I register a new React component?**
> Import it in `registry.js`, add an entry with the DOM id and a prop parser.

**Q: How do I build and serve the project?**
> Use `npm run build` for assets and `php -S localhost:8000` for the PHP server.

**Q: How do I add API integration or caching?**
> Use `ApiClient.php` for API calls and Symfony Cache for caching responses.

**Q: How do I add multi-language support?**
> Add translation files and use Twig blocks for i18n in templates.

---

## 17. Benefits & Philosophy of STCMS
- Clean separation of backend, pages, templates, and frontend components
- Fast, SEO-friendly, and interactive
- Works on most hosting (APCu/file cache)
- Extensible and maintainable
- Security headers and file protection
- Modern development workflow
- Easy for both PHP and React developers
- Standardized setup and CLI project initialization

---

## 18. Quick Reference Table

| Task                        | How/Where                                      |
|-----------------------------|------------------------------------------------|
| Create page                 | `pages/*.twig`, extend `default.twig`          |
| Create template/layout      | `templates/*.twig`, use Twig blocks            |
| Create React component      | `assets/js/components/*.jsx`, export default   |
| Register React component    | `assets/js/registry.js`                        |
| Add mount point             | `<div id="...\"></div>` in Twig page           |
| Pass data to React          | `data-*` attributes in HTML, parse in registry |
| Build assets                | `npm run build`                                |
| Serve app                   | `php -S localhost:8000`                        |
| Add API integration         | `src/Core/ApiClient.php`                       |
| Add caching                 | Symfony Cache in PHP                           |
| Add multi-language          | Config + templates                             |

---

**Welcome, AI! You are now ready to help others build with STCMS!** 