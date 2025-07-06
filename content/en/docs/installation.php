<?php
// SEO Metadata for this page
$page_title = "STCMS Installation Guide";
$page_description = "Learn how to install STCMS on your web server. Step-by-step installation instructions for the static website CMS.";
$page_keywords = "STCMS, installation, setup, web server, PHP";
$page_name = "Installation";
?>

<div class="docs-section">
    <h3>System Requirements</h3>
    <p>Before installing STCMS, make sure your server meets these requirements:</p>
    <ul>
        <li><strong>PHP:</strong> Version 7.4 or higher</li>
        <li><strong>Web Server:</strong> Apache (with mod_rewrite enabled) or Nginx</li>
        <li><strong>File Permissions:</strong> Write access to create cache files</li>
        <li><strong>Memory:</strong> At least 128MB RAM (recommended: 256MB+)</li>
    </ul>
</div>

<div class="docs-section">
    <h3>Installation Methods</h3>
    
    <h4>Method 1: Manual Installation</h4>
    <ol>
        <li><strong>Download STCMS:</strong>
            <div class="code-block">
                <pre><code># Clone the repository
git clone https://github.com/your-repo/stcms.git

# Or download the ZIP file and extract it</code></pre>
            </div>
        </li>
        <li><strong>Upload to Server:</strong> Upload all files to your web server's document root or a subdirectory</li>
        <li><strong>Set Permissions:</strong>
            <div class="code-block">
                <pre><code># Make sure the cache directory is writable
chmod 755 cache/
chmod 644 .htaccess</code></pre>
            </div>
        </li>
        <li><strong>Test Installation:</strong> Visit your domain in a web browser</li>
    </ol>
    
    <h4>Method 2: Using Composer</h4>
    <div class="code-block">
        <pre><code># Create a new project
composer create-project stcms/stcms my-website

# Navigate to project directory
cd my-website

# Start development server
php -S localhost:8000</code></pre>
    </div>
</div>

<div class="docs-section">
    <h3>Configuration</h3>
    <p>After installation, you may want to configure STCMS for your needs:</p>
    
    <h4>1. Supported Languages</h4>
    <p>Edit <code>code/Router.php</code> to add or remove supported languages:</p>
    <div class="code-block">
        <pre><code>private $supportedLanguages = ['en', 'de', 'fr', 'es'];</code></pre>
    </div>
    
    <h4>2. Supported Templates</h4>
    <p>Add new template types in <code>code/Router.php</code>:</p>
    <div class="code-block">
        <pre><code>private $supportedTemplates = ['docs', 'landing', 'blog'];</code></pre>
    </div>
    
    <h4>3. Cache Settings</h4>
    <p>Configure caching in <code>code/Cache.php</code>:</p>
    <div class="code-block">
        <pre><code>private $defaultExpiry = 3600; // 1 hour
private $cacheDir = 'cache/';</code></pre>
    </div>
</div>

<div class="docs-section">
    <h3>Directory Structure Setup</h3>
    <p>Create the required directories if they don't exist:</p>
    <div class="code-block">
        <pre><code>mkdir -p content/en/docs content/en/landing
mkdir -p content/de/docs content/de/landing
mkdir -p templates/en templates/de
mkdir -p assets/css assets/js assets/images
mkdir -p cache</code></pre>
    </div>
</div>

<div class="docs-section">
    <h3>Web Server Configuration</h3>
    
    <h4>Apache Configuration</h4>
    <p>Make sure mod_rewrite is enabled and .htaccess is working:</p>
    <div class="code-block">
        <pre><code># Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2</code></pre>
    </div>
    
    <h4>Nginx Configuration</h4>
    <p>Add this to your Nginx server block:</p>
    <div class="code-block">
        <pre><code>location / {
    try_files $uri $uri/ /index.php?$query_string;
}</code></pre>
    </div>
</div>

<div class="docs-section">
    <h3>Testing Your Installation</h3>
    <p>After installation, test these URLs:</p>
    <ul>
        <li><code>http://yourdomain.com/en</code> - English home page</li>
        <li><code>http://yourdomain.com/en/docs</code> - English documentation</li>
        <li><code>http://yourdomain.com/de</code> - German home page</li>
        <li><code>http://yourdomain.com/de/docs</code> - German documentation</li>
    </ul>
</div>

<div class="docs-section">
    <h3>Troubleshooting</h3>
    
    <h4>Common Issues:</h4>
    <ul>
        <li><strong>404 Errors:</strong> Check that mod_rewrite is enabled and .htaccess is readable</li>
        <li><strong>Permission Errors:</strong> Ensure the cache directory is writable</li>
        <li><strong>Blank Pages:</strong> Check PHP error logs for syntax errors</li>
        <li><strong>URL Rewriting Not Working:</strong> Verify .htaccess is in the root directory</li>
    </ul>
    
    <h4>Debug Mode</h4>
    <p>Enable error reporting for debugging:</p>
    <div class="code-block">
        <pre><code>// Add to index.php for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);</code></pre>
    </div>
</div>

<div class="docs-tip">
    <h4>💡 Pro Tip</h4>
    <p>For development, use PHP's built-in server: <code>php -S localhost:8000</code>. This is perfect for testing and development.</p>
</div>

<div class="docs-section">
    <h3>Next Steps</h3>
    <p>Now that STCMS is installed, you can:</p>
    <ul>
        <li><a href="/en/docs/configuration">Configure your installation</a></li>
        <li><a href="/en/docs/templates">Learn about templates</a></li>
        <li><a href="/en/docs/content">Start creating content</a></li>
        <li><a href="/en/docs/multilang">Set up multiple languages</a></li>
    </ul>
</div> 