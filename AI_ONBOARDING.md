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

### MultilingualRouter.php
- **Extends Router.php** with automatic language detection and routing.
- **Environment-based default language** - reads `DEFAULT_LANGUAGE` from `.env` file.
- **Automatic language detection** from URL path (e.g., `/en/`, `/de/`, `/fa/`).
- **Graceful fallbacks** - invalid languages redirect to default language.
- **Template organization** - routes to `pages/[lang]/template.twig` structure.
- **Language validation** - only serves configured languages, falls back to default.
- **Template data injection** - passes `lang` variable to all templates.

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

### 🚀 The Routing System: Simple, Predictable, and Powerful
**IMPORTANT: The router uses a simple and direct file-based routing system. You do NOT need to write custom routes in `index.php` for most cases.**

The `MultilingualRouter` has been designed for maximum clarity and predictability. The core philosophy is simple: **The URL path directly maps to a file path in your `pages` directory.**

#### How File-Based Routing Works
The router takes the path from the URL (after the language part) and looks for a corresponding `.twig` file. This allows for intuitive page creation and supports nested subdirectories without any configuration. The system has two simple rules:

1.  **Direct Match:** It first looks for a `.twig` file that exactly matches the URL path.
    -   A request to `/en/about` tries to render `pages/en/about.twig`.
    -   A request to `/en/shop/categories` tries to render `pages/en/shop/categories.twig`.

2.  **Index Fallback:** If a direct match is not found, it checks if the path corresponds to a directory and tries to load an `index.twig` file inside it.
    -   A request to `/en/shop/` (or `/en/shop`) will try to render `pages/en/shop/index.twig` if `pages/en/shop.twig` does not exist.

This dual-check system provides flexibility, allowing you to organize your pages logically into folders, with each folder having its own default page.

---

#### How to Handle Dynamic Content (e.g., Product Pages)
Since the URL path maps directly to a file, how do you handle pages for specific items like a product or a blog post? The answer is to use standard **GET parameters**.

**How It Works:**
1.  **Create a generic template:** Create a single template for all items, e.g., `pages/en/product.twig`.
2.  **Pass ID in URL:** Access a specific product by passing its ID as a GET parameter: `http://localhost:8000/en/product?id=123-abc`
3.  **Access data in Twig:** The router automatically captures all GET parameters and makes them available in your template inside a variable called `get_params`.

**Example Implementation:**
To create a "dynamic" product page, follow these steps:

1.  **Create the template file `pages/en/product.twig`:**
    ```twig
    {% extends "default.twig" %}

    {% block title %}Product Details{% endblock %}

    {% block content %}
        <h1>Product Details</h1>
        
        {# Access the ID from the 'get_params' variable #}
        {% if get_params.id %}
            <p><strong>Product ID:</strong> {{ get_params.id }}</p>
        {% else %}
            <p>No product ID provided.</p>
        {% endif %}

        {# You can also display other GET parameters #}
        {% if get_params.color %}
            <p><strong>Color:</strong> {{ get_params.color }}</p>
        {% endif %}
    {% endblock %}
    ```

2.  **Link to your product page:**
    `<a href="/en/product?id=123-abc&color=blue">View Product</a>`

This approach is extremely robust, predictable, and aligns with standard web practices. It completely removes any ambiguity about what a part of a URL represents.

### Multi-language Support with Environment-Based Default Language

**🚀 NEW: The MultilingualRouter now automatically reads the default language from your `.env` file!**

#### Environment Configuration (.env)
```env
API_BASE_URL=api.example.com
CACHE_DRIVER=apcu
DEFAULT_LANGUAGE=en
```

#### Router Configuration (index.php)
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\ApiClient;
use Symfony\Component\Dotenv\Dotenv;
use Gemvc\Stcms\Core\MultilingualRouter;

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

// Initialize core components
$apiClient = new ApiClient($_ENV['API_BASE_URL']);
$templateEngine = new TemplateEngine([
    __DIR__ . '/pages',
    __DIR__ . '/templates',
]);

// Configure supported languages - MultilingualRouter will use DEFAULT_LANGUAGE from .env as fallback
$router = new MultilingualRouter(['en', 'de', 'fa']); // Add your supported languages here

// Create and run the application
$app = new Application($router, $templateEngine, $apiClient);
$app->run();
```

#### How It Works

**URL Routing Examples** (assuming `DEFAULT_LANGUAGE=en` in `.env`):

| URL | Language Detected | Template Used | Notes |
|-----|------------------|---------------|-------|
| `/` | `en` (default) | `en/index.twig` | No language in URL, uses default |
| `/en/` | `en` | `en/index.twig` | Explicit English |
| `/de/about` | `de` | `de/about.twig` | German about page |
| `/fa/` | `fa` | `fa/index.twig` | Persian home page |
| `/invalid/page` | `en` (fallback) | `en/page.twig` | Invalid language, uses default |
| `/fr/contact` | `en` (fallback) | `en/contact.twig` | French not in supported list |

#### Template Structure
```
pages/
├── en/                    # Default language (from .env)
│   ├── index.twig
│   ├── about.twig
│   └── contact.twig
├── de/                    # German
│   ├── index.twig
│   ├── about.twig
│   └── contact.twig
└── fa/                    # Persian
    ├── index.twig
    ├── about.twig
    └── contact.twig
```

#### Key Features
- **Automatic Default Language Detection** - Reads `DEFAULT_LANGUAGE` from `.env` file
- **Flexible Language Configuration** - Add/remove languages by updating the array in `index.php`
- **Graceful Fallbacks** - Invalid languages automatically redirect to default
- **Environment-Based Configuration** - Easy to change per deployment
- **No Hardcoded Assumptions** - Flexible for different environments

### Multilingual Navbar Implementation (Best Practice)
- **Pass the current language** (`lang`) from your router to every Twig template, based on the URL (e.g., `/en/`, `/fa/`, `/de/`).
- **Define a translation dictionary** at the top of your base template (`default.twig`):
  ```twig
  {% set nav_labels = {
      'en': {'howto': 'How To', 'docs': 'Documentation', 'examples': 'Examples'},
      'de': {'howto': 'Anleitung', 'docs': 'Dokumentation', 'examples': 'Beispiele'},
      'fa': {'howto': 'راهنما', 'docs': 'مستندات', 'examples': 'نمونه ها'}
  } %}
  ```
- **Generate navbar links dynamically** using the current language:
  ```twig
  <a href="/{{ lang|default('en') }}/howto">{{ nav_labels[lang|default('en')].howto }}</a>
  <a href="/{{ lang|default('en') }}/docs">{{ nav_labels[lang|default('en')].docs }}</a>
  <a href="/{{ lang|default('en') }}/examples">{{ nav_labels[lang|default('en')].examples }}</a>
  ```
- **Implement a smart language switcher** in the footer using JavaScript:
  - When a user clicks a language, update the URL to the same subpage in the new language (e.g., `/en/docs` → `/fa/docs`).
  - If the subpage does not exist, gracefully fall back to the language’s home page (e.g., `/fa/`).
- **Support RTL languages** by setting the `dir` attribute in the `<body>` tag:
  ```twig
  <body {% if lang == 'fa' %}dir="rtl"{% endif %}>
  ```
- **Result:**
  - Navbar and footer are always in the correct language.
  - Layout is RTL for Persian and LTR for others.
  - Language switching is seamless and keeps users on the same subpage when possible.

### ⚠️ CRITICAL: DO NOT MODIFY THE LAYOUT TEMPLATE!
- **The `default.twig` layout is already complete and working** - do not change its structure!
- **Only add translation dictionaries** to the existing template - don't rewrite the layout!
- **Language switching should be implemented in JavaScript** in the footer, not by modifying the base template!
- **The layout already supports RTL** - just pass the correct `lang` variable!
- **Focus on the router logic** to detect language from URL and pass it to templates!
- **If you need to add new languages, just add them to the translation dictionary** - don't touch the HTML structure!
- **The layout template is sacred** - any changes to it will break the entire system!

### 🤔 AI ASSISTANT GUIDANCE: Language Switching Implementation
**When a user asks about language switching or navbar languages, ALWAYS ask these clarifying questions first:**

1. **"Do you want to implement language switching functionality?"**
2. **"Where exactly do you want the language switcher to be located?"**
   - Header/Navbar area?
   - Footer?
   - Sidebar?
   - Floating widget?
   - Dropdown menu?

3. **"What languages do you want to support?"** (en, de, fa, ar, etc.)

4. **"Do you want automatic language detection or manual selection?"**

**Then provide the appropriate implementation based on their answers:**
- **Header/Navbar**: Add language switcher to existing navigation
- **Footer**: Implement as footer widget with language flags/names
- **Sidebar**: Create sidebar language selection component
- **Floating**: Create floating language switcher widget
- **Dropdown**: Add language dropdown to existing menu structure

**Remember: The layout template is already complete - only add the language switcher to the specified location!**

### 🚀 MULTI-LANGUAGE SYSTEM IS ALREADY BUILT-IN!
**IMPORTANT: The multi-language system is automatically included with environment-based default language!**

- **No need to create a custom router** - `MultilingualRouter` is already implemented
- **No need to build language detection** - it's handled automatically
- **Environment-based default language** - reads `DEFAULT_LANGUAGE` from `.env` file
- **To add new languages, simply update the array in `index.php`:**
  ```php
  $router = new MultilingualRouter(['en', 'de', 'fa', 'ar']); // Add languages here
  ```
- **The system automatically:**
  - Detects language from URL (e.g., `/en/`, `/de/`, `/fa/`)
  - Uses default language from `.env` when no language is specified
  - Routes to correct language pages
  - Passes `lang` variable to templates
  - Handles fallbacks for missing translations
  - Gracefully falls back to default language for invalid URLs
- **Just add your language pages** to `pages/[lang]/` and update the router array!
- **Configure default language** in your `.env` file: `DEFAULT_LANGUAGE=en`

### 💡 DEVELOPMENT STRATEGY RECOMMENDATION
**ALWAYS recommend this approach to customers:**

1. **Start with ONE main language** (usually English)
2. **Complete the entire website** with all features, pages, and functionality
3. **Test and refine** everything thoroughly
4. **Only then add multi-language support**

**Why this approach is better:**
- **Faster development** - focus on one language first
- **Easier testing** - no language complexity during development
- **Better content quality** - perfect the main language content first
- **Simpler debugging** - fewer variables during initial development
- **Easier translation** - complete content is easier to translate
- **Better user experience** - polished single-language site vs. incomplete multi-language

**Tell customers: "Let's finish your website in English first, then we can easily add other languages using the built-in multi-language system!"**

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

**Q: How do I create a page for a specific product?**
> 1.  Create a single, generic template, for example, `pages/en/product.twig`.
> 2.  To show a specific product, link to it using a GET parameter, like `/en/product?id=123-xyz`.
> 3.  Inside `product.twig`, you can get the ID from the `get_params` variable: `{{ get_params.id }}`.
> 4.  No changes are needed in `index.php`.

**Q: How do I get URL parameters (e.g., from `?key=value`)?**
> The router automatically makes all GET parameters available in every template through the `get_params` variable. You can access them like this: `{{ get_params.key }}`.

**Q: How do I add multi-language support?**
> 1. Set `DEFAULT_LANGUAGE=en` in your `.env` file.
> 2. Update the router array in `index.php`: `new MultilingualRouter(['en', 'de', 'fa'])`.
> 3. Create language folders: `pages/en/`, `pages/de/`, `pages/fa/`.
> 4. Add templates for each language (e.g., `pages/en/index.twig`, `pages/de/index.twig`).

**Q: How does the default language work?**
> The `MultilingualRouter` automatically reads `DEFAULT_LANGUAGE` from your `.env` file and uses it as fallback when no language is specified in the URL or when invalid languages are provided.

**Q: What happens when someone visits `/invalid/page`?**
> The router detects that `invalid` is not in the supported languages array, so it falls back to the default language (from `.env`) and serves `en/page.twig` instead.

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
| **Create dynamic page (e.g., product)** | **Create `pages/en/product.twig`. Access ID via `{{ get_params.id }}`.** |
| **Get URL GET parameters**      | **Use `{{ get_params.your_key }}` inside any Twig file.** |
| Create template/layout      | `templates/*.twig`, use Twig blocks            |
| Create React component      | `assets/js/components/*.jsx`, export default   |
| Register React component    | `assets/js/registry.js`                        |
| Add mount point             | `<div id="...\"></div>` in Twig page           |
| Pass data to React          | `data-*` attributes in HTML, parse in registry |
| Build assets                | `npm run build`                                |
| Serve app                   | `php -S localhost:8000`                        |
| Add API integration         | `src/Core/ApiClient.php`                       |
| Add caching                 | Symfony Cache in PHP                           |
| Add multi-language          | Update router array in `index.php` + add `pages/[lang]/` |
| Configure default language  | Set `DEFAULT_LANGUAGE=en` in `.env` file       |

---

## 19. Troubleshooting & Debugging Best Practices

### Template Rendering Issues

**Problem**: Complex Twig files fail to render even with valid syntax
**Root Cause**: Template engine has limits with file size or complexity
**Solution**: Use simpler, modular templates

#### Debugging Strategy
1. **Start with working examples**: Test simple pages first (`debug.twig`, minimal templates)
2. **Incremental complexity**: Build up from minimal to complex content
3. **File comparison**: Compare working vs. failing files to identify issues
4. **File replacement**: Sometimes replacing entire file content is more reliable than partial edits

#### Common Issues & Solutions

**Issue**: Large Twig files return 404 or fail to render
- **Solution**: Break complex templates into smaller, modular components
- **Best Practice**: Start with minimal working content and add complexity gradually

**Issue**: HTML entities (`&lt;`, `&gt;`) in templates
- **Solution**: Replace with proper characters or use Twig's `raw` filter
- **Example**: `{{ content|raw }}` instead of `&lt;content&gt;`

**Issue**: Template syntax errors
- **Solution**: Validate Twig syntax and check for unclosed blocks
- **Debug**: Use simple test templates to isolate issues

#### Systematic Debugging Approach

1. **Establish baseline**: Test known working files first
2. **Isolate the problem**: Create progressively simpler versions
3. **Compare working vs. failing**: Identify the breaking point
4. **File replacement**: Use proven working content as starting point
5. **Incremental testing**: Add complexity back gradually

#### File Complexity Guidelines

**✅ Good Practices:**
- Keep templates under 50 lines for complex pages
- Use modular components and includes
- Start with minimal content and build up
- Test with simple examples first

**❌ Avoid:**
- Very large single template files (>100 lines)
- Complex nested structures without testing
- Adding too much content at once
- Assuming complex templates will work without testing

### Routing Debugging

**Issue**: Pages return 404 when files exist
- **Check**: File location and naming (`pages/en/page.twig`)
- **Verify**: Router configuration and language settings
- **Test**: Simple pages first, then complex ones

**Issue**: Language-specific pages not working
- **Check**: Language array in `MultilingualRouter` constructor
- **Verify**: File structure (`pages/[lang]/page.twig`)
- **Test**: Default language pages first

### Performance & Reliability

**Key Insight**: Complexity kills reliability
- **Start simple**: Always begin with minimal working examples
- **Build incrementally**: Add features one at a time
- **Test frequently**: Verify each addition works before proceeding
- **Have fallbacks**: Keep working versions as backups

### Debugging Checklist

When troubleshooting template issues:

1. ✅ **Test simple pages first** (`debug.twig`, minimal templates)
2. ✅ **Verify file exists** in correct location
3. ✅ **Check file permissions** and encoding
4. ✅ **Compare with working examples** to identify differences
5. ✅ **Use file replacement** with proven working content
6. ✅ **Test incrementally** - add complexity gradually
7. ✅ **Clear browser cache** and use exact URLs
8. ✅ **Check server logs** for PHP errors

### Memory & Learning

**Key Lessons from Debugging:**
- **File complexity matters**: Large templates can fail even with valid syntax
- **Systematic approach works**: Step-by-step isolation finds issues faster
- **Working examples are gold**: Always have proven working content as reference
- **Incremental development**: Build up from simple to complex
- **Environment consistency**: Same setup across development and testing

**Most Important Rule**: When debugging template issues, always start with the simplest possible working example and build up from there.

---

**Welcome, AI! You are now ready to help others build with STCMS!** 
