<?php

namespace Gestime\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Helper pour la validation des heures
 *
 */
class HourFormatValidator extends ConstraintValidator
{
    /**
     * [validate description]
     * @param  string     $value
     * @param  Constraint $constraint
     * @return void
     */
    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/(2[0-3]|[01][0-9]):[0-5][0-9]/', $value)) {
            $this->context->addViolation($constraint->message, array('%string%' => $value));
        }
    }
}
