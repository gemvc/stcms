# STCMS Static Website CMS multi language multi template Specification

## Project Target

A lightweight, static-like PHP website with clean architecture and multi-language support.

## Core Requirements

- **No database or CMS** (just PHP files)
- **Reusable header, footer, sidebar** (via PHP includes)
- **Multi-language support** (per-language folders, e.g., `/en/`, `/de/`)
- **Simple routing** (via clean URLs, no frameworks)
- **Easy to add new pages** (just add a PHP file in the right folder)
- **SEO-friendly** (customizable title, description, keywords per page)
- **Modern, maintainable code structure**
- **Optional:** Simple file-based caching for performance (no Memcache required)

## Project Structure

```
/
├── index.php                    # Main router/entry point
├── code/                        # PHP base classes
│   ├── Router.php
│   ├── Cache.php
│   └── ...
├── content/                     # Page content (SEO variables + HTML)
│   ├── en/
│   │   ├── docs/
│   │   │   ├── home.php
│   │   │   ├── installation.php
│   │   │   └── ...
│   │   └── landing/
│   │       ├── home.php
│   │       ├── about.php
│   │       └── ...
│   └── de/
│       ├── docs/
│       └── landing/
├── templates/                   # Layout templates
│   ├── en/
│   │   ├── docs.template.php
│   │   └── landing.template.php
│   └── de/
│       ├── docs.template.php
│       └── landing.template.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── .htaccess                   # URL rewriting
```

## How It Works

### 1. Main Router (`index.php`)
- Parses URL to determine language, template, and page
- Loads appropriate content file (sets SEO variables)
- Loads appropriate template file (includes header/footer)
- Handles caching

### 2. Code Classes (`code/`)
- `Router.php` - URL parsing and routing logic
- `Cache.php` - Simple file-based caching
- Other utility classes

### 3. Content Files (`content/`)
- Each file sets SEO variables (`$page_title`, `$page_description`, etc.)
- Contains the main content HTML
- Organized by language and template type

### 4. Template Files (`templates/`)
- Include header, footer, sidebar
- Use content from the content files
- Handle the overall page structure

## URL Examples

- `/en/docs/installation` → `content/en/docs/installation.php` + `templates/en/docs.template.php`
- `/de/landing/about` → `content/de/landing/about.php` + `templates/de/landing.template.php`
- `/en/landing/home` → `content/en/landing/home.php` + `templates/en/landing.template.php`

## Benefits

- ✅ Clean separation of content and layout
- ✅ Easy to add new languages (copy folder, translate)
- ✅ Easy to add new pages (copy PHP file, edit content)
- ✅ SEO-friendly (customizable per page)
- ✅ No database required
- ✅ Simple file-based caching
- ✅ Maintainable structure

## Content File Example

```php
<?php
// SEO Metadata for this page
$page_title = "GEMVC - Installation Guide";
$page_description = "Learn how to install GEMVC PHP framework";
$page_keywords = "GEMVC, installation, PHP framework";
$page_name = "Installation";
?>

<!-- Page content HTML -->
<section class="hero">
    <h1>Installation Guide</h1>
    <p>Follow these steps to install GEMVC...</p>
</section>
```

## Template File Example

```php
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $page_description; ?>">
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
</head>
<body>
    <?php include 'en/header.php'; ?>
    
    <main>
        <?php echo $content; ?>
    </main>
    
    <?php include 'en/footer.php'; ?>
</body>
</html>
```

## Implementation Steps

1. Create folder structure
2. Create base classes (Router, Cache)
3. Create main index.php router
4. Create template files
5. Create content files
6. Set up .htaccess for URL rewriting
7. Add assets (CSS, JS, images)
8. Test and refine 