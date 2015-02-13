<?php namespace LaravelComposer\Recipes\Packages;

use LaravelComposer\Composer;
use LaravelComposer\Recipes\AbstractRecipe;
use LaravelComposer\Recipes\Packages\DatabaseRecipes\DatabaseConfiguratorRecipe;
use LaravelComposer\Recipes\Packages\DatabaseRecipes\DatabaseGeneratorRecipe;

class DatabaseRecipe extends AbstractRecipe
{

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask('Do you want to configure the database? (Y/n) ');
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
            $composer->addRecipe(new DatabaseConfiguratorRecipe($this->helper, $this->input, $this->output));
            $composer->addRecipe(new DatabaseGeneratorRecipe($this->helper, $this->input, $this->output));
        }

        return true;
    }
}
