<?php namespace LaravelComposer\Recipes\Packages;

use LaravelComposer\Composer;
use LaravelComposer\Recipes\AbstractRecipe;

class CashierRecipe extends AbstractRecipe
{

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        return $this->ask('Would you like to use cashier for payments? (y/N) ', false);
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
            $composer->addDependency('laravel/cashier', $composer->getLaravelVersion()[0] === '4' ? '~2.0' : '~3.0');
            $composer->addProvider('Laravel\Cashier\CashierServiceProvider');

            if ($composer->getLaravelVersion()[0] === '4') {
                $file = 'app/models/User.php';

                $uses = <<<USES
use Laravel\Cashier\BillableInterface;

class
USES;

                $implements = 'implements BillableInterface, ';
            } else {
                $file = $composer->getORM() === 'Eloquent' ? 'app/User.php' : 'app/Entities/User.php';

                $uses = <<<USES
use Laravel\Cashier\Contracts\Billable as BillableContract;

class
USES;

                $implements = 'implements BillableContract, ';
            }

            $userModel = file_get_contents($file);
            $traits    = '\1use Billable, ';

            if ($composer->getORM() === 'Eloquent') {
                $properties = "    protected \$dates = ['trial_ends_at', 'subscription_ends_at'];";
                $traitUse   = $composer->getLaravelVersion()[0] === '4' ? 'Trait as Billable' : '';
                $uses       = "use Laravel\\Cashier\\Billable$traitUse;\n$uses";
                $composer->runCommand('php artisan cashier:table users');
                $userModel = str_replace('}', "{$properties}\n}", $userModel);
            } else {
                copy($composer->stub('Cashier/Billable'), 'app/Entities/Billable.php');
            }

            $userModel = str_replace('class', $uses, $userModel);
            $userModel = str_replace('implements ', $implements, $userModel);
            $userModel = preg_replace('/([ \t]+)use /', $traits, $userModel);
            file_put_contents($file, $userModel);

            $composer->migrateDatabase();
        }
    }
}
