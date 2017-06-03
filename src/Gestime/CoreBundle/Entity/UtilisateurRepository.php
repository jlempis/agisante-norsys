<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * utilisateurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UtilisateurRepository extends EntityRepository
{
    /**
     * [getListUsers description]
     * @param  [type] $site [description]
     * @return [type]       [description]
     */
    public function getListUsers($site)
    {
        $qb = $this->createQueryBuilder('u')
        ->select('u.id as u_id,
                  u.nom as u_nom,
                  u.prenom as u_prenom ,
                  u.email as u_email,
                  u.username as u_username,
                  GroupConcat(g.name) as gname')
        ->leftjoin('u.groups', 'g')
        ->where('u.site = :site')
        ->groupBy('u.id')
        ->setParameter('site', $site);

        return $qb;
    }

    /**
     * [getListMedecins]
     * @param Utilisateur $user
     * @return ArrayResult
     */
    public function getListMedecins($user)
    {
        $qb = $this->createQueryBuilder('u')
        ->select('m.idMedecin')
        ->leftjoin('u.medecins', 'm')
        ->where('u.site = :site')
        ->andwhere('u.id = :userId')
        ->setParameter('userId', $user->getId())
        ->setParameter('site', $user->getSite());

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * [getInfoApi description]
     * @param string $username
     * @return ArrayResult
     */
    public function getInfoApi($username)
    {
        $qb = $this->createQueryBuilder('u')
        ->select('u.username, u.salt, s.id')
        ->leftjoin('u.site', 's')
        ->where('u.username = :username')
        ->setParameter('username', $username);

        return $qb->getQuery()->getArrayResult();
    }
}