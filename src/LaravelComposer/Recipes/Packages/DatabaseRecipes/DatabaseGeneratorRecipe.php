<?php namespace LaravelComposer\Recipes\Packages\DatabaseRecipes;

use LaravelComposer\Composer;
use LaravelComposer\Recipes\AbstractRecipe;

class DatabaseGeneratorRecipe extends AbstractRecipe
{

    /**
     * @var array
     */
    private $commands = [
        'Eloquent' => 'migrate',
        'Doctrine' => 'doctrine:schema:create',
    ];

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask("Do you want to generate the default database now? (y/N) ", false);
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
        if ($result) {
            $composer->runCommand('php artisan ' . $this->commands[ $composer->getORM() ]);
        }

        return true;
    }
}
