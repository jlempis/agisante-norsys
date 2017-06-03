<?php

namespace Gestime\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniquePeriode extends Constraint
{
    /**
     * validatedBy
     * @return string
     */
    public function validatedBy()
    {
        return 'unique_periode';
    }

    public $message = 'Chevauchement de periode';
}
