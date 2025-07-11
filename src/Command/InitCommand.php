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
        $setupDir = __DIR__ . '/../setup/minimal'; // Changed to 'minimal'
        
        // Create directories
        $dirs = [
            'templates',
            'pages/en', // Only create 'en' directory initially
            'assets/js/components',
            'assets/css',
            'public/assets/build', // Ensure build directory exists
            'components', // Create components directory
        ];
        foreach ($dirs as $dir) {
            $fs->mkdir($dir);
        }

        // Copy setup files
        $this->copySetupFiles($fs, $output); // Pass only needed arguments

        $output->writeln('<info>STCMS project initialized with a minimal setup!</info>');
        $output->writeln('Run <comment>npm install && npm run dev</comment> to start the dev server.');
        $output->writeln('For complete documentation and examples, run:');
        $output->writeln('<comment>php vendor/gemvc/stcms/bin/stcms install:help</comment>');
        return Command::SUCCESS;
    }

    private function copySetupFiles(Filesystem $fs, OutputInterface $output): void
    {
        $minimalSetupDir = __DIR__ . '/../setup/minimal';
        $rootSetupDir = __DIR__ . '/../setup';

        // Copy .env template from the main setup folder
        $envContent = file_get_contents($rootSetupDir . '/env.template');
        $fs->dumpFile('.env', $envContent);

        // Copy vite.config.js from the main setup folder
        $viteContent = file_get_contents($rootSetupDir . '/vite.config.js');
        $fs->dumpFile('vite.config.js', $viteContent);

        // Copy package.json from the main setup folder
        $packageContent = file_get_contents($rootSetupDir . '/package.json');
        $fs->dumpFile('package.json', $packageContent);

        // Copy .gitignore from the main setup folder
        $gitignoreContent = file_get_contents($rootSetupDir . '/.gitignore');
        $fs->dumpFile('.gitignore', $gitignoreContent);

        // Copy templates from the main setup folder
        foreach (glob($rootSetupDir . '/templates/*.twig') as $templateFile) {
            $basename = basename($templateFile);
            $fs->dumpFile('templates/' . $basename, file_get_contents($templateFile));
        }

        // Copy minimal pages
        $this->copyMinimalPages($fs, $minimalSetupDir, $output);

        // Copy assets from the main setup folder
        $this->copyAssets($fs, $rootSetupDir, $output);

        // Copy reusable components from the main setup folder
        $this->copyComponents($fs, $rootSetupDir, $output);

        // Copy index.php from the main setup folder
        $indexContent = file_get_contents($rootSetupDir . '/index.php');
        $fs->dumpFile('index.php', $indexContent);

        // Copy .htaccess from the main setup folder
        $htaccessContent = file_get_contents($rootSetupDir . '/.htaccess');
        $fs->dumpFile('.htaccess', $htaccessContent);

        $output->writeln('<comment>Copied minimal setup files.</comment>');
    }

    private function copyMinimalPages(Filesystem $fs, string $minimalSetupDir, OutputInterface $output): void
    {
        $pagesDir = $minimalSetupDir . '/pages/en';
        foreach (glob($pagesDir . '/*.twig') as $pageFile) {
            $basename = basename($pageFile);
            $fs->dumpFile('pages/en/' . $basename, file_get_contents($pageFile));
        }
        $output->writeln('<comment>Copied minimal pages for "en" language.</comment>');
    }

    private function copyAssets(Filesystem $fs, string $setupDir, OutputInterface $output): void
    {
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

        $output->writeln('<comment>Copied assets.</comment>');
    }

    private function copyComponents(Filesystem $fs, string $setupDir, OutputInterface $output): void
    {
        // Copy all component files from setup
        foreach (glob($setupDir . '/components/components.twig') as $componentFile) {
            $basename = basename($componentFile);
            $fs->dumpFile('components/' . $basename, file_get_contents($componentFile));
        }
        $output->writeln('<comment>Copied components.</comment>');
    }
} 