<?php
// SEO Metadata for this page
$page_title = "STCMS Dokumentation - Erste Schritte";
$page_description = "Lernen Sie, wie Sie mit STCMS beginnen, einem leichtgewichtigen statischen Website CMS mit mehrsprachiger Unterstützung.";
$page_keywords = "STCMS, Dokumentation, erste Schritte, Installation, Konfiguration";
$page_name = "Erste Schritte";
?>

<div class="docs-intro">
    <h2>Willkommen zur STCMS Dokumentation</h2>
    <p>STCMS ist ein leichtgewichtiges, statisches PHP-Website-System mit sauberer Architektur und mehrsprachiger Unterstützung. Diese Dokumentation hilft Ihnen beim Einstieg und zeigt Ihnen, wie Sie das Beste aus STCMS herausholen können.</p>
</div>

<div class="docs-section">
    <h3>Was ist STCMS?</h3>
    <p>STCMS (Static Website CMS) ist für Entwickler konzipiert, die eine einfache, schnelle und wartbare Möglichkeit suchen, mehrsprachige Websites ohne die Komplexität traditioneller CMS-Systeme zu erstellen.</p>
    
    <h4>Hauptfunktionen:</h4>
    <ul>
        <li><strong>Keine Datenbank erforderlich:</strong> Alles ist dateibasiert und macht es einfach zu deployen und zu warten</li>
        <li><strong>Mehrsprachige Unterstützung:</strong> Integrierte Unterstützung für mehrere Sprachen mit sprachspezifischen Ordnern</li>
        <li><strong>Saubere URLs:</strong> SEO-freundliche URLs mit automatischem Routing</li>
        <li><strong>Template-System:</strong> Wiederverwendbare Templates mit Header-, Footer- und Sidebar-Komponenten</li>
        <li><strong>SEO-optimiert:</strong> Anpassbare Meta-Tags pro Seite</li>
        <li><strong>Optionales Caching:</strong> Dateibasiertes Caching für verbesserte Performance</li>
    </ul>
</div>

<div class="docs-section">
    <h3>Projektstruktur</h3>
    <p>STCMS folgt einer sauberen, organisierten Struktur:</p>
    
    <div class="code-block">
        <pre><code>/
├── index.php                    # Haupt-Router/Einstiegspunkt
├── code/                        # PHP-Basisklassen
│   ├── Router.php              # URL-Parsing und Routing
│   ├── Cache.php               # Dateibasiertes Caching
│   └── ...
├── content/                     # Seiteninhalt (SEO-Variablen + HTML)
│   ├── en/                     # Englischer Inhalt
│   │   ├── docs/              # Dokumentationsseiten
│   │   └── landing/           # Landing-Seiten
│   └── de/                     # Deutscher Inhalt
├── templates/                   # Layout-Templates
│   ├── en/                     # Englische Templates
│   └── de/                     # Deutsche Templates
├── assets/
│   ├── css/                    # Stylesheets
│   ├── js/                     # JavaScript-Dateien
│   └── images/                 # Bilder
└── .htaccess                   # URL-Rewriting-Regeln</code></pre>
    </div>
</div>

<div class="docs-section">
    <h3>Wie es funktioniert</h3>
    <ol>
        <li><strong>URL-Parsing:</strong> Der Router analysiert die URL, um Sprache, Template und Seite zu bestimmen</li>
        <li><strong>Content-Loading:</strong> Die entsprechende Content-Datei wird geladen (setzt SEO-Variablen)</li>
        <li><strong>Template-Rendering:</strong> Die Template-Datei wird mit Header, Footer und Content geladen</li>
        <li><strong>Output:</strong> Das finale HTML wird an den Browser gesendet</li>
    </ol>
</div>

<div class="docs-section">
    <h3>URL-Beispiele</h3>
    <ul>
        <li><code>/de/docs/installation</code> → <code>content/de/docs/installation.php</code> + <code>templates/de/docs.template.php</code></li>
        <li><code>/en/landing/about</code> → <code>content/en/landing/about.php</code> + <code>templates/en/landing.template.php</code></li>
        <li><code>/de/landing/home</code> → <code>content/de/landing/home.php</code> + <code>templates/de/landing.template.php</code></li>
    </ul>
</div>

<div class="docs-section">
    <h3>Nächste Schritte</h3>
    <p>Bereit zum Starten? Folgen Sie diesen Schritten:</p>
    <ol>
        <li><a href="/de/docs/installation">Installationsanleitung</a> - STCMS auf Ihrem Server einrichten</li>
        <li><a href="/de/docs/configuration">Konfiguration</a> - Ihre STCMS-Installation konfigurieren</li>
        <li><a href="/de/docs/templates">Templates</a> - Lernen Sie das Template-System kennen</li>
        <li><a href="/de/docs/content">Content Management</a> - Erstellen und verwalten Sie Ihren Content</li>
        <li><a href="/de/docs/multilang">Mehrsprachigkeit</a> - Mehrere Sprachen einrichten</li>
    </ol>
</div>

<div class="docs-tip">
    <h4>💡 Pro-Tipp</h4>
    <p>STCMS ist darauf ausgelegt, einfach und intuitiv zu sein. Wenn Sie mit PHP vertraut sind, sind Sie in Minuten einsatzbereit!</p>
</div> 