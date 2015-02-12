<?php namespace LaravelComposer\Receipes;

use LaravelComposer\Composer;

class VersionReceipe extends AbstractReceipe
{

    /**
     * Run the receipe.
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
        $composer->runCommand("composer create-project laravel/laravel $appName $result --prefer-dist");

        chdir($appName);

        $composer->changeMinimumStability();

        if ($result !== '4.2') {
            $composer->runCommand("php artisan app:name $appName");
        }

        $composer->setLaravelVersion($result);

        return true;
    }
}
