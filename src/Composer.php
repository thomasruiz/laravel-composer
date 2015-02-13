<?php namespace LaravelComposer;

use LaravelComposer\Recipes\Recipe;
use RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Composer
{

    /**
     * @var Recipe[]
     */
    private $recipes = [ ];

    /**
     * @var string
     */
    private $laravelVersion;

    /**
     * @var int
     */
    private $currentRecipeOffset = 0;

    /**
     * @var string
     */
    private $appName;

    /**
     * @var string
     */
    private $ORM;

    /**
     * @var bool
     */
    private $databaseGeneration = false;

    /**
     * @var string
     */
    private $ansi;

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
     * Run all recipes.
     *
     * @param int $currentIndex
     *
     * @return bool
     */
    public function run($currentIndex = 0)
    {
        $nextIndex = $currentIndex + 1;

        $this->currentRecipeOffset = $nextIndex;

        $result = $this->recipes[ $currentIndex ]->run($this->input, $this->output);

        if ($this->recipes[ $currentIndex ]->handle($this, $result) === true) {
            isset( $this->recipes[ $nextIndex ] ) && $this->run($nextIndex);
        }
    }

    /**
     * Add a recipe.
     *
     * @param Recipe $recipe
     */
    public function addRecipe(Recipe $recipe)
    {
        array_splice($this->recipes, $this->currentRecipeOffset++, 0, [ $recipe ]);
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

        $command = "composer require $devOption --prefer-dist --no-update $package $version";

        $this->runCommand($command);
        $this->runCommand("composer update --prefer-dist --no-scripts");
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

        $process = new Process($command . ' ' . $this->ansi);

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
     * Allow the migrations to be ran.
     */
    public function allowDatabaseMigration()
    {
        $this->databaseGeneration = true;
    }

    /**
     * Run the database migrate command depending of the orm.
     */
    public function migrateDatabase()
    {
        if ($this->databaseGeneration) {
            $commands = [
                'Eloquent' => 'migrate',
                'Doctrine' => 'doctrine:schema:create',
            ];

            $this->runCommand('php artisan ' . $commands[ $this->getORM() ]);
        }
    }

    /**
     * @param string $stubName
     *
     * @return string
     */
    public function stub($stubName)
    {
        $version = $this->getLaravelVersion()[0] === '4' ? 'V4' : 'V5';
        $source  = __DIR__ . '/../../stubs/' . $version . '/' . $stubName . '.php';
        $target  = __DIR__ . '/../../build/' . $version . '/' . $stubName . '.php';

        $this->buildStub($source, $target);

        return $target;
    }

    /**
     * @param $source
     * @param $target
     */
    private function buildStub($source, $target)
    {
        $targetDirectory = substr($target, 0, strrpos($target, '/'));
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $contents = file_get_contents($source);
        file_put_contents($target, str_replace('__NAMESPACE__', $this->getAppName(), $contents));
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

    /**
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @param string $appName
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
    }

    /**
     * @return string
     */
    public function getORM()
    {
        return $this->ORM;
    }

    /**
     * @param string $ORM
     */
    public function setORM($ORM)
    {
        $this->ORM = $ORM;
    }

    /**
     * @param bool $isDecorated
     */
    public function setAnsi($isDecorated)
    {
        $this->ansi = $isDecorated ? '--ansi' : '--no-ansi';
    }
}
