<?php
namespace Gemvc\Stcms\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class InitCommand extends Command
{
    protected static $defaultName = 'init';
    protected static $defaultDescription = 'Initialize a new STCMS project with recommended folder structure and example files.';

    protected function configure(): void
    {
        $this
            ->setName('init')
            ->setDescription('Initialize a new STCMS project with recommended folder structure and example files.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $setupDir = __DIR__ . '/../setup';
        
        // Create directories
        $dirs = [
            'templates',
            'pages',
            'assets/js/components',
            'assets/css',
            'public/assets/js',
        ];
        foreach ($dirs as $dir) {
            $fs->mkdir($dir);
        }

        // Copy setup files
        $this->copySetupFiles($fs, $setupDir, $output);

        $output->writeln('<info>STCMS project initialized!</info>');
        $output->writeln('Next steps:');
        $output->writeln('  1. Edit .env for your API base URL and cache settings.');
        $output->writeln('  2. Run npm install && npx vite build to build frontend assets.');
        $output->writeln('  3. Customize templates/example.twig and React components as needed.');
        $output->writeln('  4. Start building your hybrid PHP/React app!');
        return Command::SUCCESS;
    }

        private function copySetupFiles(Filesystem $fs, string $setupDir, OutputInterface $output): void
    {
        // Copy .env template
        $envContent = file_get_contents($setupDir . '/env.template');
        $fs->dumpFile('.env', $envContent);

        // Copy vite.config.js
        $viteContent = file_get_contents($setupDir . '/vite.config.js');
        $fs->dumpFile('vite.config.js', $viteContent);

        // Copy package.json
        $packageContent = file_get_contents($setupDir . '/package.json');
        $fs->dumpFile('package.json', $packageContent);

        // Copy .gitignore
        $gitignoreContent = file_get_contents($setupDir . '/.gitignore');
        $fs->dumpFile('.gitignore', $gitignoreContent);

        // Copy templates
        foreach (glob($setupDir . '/templates/*.twig') as $templateFile) {
            $basename = basename($templateFile);
            $fs->dumpFile('templates/' . $basename, file_get_contents($templateFile));
        }

        // Copy pages with multi-language structure
        $this->copyPagesWithLanguages($fs, $setupDir, $output);

        // Copy all React components from setup
        foreach (glob($setupDir . '/assets/js/components/*.jsx') as $componentFile) {
            $basename = basename($componentFile);
            $fs->dumpFile('assets/js/components/' . $basename, file_get_contents($componentFile));
        }

        // Copy React entry point
        $reactEntryContent = file_get_contents($setupDir . '/assets/js/app.jsx');
        $fs->dumpFile('assets/js/app.jsx', $reactEntryContent);

        // Copy registry.js for registry-based React mounting
        $registryContent = file_get_contents($setupDir . '/assets/js/registry.js');
        $fs->dumpFile('assets/js/registry.js', $registryContent);

        // Copy index.php
        $indexContent = file_get_contents($setupDir . '/index.php');
        $fs->dumpFile('index.php', $indexContent);

        // Copy .htaccess
        $htaccessContent = file_get_contents($setupDir . '/.htaccess');
        $fs->dumpFile('.htaccess', $htaccessContent);

        // Copy Project.md from root
        $projectContent = file_get_contents(__DIR__ . '/../../Project.md');
        $fs->dumpFile('Project.md', $projectContent);

        // Copy README.md from root
        $readmeContent = file_get_contents(__DIR__ . '/../../README.md');
        $fs->dumpFile('README.md', $readmeContent);

        // Copy AI_ONBOARDING.md from root
        $onboardingContent = file_get_contents(__DIR__ . '/../../AI_ONBOARDING.md');
        $fs->dumpFile('AI_ONBOARDING.md', $onboardingContent);

        $output->writeln('<comment>Copied setup files from src/setup/</comment>');
    }

    private function copyPagesWithLanguages(Filesystem $fs, string $setupDir, OutputInterface $output): void
    {
        $pagesDir = $setupDir . '/pages';
        
        // Get all language directories
        $languageDirs = glob($pagesDir . '/*', GLOB_ONLYDIR);
        
        foreach ($languageDirs as $langDir) {
            $langName = basename($langDir);
            
            // Create language directory in user's project
            $fs->mkdir('pages/' . $langName);
            
            // Copy all .twig files from this language directory
            foreach (glob($langDir . '/*.twig') as $pageFile) {
                $basename = basename($pageFile);
                $fs->dumpFile('pages/' . $langName . '/' . $basename, file_get_contents($pageFile));
            }
            
            $output->writeln('<comment>Copied ' . $langName . ' pages</comment>');
        }
    }
} 