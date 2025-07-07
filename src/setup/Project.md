# STCMS - Modern PHP Hybrid CMS Library

## Project Target

A Composer-installable PHP library that provides a modern, component-based frontend for GEMVC (or any API backend), with:
- Hybrid rendering: Twig for server-side templates, React (via Vite) for interactive components
- API integration via Guzzle
- Config via Symfony Dotenv
- Caching via Symfony Cache (APCu/file)
- CLI project initialization
- Easy for both PHP and frontend (React) developers

## Core Requirements

- **Composer package** (install with `composer require gemvc/stcms`)
- **CLI tool** (`vendor/bin/stcms init`) to scaffold new projects
- **Twig** for server-side templates (with template/component structure)
- **Vite** for React component bundling (`/assets/js/components/`)
- **Guzzle** for API calls to GEMVC or any backend
- **Symfony Cache** (APCu if available, fallback to file)
- **Symfony Dotenv** for configuration
- **Multi-language support** (via config and templates)
- **SEO-friendly** (customizable meta per page)
- **Modern, maintainable code structure**

## Project Structure

```
/
├── src/                    # PHP library code (autoloaded)
│   ├── Core/               # Core classes (Application, Router, etc.)
│   │   ├── Application.php # Main application orchestrator
│   │   ├── Router.php      # URL routing and request handling
│   │   ├── Request.php     # HTTP request encapsulation
│   │   ├── Response.php    # HTTP response handling
│   │   ├── TemplateEngine.php # Twig template rendering
│   │   └── ApiClient.php   # HTTP API communication
│   ├── Command/            # CLI commands
│   └── setup/              # Template files for project initialization
├── templates/              # Twig templates (layouts, pages, components)
├── assets/
│   ├── js/
│   │   ├── components/     # React components (JSX)
│   │   └── app.jsx         # Main React entry
│   └── css/                # CSS (optional, Tailwind via CDN or Vite)
├── public/
│   └── assets/js/          # Vite build output
├── .env                    # Config
├── .htaccess               # Apache configuration with security headers
├── index.php               # Main entry point
├── vite.config.js          # Vite config
├── package.json            # Frontend dependencies
├── composer.json           # Composer config
└── bin/stcms               # CLI entry point
```

## Core PHP Classes

### Application.php
Main application orchestrator that:
- Manages session and JWT authentication
- Coordinates Router, TemplateEngine, and ApiClient
- Handles request/response flow
- Provides authentication helpers (`isAuthenticated()`, `getJwt()`)

### Router.php
URL routing and request handling:
- Supports exact route matching and dynamic routes (`/user/{id}`)
- Automatic template rendering based on URL paths
- Route parameter extraction
- Fallback to template-based routing

### Request.php & Response.php
HTTP request/response encapsulation:
- Clean interface for accessing request data
- Support for JSON responses and redirects
- AJAX request detection
- Header management

### TemplateEngine.php
Twig template rendering with custom functions:
- `asset()` - Generate asset URLs
- `route()` - Generate route URLs
- `json_encode()` - JSON encoding helper
- `is_authenticated()` - Authentication check

### ApiClient.php
HTTP API communication using Guzzle:
- GET, POST, PUT, DELETE methods
- JWT authentication support
- Error handling and timeout management
- Authentication helper methods

## How It Works

- **User installs via Composer**
- **Runs CLI to scaffold project** (copies files from `src/setup/`)
- **Twig renders main pages**; React components are mounted where needed
- **API data fetched via Guzzle** (with caching)
- **Frontend devs build React components in `/assets/js/components/`**
- **Vite bundles JS for use in templates**
- **Config and cache are environment-driven**

## Hybrid Rendering Example

- Twig template:
  ```twig
  <div id="user-profile-root" data-user="{{ user|json_encode }}" {% if jwt %}data-jwt="{{ jwt }}"{% endif %}></div>
  <script src="{{ asset('js/app.js') }}"></script>
  ```
- React mounts on `#user-profile-root` and renders the component, using the JWT if present

## Authentication Flow & Security
- **JWT is only exposed to React if the user is authenticated** (JWT is present in PHP session).
- **If not authenticated, no JWT is exposed**—React knows to show login or restrict access.
- **React components use the JWT for API requests** (e.g., via Axios/fetch, in Authorization header).
- **JWT is never generated or verified in the frontend**—all JWT logic is handled by the backend (GEMVC API).
- **Session management and login/logout handled by PHP backend.**
- **Best practice:** Always validate JWTs on the backend for every API request.

## Setup Folder Structure

The `src/setup/` folder contains all template files that get copied during project initialization:

```
src/setup/
├── .gitignore           # Git ignore patterns
├── .htaccess           # Apache configuration with security headers
├── index.php           # Main entry point
├── env.template        # Environment configuration template
├── vite.config.js      # Vite build configuration
├── package.json        # Frontend dependencies
├── templates/
│   ├── example.twig    # Example template
│   └── index.twig      # Beautiful landing page template
└── assets/js/
    ├── app.jsx         # React entry point
    └── components/
        └── UserProfile.jsx # Example React component
```

## Benefits

- ✅ Clean separation of backend, templates, and frontend components
- ✅ Easy for both PHP and React developers
- ✅ Fast, SEO-friendly, and interactive
- ✅ Works on most hosting (APCu/file cache)
- ✅ Extensible and maintainable
- ✅ Standardized project initialization
- ✅ Security headers and file protection
- ✅ Modern development workflow

## Implementation Status

✅ **Completed:**
1. Composer package structure
2. CLI tool for project initialization
3. Core PHP classes (Application, Router, Request, Response, TemplateEngine, ApiClient)
4. Setup folder with template files
5. Twig integration with custom functions
6. Vite configuration for React components
7. Example templates and components
8. Security headers and Apache configuration
9. Comprehensive .gitignore patterns

🔄 **Next Steps:**
1. Add Symfony Cache integration
2. Implement multi-language support
3. Add more CLI commands (cache:clear, etc.)
4. Create comprehensive documentation
5. Add unit tests 