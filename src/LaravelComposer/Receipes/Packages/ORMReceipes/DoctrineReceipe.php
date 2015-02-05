<?php namespace LaravelComposer\Receipes\Packages\ORMReceipes;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\Receipe;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DoctrineReceipe implements Receipe
{

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Construct a new DoctrineReceipe object
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
        mkdir('app/Entities');

        if ($composer->getLaravelVersion()[0] === '4') {
            $this->handleV4($composer);
        } else {
            $this->handleV5($composer);
        }

        $composer->addProvider('Mitch\LaravelDoctrine\LaravelDoctrineServiceProvider');
        $composer->addAlias('EntityManager', 'Mitch\LaravelDoctrine\EntityManagerFacade');
    }

    /**
     * @param Composer $composer
     */
    public function handleV4(Composer $composer)
    {
        $composer->addDependency('mitchellvanw/laravel-doctrine', '0.5.*');

        $composer->runCommand(
            'php artisan config:publish mitch/laravel-doctrine --path=vendor/mitch/laravel-doctrine/config'
        );
    }

    /**
     * @param Composer $composer
     */
    public function handleV5(Composer $composer)
    {
        $composer->addDependency('mitchellvanw/laravel-doctrine', 'dev-develop');

        $config = file_get_contents('vendor/mitchellvanw/laravel-doctrine/config/doctrine.php');
        $config = str_replace('// Paths to entities here...', "'app/Entities/',", $config);
        file_put_contents('config/doctrine.php', $config);

        unlink('app/User.php');
        copy($composer->stub('V5/Doctrine/Entity'), 'app/Entities/Entity.php');
        copy($composer->stub('V5/Doctrine/User'), 'app/Entities/User.php');
    }
}
