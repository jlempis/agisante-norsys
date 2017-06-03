<?php

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{
    /**
     * [createQueryListMessage description]
     * @param [type] $user          [description]
     * @param [type] $sens          [description]
     * @param [type] $medecinIds    [description]
     * @param [type] $statutLecture [description]
     * @param [type] $filtre        [description]
     * @return [type]                [description]
     */
    private function createQueryListMessage($user, $sens, $medecinIds, $statutLecture, $filtre)
    {
        $query = $this->createQueryBuilder('m')
            ->leftjoin('m.medecins', 'med')
            ->leftjoin('m.categories', 'cat')
            ->leftjoin('m.favoris', 'fav')
            ->where('m.site = :site')
            ->setParameter('site', $user->getSite());

        if (!is_null($medecinIds)) {
            $query->andwhere('med.idMedecin IN (:medecinIds)')
                    ->setParameter('medecinIds', $medecinIds);
        }

        switch ($filtre) {
            case 'Reception':
                $query->andwhere('m.etat= :valide')
                      ->andwhere('m.sens= :sens')
                      ->setParameter('valide', 'V')
                      ->setParameter('sens', $sens);

                break;
            case 'Favoris':
                $query->andwhere('fav.id= :utilisateurId')
                      ->andwhere('m.etat= :valide')
                      ->setParameter('utilisateurId', $user->getId())
                      ->setParameter('valide', 'V');
                break;
            case 'Envoyes':
                $query->andwhere('m.etat= :valide')
                      ->andwhere('m.sens= :sensEmission')
                      ->setParameter('valide', 'V')
                      ->setParameter('sensEmission', $sens);
                break;
            case 'Supprimes':
                $query->andwhere('m.etat= :annule')
                      ->setParameter('annule', 'A');
                break;
            default:
                //Filtre par categorie
                $query->andwhere('cat.nom= :categorie')
                      ->setParameter('categorie', $filtre);
                break;
        }
        switch ($statutLecture) {
            case 'l':
                $query->andwhere('m.lu= :lu')
                    ->setParameter('lu', true);
                break;
            case 'n':
                $query->andwhere('m.lu= :lu')
                    ->setParameter('lu', false);
                break;
        }
        $query->add('orderBy', 'm.dateEnvoi DESC');

        return $query;
    }

    /**
     * [getMessagesSearch description]
     * @param [type] $user          [description]
     * @param [type] $sens          [description]
     * @param [type] $medecinIds    [description]
     * @param [type] $statutLecture [description]
     * @param [type] $texte         [description]
     * @return [type]                [description]
     */
    private function getMessagesSearch($user, $sens, $medecinIds, $statutLecture, $texte)
    {
        $qbSearchMessage = $this->createQueryBuilder('m')
            ->leftjoin('m.medecins', 'med')
            ->leftjoin('m.categories', 'cat')
            ->where('m.site = :site')
            ->andwhere('m.etat= :valide')
            ->andwhere('m.sens= :sens');

        if (!is_null($medecinIds)) {
            $qbSearchMessage->andwhere('med.idMedecin IN (:medecinIds)')
              ->setParameter('medecinIds', $medecinIds);
        }

        $orModule = $qbSearchMessage->expr()->orx();
        $orModule->add($qbSearchMessage->expr()->like('m.sujet', ':texte'));
        $orModule->add($qbSearchMessage->expr()->like('m.objet', ':texte'));
        $qbSearchMessage->andWhere($orModule);

        switch ($statutLecture) {
            case 'l':
                $qbSearchMessage->andwhere('m.lu= :lu')
                    ->setParameter('lu', true);
                break;
            case 'n':
                $qbSearchMessage->andwhere('m.lu= :lu')
                    ->setParameter('lu', false);
                break;
        }

        $qbSearchMessage->setParameter('site', $user->getSite())
                      ->setParameter('sens', $sens)
                      ->setParameter('valide', 'V')
                      ->setParameter('texte', '%'.$texte.'%');

        return $qbSearchMessage;
    }

    /**
     * [createQuerySearch description]
     * @param [type] $textSearch    [description]
     * @param [type] $sens          [description]
     * @param [type] $medecinIds    [description]
     * @param [type] $statutLecture [description]
     * @param [type] $user          [description]
     * @param [type] $filtre        [description]
     * @return [type]                [description]
     */
    private function createQuerySearch($textSearch, $sens, $medecinIds, $statutLecture, $user, $filtre)
    {
        if ($textSearch) {
            $query = $this->getMessagesSearch($user, $sens, $medecinIds, $statutLecture, $filtre);
        } else {
            $query = $this->createQueryListMessage($user, $sens, $medecinIds, $statutLecture, $filtre);
        }

        return $query;
    }

    /**
     * Rechereche de message selon criteres (Liste paginée)
     * @param  [type]  $textSearch    Vrai si Recherche de texte, faux sinon
     * @param  [type]  $sens          [description]
     * @param  [type]  $medecinIds    [description]
     * @param  [type]  $statutLecture t=tous,l=lus, n=nonlus
     * @param  [type]  $user          [description]
     * @param  [type]  $filtre        Texte de recherche ou categorie de recherche
     * @param  integer $page          page en cours
     * @param  integer $maxperpage    Nb messages par page
     * @return [type]                 [description]
     */
    public function getLisPagineetMessages($textSearch, $sens, $medecinIds, $statutLecture, $user, $filtre, $page = 1, $maxperpage = 3)
    {
        $query = $this->createQuerySearch($textSearch, $sens, $medecinIds, $statutLecture, $user, $filtre)
            ->setFirstResult(($page-1) * $maxperpage)
            ->setMaxResults($maxperpage)
            ->getQuery();

        return new Paginator($query, $fetchJoinCollection = true);
    }

    /**
     * [getCountMessages description]
     * @param [type] $textSearch    [description]
     * @param [type] $sens          [description]
     * @param [type] $medecinIds    [description]
     * @param [type] $statutLecture [description]
     * @param [type] $user          [description]
     * @param [type] $filtre        [description]
     * @return [type]                [description]
     */
    public function getCountMessages($textSearch, $sens, $medecinIds, $statutLecture, $user, $filtre)
    {
        return count($this->createQuerySearch($textSearch, $sens, $medecinIds, $statutLecture, $user, $filtre)
            ->getQuery()
            ->getResult());
    }

    /**
     * [getListeMessages description]
     * @param  [type] $medecinId [description]
     * @param  [type] $debut     [description]
     * @param  [type] $fin       [description]
     * @return [type]            [description]
     */
    public function getListeMessages($medecinId, $debut, $fin)
    {
        $qb = $this->createQueryBuilder('m')
                    ->leftjoin('m.medecins', 'med')
                    ->where('m.etat= :valide')
                    ->andwhere('m.dateEnvoi >= :datedebut')
                    ->andwhere('m.dateEnvoi <= :datefin')
                    ->setParameter('datedebut', $debut)
                    ->setParameter('datefin', $fin)
                    ->setParameter('valide', 'V');

        if ($medecinId >0) {
            $qb->andwhere('med.idMedecin = :medecinId')
            ->setParameter('medecinId', $medecinId);
        }

        return $qb;
    }

    /**
     * [getCountMessagesNonLus description]
     * @param [type] $user        [description]
     * @param [type] $sens        [description]
     * @param [type] $medecinsIds [description]
     * @return [type]              [description]
     */
    public function getCountMessagesNonLus($user, $sens, $medecinsIds)
    {
        $qbnonLus = $this->createQueryBuilder('m')
                    ->select('count(m.idMessage)')
                    ->leftjoin('m.medecins', 'med')
                    ->where('m.site = :site')
                    ->andwhere('m.lu= :faux')
                    ->andwhere('m.etat= :valide')
                    ->andwhere('m.sens= :sens')
                    ->setParameter('valide', 'V')
                    ->setParameter('site', $user->getSite())
                    ->setParameter('faux', false)
                    ->setParameter('sens', $sens);

        if (!is_null($medecinsIds)) {
            $qbnonLus->andwhere('med.idMedecin IN (:medecinIds)')
              ->setParameter('medecinIds', $medecinsIds);
        }

        $nonLus = $qbnonLus->getQuery()
                    ->getSingleScalarResult();

        $qbnonLusFavoris = $this->createQueryBuilder('m')
                    ->select('count(m.idMessage)')
                    ->leftjoin('m.medecins', 'med')
                    ->leftjoin('m.favoris', 'fav')
                    ->where('m.site = :site')
                    ->andwhere('m.lu= :faux')
                    ->andwhere('m.etat= :valide')
                    ->andwhere('fav.id= :utilisateurId')
                    ->setParameter('valide', 'V')
                    ->setParameter('site', $user->getSite())
                    ->setParameter('faux', false)
                    ->setParameter('utilisateurId', $user->getId());

        if (!is_null($medecinsIds)) {
            $qbnonLusFavoris->andwhere('med.idMedecin IN (:medecinIds)')
              ->setParameter('medecinIds', $medecinsIds);
        }

        $nonLusFavoris = $qbnonLusFavoris->getQuery()
                    ->getSingleScalarResult();

        return (array('nonlus' => $nonLus, 'nonluFavoris' => $nonLusFavoris));
    }

    /**
     * Renvoie la reponse à un message
     * @param message $message
     * @return query
     */
    public function getMessageResponse($message)
    {
        return  $this->createQueryBuilder('m')
            ->where('m.msgOrigine = :message')
            ->setParameter('message', $message)
            ->getQuery()
            ->getResult();
    }
}