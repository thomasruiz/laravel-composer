<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
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
        $this->runCreateProject($composer, $result, $appName = $this->input->getArgument('appName'));

        chdir($appName);

        if ($result !== '4.2') {
            $this->runArtisanAppName($composer, $appName);
        }

        return true;
    }

    /**
     * Run `composer create-project...` to retrieve the laravel template.
     *
     * @param Composer $composer
     * @param string   $result
     * @param string   $appName
     *
     * @throws \RuntimeException
     */
    protected function runCreateProject(Composer $composer, $result, $appName)
    {
        $command = "composer create-project laravel/laravel $appName $result --prefer-dist";
        $composer->info("Running $command...");

        $process = new Process($command);
        $process->setTimeout(0);
        $process->run(
            function ($type, $buffer) {
                $this->output->write($buffer);
            }
        );

        if (!$process->isSuccessful()) {
            throw new \RuntimeException();
        }
    }

    /**
     * Change the application namespaces with `artisan app:name`
     *
     * @param Composer $composer
     * @param string   $appName
     */
    protected function runArtisanAppName(Composer $composer, $appName)
    {
        $command = "php artisan app:name $appName";
        $composer->info("Changing your application name");

        $process = new Process($command);
        $process->run(
            function ($type, $buffer) {
                $this->output->write($buffer);
            }
        );
    }
}
