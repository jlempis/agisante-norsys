<?php

namespace Gestime\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class HourFormat extends Constraint
{
    public $message = 'L\' heure doit être au format hh:nn. La valeur lue est "%string%".';
}
