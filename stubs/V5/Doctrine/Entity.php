<?php namespace mynewapp\Entities;

abstract class Entity
{

    /**
     * Get a property from the Entity
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->{'get' . ucfirst($name)}();
    }
}
