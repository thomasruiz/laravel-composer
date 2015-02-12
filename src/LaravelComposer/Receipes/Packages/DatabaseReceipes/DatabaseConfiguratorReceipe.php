<?php namespace LaravelComposer\Receipes\Packages\DatabaseReceipes;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\AbstractReceipe;

class DatabaseConfiguratorReceipe extends AbstractReceipe
{

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return true;
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
        return true;
    }
}
