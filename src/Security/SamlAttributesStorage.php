<?php

namespace Seb\SamlBundle\Security;

class SamlAttributesStorage
{
    private $attributes = [];

    public function setAttributes($username, $attributes)
    {
        $this->attributes[$username] = $attributes;
    }

    public function getAttributes($username)
    {
        return $this->attributes[$username];
    }
}
