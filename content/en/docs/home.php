<?php
// SEO Metadata for this page
$page_title = "STCMS Documentation - Getting Started";
$page_description = "Learn how to get started with STCMS, a lightweight static website CMS with multi-language support.";
$page_keywords = "STCMS, documentation, getting started, installation, configuration";
$page_name = "Getting Started";
?>

<div class="docs-intro">
    <h2>Welcome to STCMS Documentation</h2>
    <p>STCMS is a lightweight, static-like PHP website system with clean architecture and multi-language support. This documentation will help you get started and make the most of STCMS.</p>
</div>

<div class="docs-section">
    <h3>What is STCMS?</h3>
    <p>STCMS (Static Website CMS) is designed for developers who want a simple, fast, and maintainable way to build multi-language websites without the complexity of traditional CMS systems.</p>
    
    <h4>Key Features:</h4>
    <ul>
        <li><strong>No Database Required:</strong> Everything is file-based, making it easy to deploy and maintain</li>
        <li><strong>Multi-language Support:</strong> Built-in support for multiple languages with per-language folders</li>
        <li><strong>Clean URLs:</strong> SEO-friendly URLs with automatic routing</li>
        <li><strong>Template System:</strong> Reusable templates with header, footer, and sidebar components</li>
        <li><strong>SEO Optimized:</strong> Customizable meta tags per page</li>
        <li><strong>Optional Caching:</strong> File-based caching for improved performance</li>
    </ul>
</div>

<div class="docs-section">
    <h3>Project Structure</h3>
    <p>STCMS follows a clean, organized structure:</p>
    
    <div class="code-block">
        <pre><code>/
├── index.php                    # Main router/entry point
├── code/                        # PHP base classes
│   ├── Router.php
│   ├── Cache.php
│   └── ...
├── content/                     # Page content (SEO variables + HTML)
│   ├── en/
│   │   ├── docs/
│   │   └── landing/
│   └── de/
├── templates/                   # Layout templates
│   ├── en/
│   └── de/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
└── .htaccess                   # URL rewriting</code></pre>
    </div>
</div>

<div class="docs-section">
    <h3>How It Works</h3>
    <ol>
        <li><strong>URL Parsing:</strong> The router parses the URL to determine language, template, and page</li>
        <li><strong>Content Loading:</strong> The appropriate content file is loaded (sets SEO variables)</li>
        <li><strong>Template Rendering:</strong> The template file is loaded with header, footer, and content</li>
        <li><strong>Output:</strong> The final HTML is sent to the browser</li>
    </ol>
</div>

<div class="docs-section">
    <h3>URL Examples</h3>
    <ul>
        <li><code>/en/docs/installation</code> → <code>content/en/docs/installation.php</code> + <code>templates/en/docs.template.php</code></li>
        <li><code>/de/landing/about</code> → <code>content/de/landing/about.php</code> + <code>templates/de/landing.template.php</code></li>
        <li><code>/en/landing/home</code> → <code>content/en/landing/home.php</code> + <code>templates/en/landing.template.php</code></li>
    </ul>
</div>

<div class="docs-section">
    <h3>Next Steps</h3>
    <p>Ready to get started? Follow these steps:</p>
    <ol>
        <li><a href="/en/docs/installation">Installation Guide</a> - Set up STCMS on your server</li>
        <li><a href="/en/docs/configuration">Configuration</a> - Configure your STCMS installation</li>
        <li><a href="/en/docs/templates">Templates</a> - Learn about the template system</li>
        <li><a href="/en/docs/content">Content Management</a> - Create and manage your content</li>
        <li><a href="/en/docs/multilang">Multi-language</a> - Set up multiple languages</li>
    </ol>
</div>

<div class="docs-tip">
    <h4>💡 Pro Tip</h4>
    <p>STCMS is designed to be simple and intuitive. If you're familiar with PHP, you'll be up and running in minutes!</p>
</div> 