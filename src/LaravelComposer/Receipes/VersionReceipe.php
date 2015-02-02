<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class VersionReceipe implements Receipe
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
        $choices  = [
            '4.2',
            '5.0',
            'dev-develop'
        ];
        $question = new ChoiceQuestion("What version of laravel do you need? (defaults: 5.0)", $choices, 1);

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
        $command = "composer create-project laravel/laravel {$this->input->getArgument('appName')} $result";
        $composer->info("Running $command...");

        $process = new Process($command);
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (!$process->isSuccessful()) {
            throw new RuntimeException('Composer failed. Did you install it?');
        }

        return true;
    }
}
