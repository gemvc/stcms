# STCMS - Static Website CMS

A lightweight, static-like PHP website with clean architecture and multi-language support.

## Features

- ✅ **No Database Required** - Everything is file-based
- ✅ **Multi-language Support** - Built-in support for multiple languages
- ✅ **Clean URLs** - SEO-friendly URLs with automatic routing
- ✅ **Template System** - Reusable templates with header, footer, and sidebar
- ✅ **SEO Optimized** - Customizable meta tags per page
- ✅ **Optional Caching** - File-based caching for improved performance
- ✅ **Modern Design** - Responsive, clean, and professional UI
- ✅ **Easy to Extend** - Simple to add new pages and languages

## Quick Start

### Prerequisites

- PHP 7.4 or higher
- Apache (with mod_rewrite enabled) or Nginx
- Web server with write permissions for cache directory

### Installation

1. **Clone or download the repository**
   ```bash
   git clone https://github.com/your-repo/stcms.git
   cd stcms
   ```

2. **Set up your web server**
   - Point your web server's document root to the STCMS directory
   - Ensure mod_rewrite is enabled (Apache)
   - Make sure the cache directory is writable

3. **Test the installation**
   - Visit `http://yourdomain.com/en` for the English home page
   - Visit `http://yourdomain.com/en/docs` for the documentation

## Project Structure

```
/
├── index.php                    # Main router/entry point
├── code/                        # PHP base classes
│   ├── Router.php              # URL parsing and routing
│   └── Cache.php               # File-based caching
├── content/                     # Page content (SEO variables + HTML)
│   ├── en/                     # English content
│   │   ├── docs/              # Documentation pages
│   │   └── landing/           # Landing pages
│   └── de/                     # German content
├── templates/                   # Layout templates
│   ├── en/                     # English templates
│   └── de/                     # German templates
├── assets/
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript files
│   └── images/                 # Images
└── .htaccess                   # URL rewriting rules
```

## How It Works

### URL Routing

STCMS uses a simple but powerful routing system:

- `/en/docs/installation` → `content/en/docs/installation.php` + `templates/en/docs.template.php`
- `/de/landing/about` → `content/de/landing/about.php` + `templates/de/landing.template.php`
- `/en/landing/home` → `content/en/landing/home.php` + `templates/en/landing.template.php`

### Content Files

Each content file sets SEO variables and contains the main HTML content:

```php
<?php
// SEO Metadata for this page
$page_title = "STCMS - Installation Guide";
$page_description = "Learn how to install STCMS on your web server.";
$page_keywords = "STCMS, installation, setup, web server, PHP";
$page_name = "Installation";
?>

<!-- Page content HTML -->
<section class="hero">
    <h1>Installation Guide</h1>
    <p>Follow these steps to install STCMS...</p>
</section>
```

### Template Files

Templates handle the overall page structure and include header, footer, and content:

```php
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main>
        <?php echo $content; ?>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>
```

## Adding New Pages

1. **Create a content file** in the appropriate language and template folder:
   ```
   content/en/landing/contact.php
   ```

2. **Set SEO variables** at the top of the file:
   ```php
   <?php
   $page_title = "Contact Us";
   $page_description = "Get in touch with our team";
   $page_keywords = "contact, support, help";
   $page_name = "Contact";
   ?>
   ```

3. **Add your HTML content** below the PHP variables

4. **Access the page** at `/en/landing/contact`

## Adding New Languages

1. **Create language folders**:
   ```bash
   mkdir -p content/fr/landing content/fr/docs
   mkdir -p templates/fr
   ```

2. **Add the language** to the Router class:
   ```php
   // In code/Router.php
   private $supportedLanguages = ['en', 'de', 'fr'];
   ```

3. **Create templates** for the new language:
   ```
   templates/fr/landing.template.php
   templates/fr/docs.template.php
   ```

4. **Add content files** in the new language folders

## Configuration

### Supported Languages

Edit `code/Router.php` to add or remove supported languages:

```php
private $supportedLanguages = ['en', 'de', 'fr', 'es'];
```

### Supported Templates

Add new template types in `code/Router.php`:

```php
private $supportedTemplates = ['docs', 'landing', 'blog'];
```

### Cache Settings

Configure caching in `code/Cache.php`:

```php
private $defaultExpiry = 3600; // 1 hour
private $cacheDir = 'cache/';
```

## Web Server Configuration

### Apache

Make sure mod_rewrite is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

The `.htaccess` file handles URL rewriting automatically.

### Nginx

Add this to your Nginx server block:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Development

### Local Development Server

For development, you can use PHP's built-in server:

```bash
php -S localhost:8000
```

Then visit `http://localhost:8000/en`

### Debug Mode

Enable error reporting for debugging by adding this to `index.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Performance

### Caching

STCMS includes optional file-based caching. The Cache class provides:

- Automatic cache expiration
- Cache invalidation
- Performance monitoring

### Optimization Tips

1. **Enable caching** for production environments
2. **Minify CSS and JavaScript** for faster loading
3. **Optimize images** before uploading
4. **Use a CDN** for static assets
5. **Enable gzip compression** on your web server

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Internet Explorer 11+

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

- **Documentation**: `/en/docs`
- **Issues**: GitHub Issues
- **Discussions**: GitHub Discussions

## Changelog

### Version 1.0.0
- Initial release
- Multi-language support
- Template system
- File-based caching
- SEO optimization
- Responsive design

---

**STCMS** - Making static websites simple and powerful! 🚀 