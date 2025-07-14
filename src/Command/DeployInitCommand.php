<?php
namespace Gemvc\Stcms\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Filesystem;

class DeployInitCommand extends Command
{
    protected static $defaultName = 'deploy:init';
    protected static $defaultDescription = 'Initializes a deployment workflow.';

    protected function configure(): void
    {
        $this
            ->setName('deploy:init')
            ->setDescription('Creates a new deployment workflow file (e.g., for GitHub Actions).')
            ->addArgument('type', InputArgument::OPTIONAL, 'The type of deployment to initialize.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fs = new Filesystem();
        $projectRoot = getcwd();
        $deploySetupDir = realpath(__DIR__ . '/../setup/deploy');

        if (!$deploySetupDir || !is_dir($deploySetupDir)) {
            $output->writeln('<error>Deployment setup directory not found.</error>');
            return Command::FAILURE;
        }

        $availableTypes = array_map('basename', glob($deploySetupDir . '/*', GLOB_ONLYDIR));
        if (empty($availableTypes)) {
            $output->writeln('<error>No deployment types available.</error>');
            return Command::FAILURE;
        }

        $type = $input->getArgument('type');
        if (!$type) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select the deployment type:',
                $availableTypes,
                0
            );
            $question->setErrorMessage('Deployment type %s is invalid.');
            $type = $helper->ask($input, $output, $question);
        }

        if (!in_array($type, $availableTypes)) {
            $output->writeln(sprintf('<error>Deployment type "%s" is not supported. Available types: %s</error>', $type, implode(', ', $availableTypes)));
            return Command::FAILURE;
        }

        $sourceDir = $deploySetupDir . '/' . $type;
        $targetDir = $projectRoot . '/.github/workflows';
        $targetFile = $targetDir . '/deploy.yml';

        if ($fs->exists($targetFile)) {
            $output->writeln('<comment>A deployment workflow already exists at .github/workflows/deploy.yml. Aborting.</comment>');
            return Command::FAILURE;
        }

        $fs->mkdir($targetDir);
        $fs->copy($sourceDir . '/deploy.yml', $targetFile);

        $output->writeln(sprintf('<info>✅ Deployment workflow for "%s" created successfully!</info>', $type));
        $output->writeln('File created at: <comment>.github/workflows/deploy.yml</comment>');
        $output->writeln('Next steps:');
        $output->writeln('1. Make sure you have set the required secrets in your GitHub repository settings (e.g., FTP_SERVER, FTP_USERNAME, API_BASE_URL).');
        $output->writeln('2. Commit and push the new workflow file to your main branch to activate it.');

        return Command::SUCCESS;
    }
} 