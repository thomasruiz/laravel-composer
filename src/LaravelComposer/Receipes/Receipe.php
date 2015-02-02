<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;

interface Receipe
{

    /**
     * Run the receipe.
     *
     * @return string|bool
     */
    public function run();

    /**
     * Handle the result. Return false if the composer should stop.
     *
     * @param Composer    $composer
     * @param string|bool $result
     *
     * @return bool
     */
    public function handle(Composer $composer, $result);
}
