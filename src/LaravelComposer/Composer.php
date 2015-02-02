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
     * Construct a new Composer object
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Run all receipes.
     */
    public function run()
    {
        foreach ($this->receipes as $receipe) {
            $result = $receipe->run($this->input, $this->output);

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

    /**
     * @param string $message
     */
    public function info($message)
    {
        $this->output->writeln("<fg=green>$message</fg=green>");
    }
}
