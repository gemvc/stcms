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
│   ├── Core/               # Core classes (Cache, ApiClient, etc.)
│   └── Command/            # CLI commands
├── templates/              # Twig templates (layouts, pages, components)
├── assets/
│   ├── js/
│   │   ├── components/     # React components (JSX)
│   │   └── app.jsx         # Main React entry
│   └── css/                # CSS (optional, Tailwind via CDN or Vite)
├── public/
│   └── assets/js/          # Vite build output
├── .env                    # Config
├── vite.config.js          # Vite config
├── composer.json           # Composer config
└── bin/stcms               # CLI entry point
```

## How It Works

- **User installs via Composer**
- **Runs CLI to scaffold project**
- **Twig renders main pages**; React components are mounted where needed
- **API data fetched via Guzzle** (with caching)
- **Frontend devs build React components in `/assets/js/components/`**
- **Vite bundles JS for use in templates**
- **Config and cache are environment-driven**

## Hybrid Rendering Example

- Twig template:
  ```twig
  <div id="user-profile-root" data-user="{{ user|json_encode }}" {% if jwt %}data-jwt="{{ jwt }}"{% endif %}></div>
  <script src="/assets/js/app.js"></script>
  ```
- React mounts on `#user-profile-root` and renders the component, using the JWT if present

## Authentication Flow & Security
- **JWT is only exposed to React if the user is authenticated** (JWT is present in PHP session).
- **If not authenticated, no JWT is exposed**—React knows to show login or restrict access.
- **React components use the JWT for API requests** (e.g., via Axios/fetch, in Authorization header).
- **JWT is never generated or verified in the frontend**—all JWT logic is handled by the backend (GEMVC API).
- **Session management and login/logout handled by PHP backend.**
- **Best practice:** Always validate JWTs on the backend for every API request.

## Benefits

- ✅ Clean separation of backend, templates, and frontend components
- ✅ Easy for both PHP and React developers
- ✅ Fast, SEO-friendly, and interactive
- ✅ Works on most hosting (APCu/file cache)
- ✅ Extensible and maintainable

## Implementation Steps

1. Create Composer package structure
2. Add CLI tool for project init
3. Set up Twig, Dotenv, Cache, Guzzle
4. Set up Vite for React components
5. Scaffold example templates and components
6. Document usage for both PHP and frontend devs 