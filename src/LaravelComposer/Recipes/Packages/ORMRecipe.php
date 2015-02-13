<?php namespace LaravelComposer\Recipes\Packages;

use LaravelComposer\Composer;
use LaravelComposer\Recipes\AbstractRecipe;

class ORMRecipe extends AbstractRecipe
{

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask('Which ORM would you like to use? [default: Eloquent]', [ 'Eloquent', 'Doctrine' ], 0);
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
        if ($result !== 'Eloquent') {
            $class = '\LaravelComposer\Recipes\Packages\ORMRecipes\\' . $result . 'Recipe';
            $composer->addRecipe(new $class($this->helper, $this->input, $this->output));
        }

        return true;
    }
}
