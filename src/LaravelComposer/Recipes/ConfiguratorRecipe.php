<?php namespace LaravelComposer\Recipes;

use LaravelComposer\Composer;
use LaravelComposer\Recipes\Packages\DatabaseRecipe;
use LaravelComposer\Recipes\Packages\ORMRecipe;

class ConfiguratorRecipe extends AbstractRecipe
{

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask('Would you like to configure your new application? (Y/n) ');
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
            $composer->addRecipe(new ORMRecipe($this->helper, $this->input, $this->output));
            $composer->addRecipe(new DatabaseRecipe($this->helper, $this->input, $this->output));

            return true;
        }

        return false;
    }
}
