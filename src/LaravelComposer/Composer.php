<?php namespace LaravelComposer;

use LaravelComposer\Receipes\Receipe;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Composer
{

    /**
     * @var Receipe[]
     */
    private $receipes = [ ];

    /**
     * Run all receipes.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->receipes as $receipe) {
            $result = $receipe->run($input, $output);

            if ($receipe->handle($this, $result) === false) {
                return;
            }
        }
    }

    /**
     * Add a receipe.
     *
     * @param Receipe $receipe
     */
    public function addReceipe(Receipe $receipe)
    {
        $this->receipes[] = $receipe;
    }
}
