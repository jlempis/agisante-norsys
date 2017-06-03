<?php

namespace Gestime\CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Gestime\CoreBundle\Business\Utilities;

/**
 * Vérification des chevauchements de dates
 *
 */
class UniquePeriodeValidator extends ConstraintValidator
{
    private $utils;
    private $entityManager;

    /**
     * [__construct description]
     * @param Utilities $utils
     * @param [type]    $entityManager
     */
    public function __construct(Utilities $utils, $entityManager)
    {
        $this->utils = $utils;
        $this->entityManager = $entityManager;
    }

    private function getPeriodesAComparer($periode, $idPeriodeEnCours)
    {
        $tableauPeriodesAValider = array();
        if ($periode === null) {
            $repository = $this->entityManager->getRepository('GestimeCoreBundle:Fermeture');
            $allPeriodes = $repository->getAllPeriodesFermeture($idPeriodeEnCours);
            foreach ($allPeriodes as $periode) {
                $tableauPeriodesAValider[] = array( 'start' => $periode['datedebut']->format('Y-m-d ').$periode['heuredebut'],
                                                    'end' => $periode['datefin']->format('Y-m-d ').$periode['heurefin'], );
            }

            return($tableauPeriodesAValider);
        } else {
            return($periode);
        }
    }

    /**
     * [validate description]
     * validate value[0] = Date de debut de periode
     * validate value[1] = Date de fin de periode
     * validate value[2] = Liste des periodes à comparer
     * validate value[3] = id periode en cours
     * @param  array      $value
     * @param  Constraint $constraint
     * @return [type]
     */
    public function validate($value, Constraint $constraint)
    {
        if ($this->utils->time_overlap($value[0],
            $value[1],
            $this->getPeriodesAComparer($value[2], $value[3])
        )) {
            $this->context->addViolation($constraint->message, array('%string%' => $value[0]));
        }
    }
}
