<?php namespace LaravelComposer;

use LaravelComposer\Recipes\ConfiguratorRecipe;
use LaravelComposer\Recipes\VersionRecipe;
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
        $composer->addRecipe(new VersionRecipe($questionHelper, $input, $output));
        $composer->addRecipe(new ConfiguratorRecipe($questionHelper, $input, $output));

        $composer->setAppName($input->getArgument('appName'));
        $composer->run();
    }
}
