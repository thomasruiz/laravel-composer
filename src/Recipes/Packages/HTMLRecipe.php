<?php namespace LaravelComposer\Recipes\Packages;

use LaravelComposer\Composer;
use LaravelComposer\Recipes\AbstractRecipe;

class HTMLRecipe extends AbstractRecipe
{

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask('Would you like to use Laravel/HTML helpers? (Y/n) ');
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
            $composer->addDependency('illuminate/html', '~5.0');
            $composer->addProvider('Illuminate\Html\HtmlServiceProvider');
            $composer->addAlias('Form', 'Illuminate\Html\FormFacade');
            $composer->addAlias('HTML', 'Illuminate\Html\HtmlFacade');
        }

        return true;
    }
}
