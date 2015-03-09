<?php

namespace RCloud\Bundle\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class RCloudUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
