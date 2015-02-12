<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\Packages\DatabaseReceipe;
use LaravelComposer\Receipes\Packages\ORMReceipe;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ConfiguratorReceipe extends AbstractReceipe
{

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
            $composer->addReceipe(new DatabaseReceipe($this->helper, $this->input, $this->output));

            return true;
        }

        return false;
    }
}
