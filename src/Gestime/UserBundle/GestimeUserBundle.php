<?php

namespace Gestime\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * GestimeUserBundle
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class GestimeUserBundle extends Bundle
{
    /**
     * getParent
     * @return string
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
