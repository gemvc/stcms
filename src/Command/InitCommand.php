<?php
namespace Gemvc\Stcms\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InitCommand extends Command
{
    protected static $defaultName = 'init';
    protected static $defaultDescription = 'Initializes a new STCMS project with a secure, public-directory structure.';

    protected function configure(): void
    {
        $this
            ->setName('init')
            ->setDescription('Initializes a new STCMS project...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $projectRoot = getcwd();
        $setupDir = realpath(__DIR__ . '/../setup');

        if (!$setupDir) {
            $output->writeln('<error>Setup directory could not be found. Check package installation.</error>');
            return Command::FAILURE;
        }

        try {
            $output->writeln('<info>Initializing STCMS project...</info>');

            // 1. Create directory structure
            $this->createDirectories($fs, $projectRoot, $output);

            // 2. Copy boilerplate files
            $this->copyBoilerplateFiles($fs, $projectRoot, $setupDir, $output);
            
            // 3. Create .htaccess files
            $this->createHtaccessFiles($fs, $projectRoot, $output);

            $output->writeln('<info>----------------------------------------------------</info>');
            $output->writeln('<info>✅ STCMS project initialized successfully!</info>');
            $output->writeln('<comment>Your document root should be set to the /public directory.</comment>');
            $output->writeln('Next steps:');
            $output->writeln('1. Run <comment>npm install</comment> to install frontend dependencies.');
            $output->writeln('2. Run <comment>npm run dev</comment> to start the Vite development server.');
            $output->writeln('3. Configure your local server (Apache/Nginx) to use <comment>'.$projectRoot.'/public</comment> as the document root.');
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>An error occurred during initialization:</error>');
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    private function createDirectories(Filesystem $fs, string $root, OutputInterface $output): void
    {
        $dirs = [
            'assets/js/components',
            'assets/css',
            'components',
            'pages/en',
            'public/assets/build', // Vite build output
            'templates',
        ];
        foreach ($dirs as $dir) {
            $fs->mkdir($root . '/' . $dir);
        }
        $output->writeln('Created directory structure.');
    }

    private function copyBoilerplateFiles(Filesystem $fs, string $root, string $setupDir, OutputInterface $output): void
    {
        // Standard config files in project root
        $fs->copy($setupDir . '/.gitignore', $root . '/.gitignore');
        $fs->copy($setupDir . '/env.template', $root . '/.env');
        $fs->copy($setupDir . '/package.json', $root . '/package.json');
        
        // Use the new, corrected vite.config.js
        $this->createViteConfig($fs, $root); 
        
        // Copy PHP entry point to public directory
        $this->createPublicIndex($fs, $root, $setupDir);

        // Copy Twig components, templates, and pages
        $fs->mirror($setupDir . '/components', $root . '/components');
        $fs->mirror($setupDir . '/templates', $root . '/templates');
        $fs->mirror($setupDir . '/minimal/pages', $root . '/pages');

        // Copy assets source files
        $fs->mirror($setupDir . '/assets', $root . '/assets');

        $output->writeln('Copied boilerplate files.');
    }
    
    private function createViteConfig(Filesystem $fs, string $root): void
    {
        $content = <<<EOT
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  root: 'assets',
  build: {
    outDir: '../../public/assets/build',
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'assets/js/app.jsx'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
        assetFileNames: 'css/[name].[ext]',
      },
    },
  },
});
EOT;
        $fs->dumpFile($root . '/vite.config.js', $content);
    }

    private function createPublicIndex(Filesystem $fs, string $root, string $setupDir): void
    {
        $content = <<<'EOT'
<?php
// We are in public/, so we need to go one level up to reach the project root.
require_once __DIR__ . '/../vendor/autoload.php';

use Gemvc\Stcms\Core\Application;
use Gemvc\Stcms\Core\TemplateEngine;
use Gemvc\Stcms\Core\ApiClient;
use Symfony\Component\Dotenv\Dotenv;
use Gemvc\Stcms\Core\MultilingualRouter;

$projectRoot = dirname(__DIR__);
$dotenv = new Dotenv();
$dotenv->loadEnv($projectRoot . '/.env');

// Set default environment variables
$_ENV['APP_ENV'] = $_ENV['APP_ENV'] ?? 'production';
$_ENV['API_BASE_URL'] = $_ENV['API_BASE_URL'] ?? 'http://localhost:80';
$_ENV['DEFAULT_LANGUAGE'] = $_ENV['DEFAULT_LANGUAGE'] ?? 'en';
$_ENV['VITE_BASE_URL'] = $_ENV['VITE_BASE_URL'] ?? 'http://localhost:5173';

$apiClient = new ApiClient($_ENV['API_BASE_URL']);
$templateEngine = new TemplateEngine(
    [
        $projectRoot . '/pages',
        $projectRoot . '/templates',
        $projectRoot . '/components',
    ],
    $projectRoot,
    $_ENV['APP_ENV'],
    $_ENV['VITE_BASE_URL']
);

$supportedLanguages = array_map('basename', glob($projectRoot . '/pages/*', GLOB_ONLYDIR));
if (empty($supportedLanguages)) {
    $supportedLanguages = ['en'];
}
if (!in_array($_ENV['DEFAULT_LANGUAGE'], $supportedLanguages)) {
    $_ENV['DEFAULT_LANGUAGE'] = $supportedLanguages[0];
}

$router = new MultilingualRouter($supportedLanguages);
$app = new Application($router, $templateEngine, $apiClient);
$app->run();
EOT;
        $fs->dumpFile($root . '/public/index.php', $content);
    }

    private function createHtaccessFiles(Filesystem $fs, string $root, OutputInterface $output): void
    {
        // Root .htaccess
        $rootHtaccess = <<<EOT
# Forbid access to sensitive files in the root directory
<FilesMatch "^(\.env|\.gitignore|composer\.json|composer\.lock|package\.json|vite\.config\.js)$">
    Require all denied
</FilesMatch>

# Explicitly block access to all non-public directories
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(assets|components|pages|src|templates|vendor)($|/) - [F,L]
    RewriteRule ^(.*)$ public/\$1 [L]
</IfModule>
EOT;
        $fs->dumpFile($root . '/.htaccess', $rootHtaccess);

        // Public .htaccess
        $publicHtaccess = <<<EOT
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php?path=\$1 [QSA,L]
</IfModule>

<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "DENY"
    <FilesMatch "\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$">
        Header set Cache-Control "public, immutable, max-age=31536000"
    </FilesMatch>
</IfModule>
EOT;
        $fs->dumpFile($root . '/public/.htaccess', $publicHtaccess);
        
        $output->writeln('Created .htaccess files for secure routing.');
    }
} 