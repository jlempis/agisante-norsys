<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AdresseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdresseRepository extends EntityRepository
{
    /**
     * [findByAllFields description]
     * @param  [type] $complement [description]
     * @param  [type] $voie       [description]
     * @param  [type] $codePostal [description]
     * @param  [type] $ville      [description]
     * @return [type]             [description]
     */
    public function findByAllFields($complement, $voie, $codePostal, $ville)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.complement = :complement')
            ->andwhere('a.voie = :voie')
            ->andwhere('a.codePostal = :codePostal')
            ->andwhere('a.ville = :ville')

            ->setParameter('complement', $complement)
            ->setParameter('voie', $voie)
            ->setParameter('codePostal', $codePostal)
            ->setParameter('ville', $ville)

            ->getQuery()->getResult();
    }
}