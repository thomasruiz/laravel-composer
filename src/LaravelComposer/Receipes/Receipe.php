<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface Receipe
{

    /**
     * Run the receipe.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return string|bool
     */
    public function run(InputInterface $input, OutputInterface $output);

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
