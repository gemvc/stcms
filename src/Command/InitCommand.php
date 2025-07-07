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
            'src/Core',
            'src/Command',
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

        // Copy pages
        foreach (glob($setupDir . '/pages/*.twig') as $pageFile) {
            $basename = basename($pageFile);
            $fs->dumpFile('pages/' . $basename, file_get_contents($pageFile));
        }

        // Copy React components
        $reactComponentContent = file_get_contents($setupDir . '/assets/js/components/UserProfile.jsx');
        $fs->dumpFile('assets/js/components/UserProfile.jsx', $reactComponentContent);

        // Copy React entry point
        $reactEntryContent = file_get_contents($setupDir . '/assets/js/app.jsx');
        $fs->dumpFile('assets/js/app.jsx', $reactEntryContent);

        // Copy index.php
        $indexContent = file_get_contents($setupDir . '/index.php');
        $fs->dumpFile('index.php', $indexContent);

        // Copy .htaccess
        $htaccessContent = file_get_contents($setupDir . '/.htaccess');
        $fs->dumpFile('.htaccess', $htaccessContent);

        // Copy Project.md
        $projectContent = file_get_contents($setupDir . '/Project.md');
        $fs->dumpFile('Project.md', $projectContent);

        // Copy README.md
        $readmeContent = file_get_contents($setupDir . '/README.md');
        $fs->dumpFile('README.md', $readmeContent);

        $output->writeln('<comment>Copied setup files from src/setup/</comment>');
    }
} 