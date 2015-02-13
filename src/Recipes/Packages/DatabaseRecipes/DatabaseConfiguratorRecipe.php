<?php namespace LaravelComposer\Recipes\Packages\DatabaseRecipes;

use LaravelComposer\Composer;
use LaravelComposer\Recipes\AbstractRecipe;

class DatabaseConfiguratorRecipe extends AbstractRecipe
{

    /**
     * Run the recipe.
     *
     * @return string|bool
     */
    public function run()
    {
        $driver = $this->ask('Driver? [default: mysql] ', [ 'mysql', 'pgsql', 'sqlsrv', 'sqlite' ]);

        if ($driver === 'sqlite') {
            return $driver;
        }

        return [
            'default'  => $driver,
            'hostname' => $this->ask('Hostname? [default: localhost] ', 'localhost'),
            'username' => $this->ask('Username? [default: homestead] ', 'homestead'),
            'password' => $this->ask('Password? [default: secret] ', 'secret'),
            'database' => $this->ask('Database? [default: homestead] ', 'homestead'),
        ];
    }

    /**
     * Handle the result. Return false if the composer should stop.
     *
     * @param Composer        $composer
     * @param string|string[] $result
     *
     * @return bool
     */
    public function handle(Composer $composer, $result)
    {
        $version = $composer->getLaravelVersion()[0];

        $defaults = $this->getDefaults();
        if (is_array($result)) {
            $this->addPrefixTo($defaults, $version);
            $this->addPrefixTo($result, $version);

            $file = $version === '4' ? $composer->getConfigDir() . 'local/database.php' : '.env';
            $this->replaceInFile($file, $defaults, $result);

            $result = $result['default'];
        }

        $file = $composer->getConfigDir() . 'database.php';
        $this->replaceInFile($file, $defaults['default'], $result);

        return true;
    }

    /**
     * Default database values.
     *
     * @return string[]
     */
    private function getDefaults()
    {
        return [
            'default'  => 'mysql',
            'hostname' => 'localhost',
            'username' => 'homestead',
            'password' => 'secret',
            'database' => 'homestead',
        ];
    }

    /**
     * @param string[] &$config
     * @param string   $version
     */
    private function addPrefixTo(&$config, $version)
    {
        $extraSpace = $config['default'] === 'mysql' ? ' ' : '';

        $config['default'] = "'default' => '$config[default]'";
        if ($version === '4') {
            $config['hostname'] = "'host'     $extraSpace=> '$config[hostname]'";
            $config['database'] = "'database' $extraSpace=> '$config[database]'";
            $config['username'] = "'username' $extraSpace=> '$config[username]'";
            $config['password'] = "'password' $extraSpace=> '$config[password]'";
        } else {
            $config['hostname'] = "DB_HOST=$config[hostname]";
            $config['database'] = "DB_DATABASE=$config[database]";
            $config['username'] = "DB_USERNAME=$config[username]";
            $config['password'] = "DB_PASSWORD=$config[password]";
        }
    }

    /**
     * @param string          $file
     * @param string|string[] $from
     * @param string|string[] $to
     */
    private function replaceInFile($file, $from, $to)
    {
        $config = file_get_contents($file);
        file_put_contents($file, str_replace($from, $to, $config));
    }
}
