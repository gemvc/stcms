<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/docs.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <div class="navbar-brand">
                    <a href="/<?php echo $language; ?>" class="logo">STCMS</a>
                </div>
                <ul class="navbar-nav">
                    <li><a href="/<?php echo $language; ?>">Home</a></li>
                    <li><a href="/<?php echo $language; ?>/landing/about">About</a></li>
                    <li><a href="/<?php echo $language; ?>/landing/contact">Contact</a></li>
                    <li><a href="/<?php echo $language; ?>/docs" class="active">Documentation</a></li>
                </ul>
                <div class="language-switcher">
                    <a href="/en<?php echo str_replace('/' . $language, '', $_SERVER['REQUEST_URI']); ?>">EN</a>
                    <a href="/de<?php echo str_replace('/' . $language, '', $_SERVER['REQUEST_URI']); ?>">DE</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="docs-container">
        <div class="container">
            <div class="docs-layout">
                <!-- Sidebar -->
                <aside class="docs-sidebar">
                    <nav class="docs-nav">
                        <h3>Documentation</h3>
                        <ul>
                            <li><a href="/<?php echo $language; ?>/docs" <?php echo ($page === 'home') ? 'class=\"active\"' : ''; ?>>Getting Started</a></li>
                            <li><a href="/<?php echo $language; ?>/docs/installation" <?php echo ($page === 'installation') ? 'class=\"active\"' : ''; ?>>Installation</a></li>
                            <li><a href="/<?php echo $language; ?>/docs/configuration" <?php echo ($page === 'configuration') ? 'class=\"active\"' : ''; ?>>Configuration</a></li>
                            <li><a href="/<?php echo $language; ?>/docs/templates" <?php echo ($page === 'templates') ? 'class=\"active\"' : ''; ?>>Templates</a></li>
                            <li><a href="/<?php echo $language; ?>/docs/content" <?php echo ($page === 'content') ? 'class=\"active\"' : ''; ?>>Content Management</a></li>
                            <li><a href="/<?php echo $language; ?>/docs/multilang" <?php echo ($page === 'multilang') ? 'class=\"active\"' : ''; ?>>Multi-language</a></li>
                            <li><a href="/<?php echo $language; ?>/docs/caching" <?php echo ($page === 'caching') ? 'class=\"active\"' : ''; ?>>Caching</a></li>
                        </ul>
                    </nav>
                </aside>

                <!-- Content -->
                <main class="docs-content">
                    <div class="docs-header">
                        <h1><?php echo htmlspecialchars($page_name); ?></h1>
                        <nav class="breadcrumb">
                            <a href="/<?php echo $language; ?>">Home</a> &gt;
                            <a href="/<?php echo $language; ?>/docs">Documentation</a> &gt;
                            <span><?php echo htmlspecialchars($page_name); ?></span>
                        </nav>
                    </div>
                    
                    <div class="docs-body">
                        <?php echo $content; ?>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>STCMS</h3>
                    <p>A lightweight, static-like PHP website with clean architecture and multi-language support.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="/<?php echo $language; ?>">Home</a></li>
                        <li><a href="/<?php echo $language; ?>/landing/about">About</a></li>
                        <li><a href="/<?php echo $language; ?>/landing/contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Documentation</h4>
                    <ul>
                        <li><a href="/<?php echo $language; ?>/docs">Getting Started</a></li>
                        <li><a href="/<?php echo $language; ?>/docs/installation">Installation</a></li>
                        <li><a href="/<?php echo $language; ?>/docs/configuration">Configuration</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> STCMS. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/docs.js"></script>
</body>
</html> 