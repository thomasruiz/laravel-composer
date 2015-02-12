<?php namespace LaravelComposer\Receipes\Packages;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\AbstractReceipe;
use Symfony\Component\Console\Question\ChoiceQuestion;

class ORMReceipe extends AbstractReceipe
{

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run()
    {
        $choices  = [ 'Eloquent', 'Doctrine' ];
        $question = new ChoiceQuestion('Which ORM would you like to use? [default: Eloquent]', $choices, $choices[0]);

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
        if ($result !== 'Eloquent') {
            $class = '\LaravelComposer\Receipes\Packages\ORMReceipes\\' . $result . 'Receipe';
            $composer->addReceipe(new $class($this->helper, $this->input, $this->output));
        }

        return true;
    }
}
