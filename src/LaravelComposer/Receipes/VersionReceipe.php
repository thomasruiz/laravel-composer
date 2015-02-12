<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;
use Symfony\Component\Console\Question\ChoiceQuestion;

class VersionReceipe extends AbstractReceipe
{

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run()
    {
        $choices  = [ '4.2', '5.0' ];
        $question = new ChoiceQuestion("What version of laravel do you need? [default: 5.0]", $choices, 1);

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
        $appName = $composer->getAppName();
        $composer->runCommand("composer create-project laravel/laravel $appName $result --prefer-dist");

        chdir($appName);

        $composer->changeMinimumStability();

        if ($result !== '4.2') {
            $composer->runCommand("php artisan app:name $appName");
        }

        $composer->setLaravelVersion($result);

        return true;
    }
}
