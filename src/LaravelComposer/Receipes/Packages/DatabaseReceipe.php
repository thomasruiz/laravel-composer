<?php namespace LaravelComposer\Receipes\Packages;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\AbstractReceipe;
use LaravelComposer\Receipes\Packages\DatabaseReceipes\DatabaseConfiguratorReceipe;

class DatabaseReceipe extends AbstractReceipe
{

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask('Do you want to configure the database? (Y/n) ');
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
