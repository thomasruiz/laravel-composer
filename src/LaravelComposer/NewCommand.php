<?php namespace LaravelComposer;

use LaravelComposer\Receipes\ConfiguratorReceipe;
use LaravelComposer\Receipes\VersionReceipe;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('new')->setDescription('Create a new laravel project.')->addArgument(
            'appName',
            InputArgument::REQUIRED,
            'Your application name'
        );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composer = new Composer($input, $output);

        $questionHelper = $this->getHelper('question');
        $composer->addReceipe(new VersionReceipe($questionHelper, $input, $output));
        $composer->addReceipe(new ConfiguratorReceipe($questionHelper, $input, $output));

        $composer->setAppName($input->getArgument('appName'));
        $composer->run();
    }
}
