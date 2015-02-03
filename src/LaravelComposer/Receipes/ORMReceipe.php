<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ORMReceipe implements Receipe
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
    private $questionHelper;

    /**
     * Construct a new ORMReceipe object
     *
     * @param QuestionHelper  $questionHelper
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(QuestionHelper $questionHelper, InputInterface $input, OutputInterface $output)
    {
        $this->questionHelper = $questionHelper;
        $this->input          = $input;
        $this->output         = $output;
    }

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run()
    {
        $choices  = [ 'Eloquent', 'Doctrine' ];
        $question = new ChoiceQuestion('Which ORM would you like to use? [default: Eloquent]', $choices, $choices[0]);

        return $this->questionHelper->ask($this->input, $this->output, $question);
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
        if ($result !== 'Eloquent') {
            if ($composer->getLaravelVersion()[0] === '4') {
                $this->handleV4($composer);
            } else {
                $this->handleV5($composer);
            }

            $composer->addProvider('Mitch\LaravelDoctrine\LaravelDoctrineServiceProvider');
            $composer->addAlias('EntityManager', 'Mitch\LaravelDoctrine\EntityManagerFacade');
        }

        return true;
    }

    /**
     * @param Composer $composer
     */
    public function handleV4(Composer $composer)
    {
        $composer->addDependency('mitchellvanw/laravel-doctrine', '0.5.*');

        $composer->runCommand(
            'php artisan config:publish mitch/laravel-doctrine --path=vendor/mitch/laravel-doctrine/config'
        );
    }

    /**
     * @param Composer $composer
     */
    public function handleV5(Composer $composer)
    {
        $composer->addDependency('mitchellvanw/laravel-doctrine', 'dev-develop');
        copy('vendor/mitchellvanw/laravel-doctrine/config/doctrine.php', 'config/doctrine.php');
    }
}
