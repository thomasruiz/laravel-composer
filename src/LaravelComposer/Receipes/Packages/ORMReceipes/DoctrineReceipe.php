<?php namespace LaravelComposer\Receipes\Packages\ORMReceipes;

use LaravelComposer\Composer;
use LaravelComposer\Receipes\AbstractReceipe;

class DoctrineReceipe extends AbstractReceipe
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
        if ($composer->getLaravelVersion()[0] === '4') {
            $this->handleV4($composer);
        } else {
            $this->handleV5($composer);
        }

        $composer->addProvider('Mitch\LaravelDoctrine\LaravelDoctrineServiceProvider');
        $composer->addAlias('EntityManager', 'Mitch\LaravelDoctrine\EntityManagerFacade');

        return true;
    }

    /**
     * @param Composer $composer
     */
    private function handleV4(Composer $composer)
    {
        $composer->addDependency('mitchellvanw/laravel-doctrine', '0.5.*');

        $configPath = $composer->getConfigDir();
        $this->addDoctrineConfig($configPath, 'app/models/');
        $this->changeAuthConfig($configPath, 'User', 'User');

        copy($composer->stub('Doctrine/Entity'), 'app/models/Entity.php');
        copy($composer->stub('Doctrine/User'), 'app/models/User.php');
    }

    /**
     * @param Composer $composer
     */
    private function handleV5(Composer $composer)
    {
        mkdir('app/Entities', 0755);

        $composer->addDependency('mitchellvanw/laravel-doctrine', 'dev-develop');

        $configPath = $composer->getConfigDir();
        $this->addDoctrineConfig($configPath, 'app/Entities/');
        $this->changeAuthConfig($configPath, 'mynewapp\\User', 'mynewapp\\Entities\\User');

        unlink('app/User.php');
        copy($composer->stub('Doctrine/Entity'), 'app/Entities/Entity.php');
        copy($composer->stub('Doctrine/User'), 'app/Entities/User.php');
        copy($composer->stub('Doctrine/PasswordReset'), 'app/Entities/PasswordReset.php');
        copy($composer->stub('Doctrine/Registrar'), 'app/Services/Registrar.php');
    }

    /**
     * @param string $configPath
     * @param string $oldModel
     * @param string $newModel
     */
    private function changeAuthConfig($configPath, $oldModel, $newModel)
    {
        $auth = file_get_contents("$configPath/auth.php");

        $auth = str_replace(
            [ "'driver' => 'eloquent'", "'model' => '$oldModel'" ],
            [ "'driver' => 'doctrine'", "'model' => '$newModel'" ],
            $auth
        );

        file_put_contents($configPath . '/auth.php', $auth);
    }

    /**
     * @param string $configPath
     * @param string $pathToEntities
     */
    private function addDoctrineConfig($configPath, $pathToEntities)
    {
        $config = file_get_contents('vendor/mitchellvanw/laravel-doctrine/config/doctrine.php');

        $config = str_replace('// Paths to entities here...', "'$pathToEntities',", $config);

        file_put_contents("$configPath/doctrine.php", $config);
    }
}
