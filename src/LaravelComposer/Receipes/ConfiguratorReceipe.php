<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\Packages\ORMReceipe;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ConfiguratorReceipe implements Receipe
{

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $helper;

    /**
     * Construct a new VersionReceipe object
     *
     * @param QuestionHelper  $helper
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(QuestionHelper $helper, InputInterface $input, OutputInterface $output)
    {
        $this->helper = $helper;
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run()
    {
        $question = new ConfirmationQuestion('Would you like to configure your new application? (Y/n) ');

        return $this->helper->ask($this->input, $this->output, $question);
    }

    /**
     * Handle the result. Return false if the composer should stop.
     *
     * @param Composer    $composer
     * @param string|bool $result
     *
     * @return bool
     */
    public function handle(Composer $composer, $result)
    {
        if ($result) {
            $composer->addReceipe(new ORMReceipe($this->helper, $this->input, $this->output));

            return true;
        }

        return false;
    }
}
