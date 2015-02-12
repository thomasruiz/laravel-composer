<?php namespace LaravelComposer\Receipes\Packages;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\AbstractReceipe;
use LaravelComposer\Receipes\Packages\DatabaseReceipes\DatabaseConfiguratorReceipe;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DatabaseReceipe extends AbstractReceipe
{

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run()
    {
        $question = new ConfirmationQuestion("Do you want to configure the database? (Y/n) ");

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
            $composer->addReceipe(new DatabaseConfiguratorReceipe($this->helper, $this->input, $this->output));
        }

        return true;
    }
}
