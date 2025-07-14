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
            'public/assets/build',
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
        
        // Copy the README.md from the package root to the project root
        $packageReadme = realpath(__DIR__ . '/../../README.md');
        if ($packageReadme && $fs->exists($packageReadme)) {
            $fs->copy($packageReadme, $root . '/README.md', true);
        }
        
        // Copy vite.config.js from setup
        $fs->copy($setupDir . '/vite.config.js', $root . '/vite.config.js'); 
        
        // Copy PHP entry point to public directory
        $fs->copy($setupDir . '/index.php', $root . '/public/index.php');

        // Copy Twig components, templates, and pages
        $fs->mirror($setupDir . '/components', $root . '/components');
        $fs->mirror($setupDir . '/templates', $root . '/templates');
        $fs->mirror($setupDir . '/minimal/pages', $root . '/pages');

        // Copy assets source files
        $fs->mirror($setupDir . '/assets', $root . '/assets');

        $output->writeln('Copied boilerplate files.');
    }

    private function createHtaccessFiles(Filesystem $fs, string $root, OutputInterface $output): void
    {
        // Copy root .htaccess from setup file
        $fs->copy(realpath(__DIR__ . '/../setup/htaccess_root'), $root . '/.htaccess');

        // Copy public .htaccess from setup file
        $fs->copy(realpath(__DIR__ . '/../setup/htaccess_public'), $root . '/public/.htaccess');
        
        $output->writeln('Created .htaccess files for secure routing.');
    }
} 