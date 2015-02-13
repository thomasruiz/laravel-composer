<?php namespace LaravelComposer\Recipes;

use LaravelComposer\Composer;

class VersionRecipe extends AbstractRecipe
{

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask('What version of laravel do you need? [default: 5.0]', [ '4.2', '5.0' ], 1);
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
        $appName = $composer->getAppName();
        $composer->runCommand("composer create-project laravel/laravel $appName ~$result --prefer-dist --no-scripts");

        chdir($appName);

        $composer->changeMinimumStability();

        if ($result[0] !== '4') {
            $composer->runCommand("php -r \"copy('.env.example', '.env');\"");
            $composer->runCommand("php artisan key:generate");
            $composer->runCommand("php artisan app:name $appName");
        } else {
            $composer->runCommand("php artisan key:generate");
        }


        $composer->setLaravelVersion($result);

        return true;
    }
}
