<?php

namespace Gestime\FormBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * GestimeFormBundle
 *
 */
class GestimeFormBundle extends Bundle
{
    /**
     * getParent
     * @return string
     */
    public function getParent()
    {
        return 'GenemuFormBundle';
    }
}
