<?php

namespace Seb\SamlBundle\Tests\Security;

use PHPUnit\Framework\TestCase;
use Seb\SamlBundle\Security\SamlAttributesStorage;

class SamlAttributesStorageTest extends TestCase
{

    private $attributeStorage;

    protected function setUp(): void
    {
        $this->attributeStorage = new SamlAttributesStorage();
    }

    public function testSetAndGetAttributes()
    {
        $attributes = [
            'attr' => 'value',
            'array_attr' => [ 'array_value' ],
        ];

        $this->attributeStorage->setAttributes('username', $attributes);

        $this->assertEquals($attributes, $this->attributeStorage->getAttributes('username'));
    }
}