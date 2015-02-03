<?php namespace LaravelComposer;

use LaravelComposer\Receipes\Receipe;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Composer
{

    /**
     * @var Receipe[]
     */
    private $receipes = [ ];

    /**
     * @var string
     */
    private $laravelVersion;

    /**
     * Construct a new Composer object
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        $this->input  = $input;
        $this->output = $output;
    }

    /**
     * Run all receipes.
     */
    public function run()
    {
        array_walk(
            $this->receipes,
            function (Receipe $receipe) {
                $result = $receipe->run($this->input, $this->output);

                if ($receipe->handle($this, $result) === false) {
                    return;
                }
            }
        );
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

    /**
     * Add a dependency to composer.json
     *
     * @param string $package
     * @param string $version
     * @param bool   $dev
     */
    public function addDependency($package, $version, $dev = false)
    {
        $devOption = $dev ? '--dev' : null;

        $command = "composer require $devOption --prefer-dist $package $version";

        $this->runCommand($command);
    }

    /**
     * Add a service provider to the application.
     *
     * @param string $name
     */
    public function addProvider($name)
    {
        $file     = $this->getConfigDir() . 'app.php';
        $contents = file_get_contents($file);
        $contents = str_replace("'providers' => [", "'providers' => [\n\t\t'$name',", $contents);
        $contents = str_replace("'providers' => array(", "'providers' => array(\n\t\t'$name',", $contents);
        file_put_contents($file, $contents);
    }

    /**
     * Add an alias to the laravel application
     *
     * @param string $name
     * @param string $class
     */
    public function addAlias($name, $class)
    {
        $file     = $this->getConfigDir() . 'app.php';
        $contents = file_get_contents($file);
        $contents = str_replace("'aliases' => [", "'aliases' => [\n\t\t'$name' => '$class',", $contents);
        $contents = str_replace("'aliases' => array(", "'aliases' => array(\n\t\t'$name' => '$class',", $contents);
        file_put_contents($file, $contents);
    }

    /**
     * Run a shell command.
     *
     * @param string $command
     * @param int    $timeout
     */
    public function runCommand($command, $timeout = 0)
    {
        $this->info("Running $command...");

        $process = new Process($command);

        $process->setTimeout($timeout);

        $process->run(
            function ($type, $buffer) {
                $this->output->write($buffer);
            }
        );

        if (!$process->isSuccessful()) {
            throw new RuntimeException();
        }
    }

    /**
     * Change the minimum stability to "dev" in composer.json
     */
    public function changeMinimumStability()
    {
        $file     = 'composer.json';
        $contents = file_get_contents($file);

        if (strpos($contents, 'minimum-stability') !== false) {
            $contents = str_replace(
                '"minimum-stability": "stable"',
                '"minimum-stability": "dev",' . "\n\t" . '"prefer-stable": true',
                $contents
            );
        } else {
            $contents = str_replace(
                '"config": {',
                '"minimum-stability": "dev",' . "\n\t" . '"prefer-stable": true,' . "\n\t" . '"config": {',
                $contents
            );
        }

        file_put_contents($file, $contents);
    }

    /**
     * @param string $result
     */
    public function setLaravelVersion($result)
    {
        $this->laravelVersion = $result;
    }

    /**
     * @return string
     */
    public function getLaravelVersion()
    {
        return $this->laravelVersion;
    }

    /**
     * @return string
     */
    public function getConfigDir()
    {
        return $this->laravelVersion[0] === '4' ? "app/config/" : "config/";
    }
}
