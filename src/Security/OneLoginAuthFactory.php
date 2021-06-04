<?php

namespace Seb\SamlBundle\Security;

use OneLogin\Saml2\Auth;
use OneLogin\Saml2\Utils;

class OneLoginAuthFactory
{
    public $oneLoginSettings;
    public $trustProxy;

    public function __construct($oneLoginSettings, $config)
    {
        $this->oneLoginSettings = $oneLoginSettings;
        $this->trustProxy = $config['trust_proxy'];
    }

    public function createOneLoginAuth()
    {
        Utils::setProxyVars($this->trustProxy);

        return new Auth($this->oneLoginSettings);
    }
}
