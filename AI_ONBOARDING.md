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
├── components/
│   └── components.twig            # Reusable Twig components (macros)
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

## 4. Entry Point and Web Server Configuration: .htaccess & index.php

STCMS uses a "public directory" structure, which is a modern security best practice. All application logic, templates, and source files reside outside the web-accessible `public/` folder, and only `public/index.php` is exposed to the web. This is managed by two `.htaccess` files.

### .htaccess in the Project Root
This file has two critical jobs:
1.  **Security:** It blocks all direct web access to sensitive files (like `.env`) and crucial application directories (like `src`, `vendor`, `templates`, etc.). This prevents anyone from accessing your source code or configuration from a browser.
2.  **Routing:** It forwards every incoming request to the `public/` directory, where the application's front-controller (`index.php`) resides.

**Example: `/.htaccess`**
```apache
# --- 1. Security: Block all files and directories starting with a dot ---
# This is a strong general rule that protects .git, .env, etc.
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule "(^|/)\." - [F]
</IfModule>

# --- 2. Security: Explicitly forbid access to sensitive directories ---
# Any direct web request to these folders will be blocked.
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(src|vendor|templates|pages|components|assets)/?$ - [F,NC,L]
</IfModule>

# --- 3. Routing: Pass all other requests to the public folder ---
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### .htaccess in the `public/` Directory
This file acts as the application's front-controller. It receives all requests forwarded from the root `.htaccess` and routes them to `index.php` if they don't correspond to a real, physical file (like a CSS file or an image). This is what enables the clean, file-based routing system.

**Example: `/public/.htaccess`**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On

    # Send requests to front controller if they don't point to a real file or directory
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### index.php
The `public/index.php` file bootstraps the entire application. It loads Composer's autoloader, environment variables from `.env`, and initializes all the core STCMS components like the Router, Template Engine, and ApiClient before running the application.

**Best Practices:**
- This setup provides a robust, secure, and flexible foundation for any modern PHP application.
- You **do not** need to edit these files manually. The `stcms init` command creates them for you with the correct, optimized configuration.

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

## 8. CLI Commands

STCMS comes with a powerful command-line interface (CLI) to help you manage and scaffold your project. You can run these commands from the root of your project.

### `init`
This is the first command you should run. It sets up the entire project structure, including directories, `.env` file, `.htaccess` files, and starter templates.
```bash
php vendor/bin/stcms init
```

### `install:help`
This command installs the full documentation and multi-language example pages, which are not included in the minimal `init` setup.
```bash
# Install English docs
php vendor/bin/stcms install:help en

# Install German and Farsi docs
php vendor/bin/stcms install:help de fa
```

### `cache:clear`
In a production environment (`APP_ENV=production`), Twig templates are compiled and cached for performance. If you make changes to a `.twig` file, you must clear the cache to see your changes. This command safely deletes the template cache.
```bash
php vendor/bin/stcms cache:clear
```

### `deploy:init`
This command helps you set up a deployment workflow. It creates a template file for a CI/CD service like GitHub Actions.
```bash
# Initialize a deployment workflow for GitHub Actions and FTP
php vendor/bin/stcms deploy:init github-actions-ftp
```
If you run the command without specifying a type, it will interactively ask you to choose from the available templates. See the "Deployment" section for more details.

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

## 12. Creating Reusable Functional Components with Twig Macros

STCMS promotes a component-based architecture not just for React, but for server-rendered UI as well. Using Twig's `macro` feature, you can create powerful, reusable "functional components" that ensure consistency and speed up development. This is the recommended approach for any UI element that is used more than once.

**What are they?**
Macros are like functions in Twig. You define a piece of reusable HTML with parameters, and then you can call it anywhere you need it.

**How to create them:**
1.  **Create a central file for your components:** The best practice is to create files in the root `components/` directory. The `init` command provides a starter file for you at `components/components.twig`.
2.  **Define your macros:** Inside this file, define your UI components using `{% macro ... %}`.

    *Example: `components/components.twig`*
    ```twig
    {#
      A flexible button component.
      @param string text The button's text.
      @param string url  The URL for the button's href.
      @param string variant ('primary' or 'secondary')
    #}
    {% macro button(text, url, variant='primary') %}
        {% set colors = {
            'primary': 'bg-red-600 hover:bg-red-700',
            'secondary': 'bg-gray-700 hover:bg-gray-800'
        } %}
        <a href="{{ url }}" class="inline-block text-white font-bold py-2 px-4 rounded transition-transform hover:scale-105 {{ colors[variant] }}">
            {{ text }}
        </a>
    {% endmacro %}
    ```

**How to use them:**
1.  **Import the macros** at the top of any page where you want to use a component.
2.  **Call the macro** like a function, passing in the required parameters.

    *Example: Using the button in `pages/en/index.twig`*
    ```twig
    {% extends "default.twig" %}
    {% import "components.twig" as components %} {# 1. Import the components file #}
    
    {% block content %}
      <h1 class="text-3xl">Welcome!</h1>
      
      {# 2. Use the component #}
      <div class="mt-4 flex gap-4">
          {{ components.button(text='Learn More', url='/en/how', variant='primary') }}
          {{ components.button(text='Contact Us', url='/en/contact', variant='secondary') }}
      </div>
    {% endblock %}
    ```
This approach is strongly recommended for any UI element that appears in multiple places, such as buttons, cards, alerts, or form inputs. It keeps your code DRY (Don't Repeat Yourself) and makes site-wide styling changes trivial.

---

## 13. Using PHP Logic & Data in Twig Templates
A key part of building a dynamic website is passing data from your PHP backend to your Twig frontend. The most important rule is to **separate your logic from your presentation**. PHP should handle the logic (fetching from a database, calling an API, running calculations), and Twig should handle the display.

There are two primary ways to make your PHP logic and data available to Twig.

### Method 1: Passing Page-Specific Data from the Router
This is the standard approach for data that is needed by a specific page or a small group of pages. The logic should be placed in the `MultilingualRouter.php` file, which is responsible for preparing and rendering templates.

**How it works:**
The `renderMultilingualTemplate` method in `src/Core/MultilingualRouter.php` prepares a `$data` array that gets passed to every Twig template. You can add your own page-specific data to this array.

**Example: Passing a list of products to a "Shop" page:**
1.  Open `src/Core/MultilingualRouter.php`.
2.  Navigate to the `renderMultilingualTemplate` method.
3.  Just before the `try...catch` block that calls `$templateEngine->render()`, add your logic.

```php
// ... inside renderMultilingualTemplate method
// Prepare base template data, always available
$data = [
    // ... existing data (user, jwt, lang, etc.)
];

// --- ADD YOUR PAGE-SPECIFIC LOGIC HERE ---
// Example: if the current page is the 'shop' page, fetch product data.
if ($subpath === 'shop') {
    // In a real app, you would fetch this from an API or database.
    $products = [
        ['id' => 1, 'name' => 'Awesome Gadget', 'price' => '29.99'],
        ['id' => 2, 'name' => 'Cool Gizmo', 'price' => '49.99'],
        ['id' => 3, 'name' => 'Shiny Thing', 'price' => '19.99'],
    ];
    $data['products'] = $products;
}

// The $data array is now passed to the renderer below.
try {
    // ... existing render calls
```

4.  **Use the data in your Twig file** (`pages/en/shop.twig`):
```twig
{% extends "default.twig" %}
{% block content %}
    <h1 class="text-2xl">Our Products</h1>
    <ul>
        {% for product in products %}
            <li>{{ product.name }} - ${{ product.price }}</li>
        {% else %}
            <p>No products found.</p>
        {% endfor %}
    </ul>
{% endblock %}
```

### Method 2: Creating Reusable Global Functions
This is the best approach for functions that you want to be available in *any* Twig template, such as utility functions for formatting dates, getting global configuration, or performing a common calculation.

**How it works:**
You can register your own custom PHP functions with the Twig environment in the `TemplateEngine.php` file.

**Example: Creating a `format_price` function:**
1.  Open `src/Core/TemplateEngine.php`.
2.  Navigate to the `addCustomFunctions` method.
3.  Add a new `TwigFunction`. You can define the function inline as a closure or point to any existing static or regular function.

```php
// ... inside addCustomFunctions method
// Add is_authenticated function
$this->twig->addFunction(new \Twig\TwigFunction('is_authenticated', function () {
    return isset($_SESSION['jwt']);
}));

// --- ADD YOUR NEW FUNCTION HERE ---
$this->twig->addFunction(new \Twig\TwigFunction('format_price', function (string $price, string $currency = '$') {
    return $currency . number_format((float)$price, 2);
}));
```
4.  **Use the function anywhere in your Twig templates:**
```twig
<p>Price: {{ format_price(product.price) }}</p> {# Output: $49.99 #}
<p>Price in Euros: {{ format_price(product.price, '€') }}</p> {# Output: €49.99 #}
```

This makes your custom PHP functions available globally as if they were built-in Twig functions.

---

## 14. Registry & Auto-Mounting System
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

## 15. Passing Data from PHP/Twig to React
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

## 16. Building and Serving
- **Development:** `npm run dev` (Vite dev server)
- **Production:** `npm run build` (outputs to `public/assets/build/app.js`)
- **PHP Server:** `php -S localhost:8000 -t public` (from project root)

---

## 17. Advanced Features & Best Practices

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

### Multi-language Support with Automatic Language Detection

**🚀 NEW: STCMS now automatically detects supported languages from your `pages` directory!**

You no longer need to manually edit `index.php` to add new languages. The system is designed to be zero-configuration.

#### How It Works:
1.  **Create a language folder:** Simply add a new directory inside your `pages` folder (e.g., `pages/fa/`).
2.  **The router automatically finds it:** On the next page load, the router will automatically recognize "fa" as a supported language.
3.  **It just works!**

This approach removes the need to maintain a manual list of languages and ensures your configuration is always in sync with your project's structure.

#### Environment Configuration (.env)
Your `.env` file still controls the *default* language.
```env
API_BASE_URL=api.example.com
CACHE_DRIVER=apcu
DEFAULT_LANGUAGE=en
```

**Graceful Fallback:** If the `DEFAULT_LANGUAGE` set in your `.env` file does not correspond to an existing folder in `pages/`, the system will not crash. Instead, it will automatically fall back to using the **first available language** it finds, ensuring your site always remains online.

#### Router Configuration (index.php) - No Action Needed!
The setup in `index.php` is now fully automated.

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\ApiClient;
use Symfony\Component\Dotenv\Dotenv;
use Gemvc\Stcms\Core\MultilingualRouter;

// Load environment variables and set defaults
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');
if (!isset($_ENV['DEFAULT_LANGUAGE'])) {
    $_ENV['DEFAULT_LANGUAGE'] = 'en';
}

// Initialize core components
$apiClient = new ApiClient($_ENV['API_BASE_URL']);
$templateEngine = new TemplateEngine([
    __DIR__ . '/pages',
    __DIR__ . '/templates',
]);

// Dynamically find supported languages by scanning the 'pages' directory
$supportedLanguages = [];
$pagesDir = __DIR__ . '/pages';
if (is_dir($pagesDir)) {
    $languageDirs = glob($pagesDir . '/*', GLOB_ONLYDIR);
    foreach ($languageDirs as $langDir) {
        $supportedLanguages[] = basename($langDir);
    }
}

// If no languages are found, default to 'en'
if (empty($supportedLanguages)) {
    $supportedLanguages = ['en'];
}

// Validate that the DEFAULT_LANGUAGE from .env exists.
// If not, fall back to the first available language to prevent errors.
if (!in_array($_ENV['DEFAULT_LANGUAGE'], $supportedLanguages)) {
    $_ENV['DEFAULT_LANGUAGE'] = $supportedLanguages[0];
}

$router = new MultilingualRouter($supportedLanguages);

// Create and run the application
$app = new Application($router, $templateEngine, $apiClient);
$app->run();
```
You don't need to touch this code. Simply manage your language folders, and the system handles the rest.

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

## 18. Deployment with CI/CD (GitHub Actions)

Deploying a modern web application involves more than just copying files. You need to install dependencies, build frontend assets, and manage sensitive environment variables. STCMS helps you automate this process using the `deploy:init` command.

### The `.env` File Problem
Your `.env` file contains sensitive information (like API keys and database passwords) and is correctly listed in `.gitignore`. This means it **will not** be committed to your Git repository. When a CI/CD runner like GitHub Actions checks out your code, the `.env` file will be missing, and your application will fail.

### The Solution: GitHub Secrets and Workflow Scaffolding
The standard and most secure solution is to store your environment variables as **Secrets** in your GitHub repository and use a workflow to create the `.env` file on the server during deployment. The `deploy:init` command automates the creation of this workflow file.

**Step-by-Step Guide to Automated Deployment:**

1.  **Scaffold the Workflow File:**
    Run the `deploy:init` command in your project root. For deploying to a standard FTP server, use the `github-actions-ftp` type.
    ```bash
    php vendor/bin/stcms deploy:init github-actions-ftp
    ```
    This will create a new file at `.github/workflows/deploy.yml`.

2.  **Add Secrets to Your GitHub Repository:**
    Navigate to your repository on GitHub, then go to `Settings` > `Secrets and variables` > `Actions`. Click `New repository secret` and add the following secrets. These must match the values needed by your application and the `deploy.yml` workflow.
    -   `FTP_SERVER`: Your FTP host address (e.g., `ftp.yourdomain.com`).
    -   `FTP_USERNAME`: Your FTP username.
    -   `FTP_PASSWORD`: Your FTP password.
    -   `API_BASE_URL`: The base URL for your API.
    -   `DEFAULT_LANGUAGE`: The default language for your site (e.g., `en`).
    -   `VITE_BASE_URL`: The base URL for Vite, often the same as your site's URL.

3.  **Commit and Push:**
    Commit the new `.github/workflows/deploy.yml` file and push it to your `main` branch.
    ```bash
    git add .github/workflows/deploy.yml
    git commit -m "feat: Add deployment workflow"
    git push origin main
    ```

That's it! The next time you push a change to your `main` branch, GitHub Actions will automatically trigger, build your project, create the `.env` file from your secrets, and deploy everything to your FTP server.

---

## 19. Example Q&A for AI Assistant

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
> Use `npm run build` for assets and `php -S localhost:8000 -t public` for the PHP server.

**Q: How do I create a page for a specific product?**
> 1.  Create a single, generic template, for example, `pages/en/product.twig`.
> 2.  To show a specific product, link to it using a GET parameter, like `/en/product?id=123-xyz`.
> 3.  Inside `product.twig`, you can get the ID from the `get_params` variable: `{{ get_params.id }}`.
> 4.  No changes are needed in `index.php`.

**Q: How do I get URL parameters (e.g., from `?key=value`)?**
> The router automatically makes all GET parameters available in every template through the `get_params` variable. You can access them like this: `{{ get_params.key }}`.

**Q: How do I add multi-language support?**
> 1.  First, ensure you have the desired language pages. You can add them manually or by using the `install:help` command. For example, to add German documentation, run: `php vendor/bin/stcms install:help de`.
> 2.  That's it! The router automatically detects the new `pages/de` directory and adds 'de' to the list of supported languages.
> 3.  (Optional) If you want German to be the default language, change `DEFAULT_LANGUAGE=de` in your `.env` file.

**Q: How does the default language work?**
> The `MultilingualRouter` reads `DEFAULT_LANGUAGE` from your `.env` file. If that language isn't available as a folder in `/pages`, it intelligently falls back to the first available language it finds, preventing errors.

**Q: What happens when someone visits `/invalid/page`?**
> The router detects that `invalid` is not in the list of automatically detected languages, so it falls back to the default language and serves `pages/[default_lang]/page.twig` instead.

**Q: How do I get data from my database onto a page?**
> For page-specific data, you should add your database fetching logic to `src/Core/MultilingualRouter.php` in the `renderMultilingualTemplate` method. Check the current path, fetch your data, and add it to the `$data` array before it's passed to the renderer.

**Q: How can I create a global function, like `get_setting('site_name')`, to use in any template?**
> You should register it as a custom function in `src/Core/TemplateEngine.php`. In the `addCustomFunctions` method, add a new `TwigFunction` that points to your PHP `get_setting` logic. Then you can call `{{ get_setting('site_name') }}` in any `.twig` file.

---

## 20. Benefits & Philosophy of STCMS
- Clean separation of backend, pages, templates, and frontend components
- Fast, SEO-friendly, and interactive
- Works on most hosting (APCu/file cache)
- Extensible and maintainable
- Security headers and file protection
- Modern development workflow
- Easy for both PHP and React developers
- Standardized setup and CLI project initialization

---

## 21. Quick Reference Table

| Task | How/Where |
|---|---|
| Create page | `pages/[lang]/*.twig`, extend `default.twig` |
| **Pass page-specific PHP data** | **Add logic to `renderMultilingualTemplate` in `MultilingualRouter.php`.** |
| **Create global PHP function for Twig** | **Add a `TwigFunction` in `addCustomFunctions` in `TemplateEngine.php`.** |
| Create reusable component | Define a `{% macro ... %}` in `components/components.twig`. |
| Use reusable component | `{% import "components.twig" as c %}` then `{{ c.my_macro(...) }}`. |
| Create dynamic page (e.g., product) | Create `pages/en/product.twig`. Access ID via `{{ get_params.id }}`. |
| **Get URL GET parameters** | **Use `{{ get_params.your_key }}` inside any Twig file.** |
| Create template/layout | `templates/*.twig`, use Twig blocks |
| Create React component | `assets/js/components/*.jsx`, export default |
| Register React component | `assets/js/registry.js` |
| Add mount point | `<div id="...\"></div>` in Twig page |
| Pass data to React | `data-*` attributes in HTML, parse in registry |
| Build assets | `npm run build` |
| Serve app | `php -S localhost:8000 -t public` |
| **Clear application cache** | **`php vendor/bin/stcms cache:clear`** |
| **Setup deployment workflow** | **`php vendor/bin/stcms deploy:init [type]`** |
| **Add new language support** | **Create a folder `pages/[lang]/` or run `install:help [lang]`. It's detected automatically.** |
| Configure default language | Set `DEFAULT_LANGUAGE=en` in `.env` |

---

## 22. Appendix: For AI Assistants

This section provides specific instructions and guidelines for AI assistants to ensure they provide accurate and helpful support to STCMS users.

# 🚀 AI Assistant Instructions for STCMS

**Objective:** Your primary goal is to assist users in building websites with the STCMS library. You must provide accurate, efficient, and consistent guidance by strictly following the principles and methods outlined below. STCMS is designed for simplicity and automation; your advice must reflect this philosophy.

---

### ⭐ Golden Rules: Prime Directives for the AI Assistant

These are the most important rules. Do not deviate from them.

1.  **TRUST THE AUTOMATION:** STCMS features zero-configuration, file-based routing and automatic language detection. **NEVER** advise a user to manually edit `index.php` to add routes or languages. The system is smarter than that. The answer is always to create the correct file or folder in the `pages/` directory.

2.  **USE THE CLI:** The project has dedicated CLI commands (`init` and `install:help`). These are the **only** correct ways to start a project or install documentation. Do not suggest manual copying of files.

3.  **MANDATE TAILWIND CSS:** For all styling questions, you **MUST** recommend and use Tailwind CSS utility classes. Do not suggest creating separate `.css` files or using inline `<style>` tags. STCMS is designed around a utility-first CSS workflow. This is not optional advice; it is the standard. [[memory:2399285]]

4.  **TWIG FOR STRUCTURE, REACT FOR INTERACTIVITY:** Clearly separate these concerns. The main page layout, structure, and non-interactive content are handled by Twig. Complex, stateful, or highly interactive elements (like forms with validation or data dashboards) are handled by React components.

5.  **PROMOTE REUSABLE TWIG COMPONENTS:** For common UI elements (buttons, cards, etc.), instruct users to create and use Twig macros in the root `components/` directory (e.g., `components/components.twig`). This promotes consistency and follows the component-based architecture. Do not have them write raw HTML for these elements repeatedly.

6.  **NEVER HACK THE CORE:** Users should never be told to modify files inside the `vendor/gemvc/stcms/src` directory, especially the `Core` classes. The library provides all necessary functionality through its public API (routing, templates, CLI).

---

### 🧠 Core Concepts Explained Simply

#### 1. Routing: The URL is the File Path
This is the simplest, most important concept.
- A request to `/en/about` automatically renders the file `pages/en/about.twig`.
- A request to `/de/shop/products` automatically renders `pages/de/shop/products.twig`.
- If a direct file match isn't found, it looks for an `index.twig` in that directory. So, `/de/shop/` will render `pages/de/shop/index.twig`.
- **Dynamic Content:** For things like a specific product, the URL uses a GET parameter. Example: `/en/product?id=123`. The ID is available in Twig via `{{ get_params.id }}`. **Do not** suggest complex routing patterns like `/product/{id}`.

#### 2. Multi-Language Support: It's Automatic
- **How to add a language:** The user creates a folder in `pages/` (e.g., `pages/fa/`) OR runs `php vendor/bin/stcms install:help fa`.
- The system **automatically** detects the `fa` folder and adds it to the supported languages list. No other steps are needed.
- **Default Language:** The default is set in `.env` via `DEFAULT_LANGUAGE=en`. If this language folder doesn't exist, the system gracefully falls back to the first available language it finds.

#### 3. Templating: Twig + Tailwind CSS
- All pages must `{% extends "default.twig" %}`.
- All styling **MUST** be done with Tailwind CSS utility classes directly in the HTML.

**Example for a User Asking "How do I make a red button?":**
> **Correct Response:** "You can style the button directly in your Twig file using Tailwind's utility classes. Here is an example:"
> ```