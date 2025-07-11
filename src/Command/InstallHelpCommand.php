<?php
namespace Gemvc\Stcms\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InstallHelpCommand extends Command
{
    protected static $defaultName = 'install:help';
    protected static $defaultDescription = 'Installs the documentation for specified languages.';

    protected function configure(): void
    {
        $this
            ->setName('install:help')
            ->setDescription('Installs the documentation and examples for one or more languages.')
            ->addArgument('languages', InputArgument::IS_ARRAY, 'The language(s) to install (e.g., en de fa). Defaults to "en".');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $helpDir = __DIR__ . '/../setup/help/pages';

        if (!$fs->exists($helpDir)) {
            $output->writeln('<error>Help source directory not found!</error>');
            return Command::FAILURE;
        }

        $languagesToInstall = $input->getArgument('languages');
        if (empty($languagesToInstall)) {
            $languagesToInstall = ['en']; // Default to English
            $output->writeln('<comment>No language specified. Defaulting to "en".</comment>');
        }

        $output->writeln('<comment>Installing help files and examples...</comment>');

        foreach ($languagesToInstall as $langName) {
            $langDir = $helpDir . '/' . $langName;
            if (!$fs->exists($langDir)) {
                $output->writeln('<error>Language "' . $langName . '" not found in help sources. Skipping.</error>');
                continue;
            }

            $targetDir = 'pages/' . $langName;
            $fs->mkdir($targetDir);
            $fs->mirror($langDir, $targetDir);
            
            $output->writeln('<info>Copied documentation pages for language: ' . $langName . '</info>');
        }

        $output->writeln('<info>Help documentation and examples installed successfully!</info>');
        $output->writeln('You can now see all examples in your browser.');
        return Command::SUCCESS;
    }
} 