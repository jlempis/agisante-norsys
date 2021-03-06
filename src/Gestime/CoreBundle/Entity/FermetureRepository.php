<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Gestime\CoreBundle\Entity\Repondeur;
use Doctrine\ORM\EntityRepository;

/**
 * FermetureRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FermetureRepository extends EntityRepository
{
    /**
     * [getAllPeriodesFermeture description]
     * @param  [type] $idFermeture [description]
     * @return [type]              [description]
     */
    public function getAllPeriodesFermeture($idFermeture)
    {
        $qbAllPeriodesFermeture = $this->createQueryBuilder('f')
            ->select('f.datedebut, f.heuredebut, f.datefin, f.heurefin');

        if ($idFermeture !== null) {
            $qbAllPeriodesFermeture->where('f.idFermeture != :idFermeture')
            ->setParameter('idFermeture', $idFermeture);
        }

        return $qbAllPeriodesFermeture->getQuery()->getResult();
    }

    /**
     * [hasRepondeur description]
     * @param Repondeur $repondeur
     * @return boolean
     */
    public function hasRepondeur(Repondeur $repondeur)
    {
        $qb = $this->createQueryBuilder('a')
        ->select('count(a.idFermeture)')
        ->where('a.repondeur = :repondeur');

        $qb->setParameter('repondeur', $repondeur);

        return (intval($qb->getQuery()->getSingleScalarResult()) >0);
    }
}
