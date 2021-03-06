<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * medecinRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MedecinRepository extends EntityRepository
{
    /**
     * allMedecinBySite
     * @param Site $site
     * @return querybuilder
     */
    private function allMedecinBySite($site)
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.site = :site')
            ->andwhere('m.agenda = 1')
            ->orderBy('m.generaliste', 'DESC')
            ->addOrderBy('m.nom', 'ASC')
            ->setParameter('site', $site);
    }

    /**
     * allMedecinByUser
     * @param int $userId
     * @return querybuilder
     */
    private function allMedecinByUser($userId)
    {

        $medecins =  $this->createQueryBuilder('m')
            ->select('m')
            ->leftjoin('m.utilisateurs', 'u')
            ->leftjoin('u.medecindefault', 'd')
            ->where('u.id = :userId')
            ->andwhere('m.agenda = 1')
            ->orderBy('m.idMedecin-d.idMedecin', 'DESC')
            ->addOrderBy('m.generaliste', 'DESC')
            ->addOrderBy('m.nom', 'ASC')
            ->setParameter('userId', $userId);

        return $medecins;
    }

    /**
     * [getListMedecinsRemplacants description]
     * @param Site $site
     * @return querybuilder
     */
    public function getListMedecinsRemplacants($site)
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.remplacant = :vrai')
            ->andwhere('m.site = :site')
            ->setParameter('vrai', true)
            ->setParameter('site', $site);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getListMedecinsSpecialites()
    {
      return $this->createQueryBuilder('m')
        ->select('m,s')
        ->leftJoin('m.specialites', 's')
        ->getQuery()
        ->getResult();
    }

    /**
     * [getListMedecins description]
     * @param Site $site
     * @return querybuilder array
     */
    public function getListMedecins($site)
    {
        return $this->createQueryBuilder('m')
            ->select('m.idMedecin as m_idMedecin,
                      m.nom as m_nom,
                      m.prenom as m_prenom,
                      m.remplacant as m_remplacant,
                      m.generaliste as m_generaliste,
                      m.dureeRdv as m_dureeRdv,
                      m.abonneSms as m_abonneSms,
                      a.raisonSociale as a_raisonSociale')
            ->leftjoin('m.abonne', 'a')
            ->where('m.site = :site')
            ->setParameter('site', $site);
    }

    /**
     * getObjectListMedecins (Utilisé dans la commende Aabsences:feries)
     * @param Site    $site
     * @param integer $rappels Nb Heures avant rappel
     * @return querybuilder
     */
    public function getObjectListMedecins($site, $rappels='N')
    {
        $qb = $this->createQueryBuilder('m')
            ->where('m.site = :site')
            ->setParameter('site', $site);

        if ($rappels == 'O' ) {
            $qb->andwhere('m.abonneSms = 1');
        }

        return $qb;
    }

    /**
     * [getAnnotations description]
     * @param integer $idMedecin
     * @return querybuilder
     */
    public function getAnnotations($idMedecin)
    {
        return $this->createQueryBuilder('m')
            ->select('j.value as day , h.debut as start, h.fin, a.value as activite , c.value as cssClass,  h.texte as text')
            ->join('m.horaires', 'h')
            ->join('h.jour', 'j')
            ->join('h.activite', 'a')
            ->join('h.classe', 'c')
            ->where('m.idMedecin = :idMedecin')
            ->setParameter('idMedecin', $idMedecin)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * [getMedecinByNom description]
     * @param String $nom
     * @return querybuilder
     */
    public function getMedecinByNom($nom)
    {
        return $this->createQueryBuilder('m')
            ->select('m')
            ->where('m.nom = :nom')
            ->setParameter('nom', $nom);
    }

  /**
   * [getMedecinByNom description]
   * @param String $nom
   * @return querybuilder
   */
  public function getMedecinById($id)
  {
    return $this->createQueryBuilder('m')
      ->select('m')
      ->where('m.idMedecin = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getOneOrNullResult();
  }


    /**
     * [getDureeConsultation description]
     * @param integer $idMedecin
     * @return querybuilder
     */
    public function getDureeConsultation($idMedecin)
    {
        return $this->createQueryBuilder('m')
            ->select('m.dureeRdv')
            ->where('m.idMedecin = :idMedecin')
            ->setParameter('idMedecin', $idMedecin)
            ->getQuery()
            //->getSingleResult();
            ->getOneOrNullResult();
    }

    /**
     * [getNumeroSMSbyMedecin description]
     * @param integer $idMedecin
     * @return querybuilder
     */
    public function getNumeroSMSbyMedecin($idMedecin)
    {
        return $this->createQueryBuilder('m')
            ->select('t.numero')
            ->leftjoin('m.telephones', 't')
            ->where('t.envoiSMS = :envoisms')
            ->andwhere('m.idMedecin = :idMedecin')
            ->setParameter('envoisms', true)
            ->setParameter('idMedecin', $idMedecin);
    }

    /**
     * Get getMedecinsUser
     *
     * Retourne la liste des medecins du site de l'utilisateur
     * Utilisé pour les utilisateurs qui sont admin d'un site
     * Ils ont alors le droit de voir tous les medecins du site
     * @param  boolean $tous
     * @param  Site    $site
     * @param  integer $userId
     * @return [Collection]
     */
    public function getMedecinsUser($tous, $site, $userId)
    {
        if ($tous) {
            return $this->allMedecinBySite($site);
        } else {
            return $this->allMedecinByUser($userId);
        }
    }

  public function findByName($nom, $internet = TRUE) {

      $qb = $this->createQueryBuilder('m')
        ->select('m.idMedecin, m.nom, m.prenom, i.photoPath, a.ville, s.nom as specialite')
        ->join('m.abonne', 'a')
        ->join('m.infosDoc24', 'i')
        ->join('m.specialites', 's')
        ->where('m.rdvInternet = :internet')
        ->setParameter('internet', $internet);

      $qb->andwhere($qb->expr()->like('m.nom', ':nom'))
        ->setParameter('nom', '%'.$nom.'%');

        $qb->orderBy('m.nom');

        return $qb->getQuery()
        ->getArrayResult();

  }

  public function getInfosWeb($idMedecin) {

    $qb = $this->createQueryBuilder('m')
      ->select('m.idMedecin, a.latitude, a.longitude,
      i.photoPath, i.tarification, i.presentation, i.presentationLongue, i.conventionnement,
      i.tiers_payant, i.paiement_cb, i.paiement_ch, i.paiement_esp, i.site, i.email, i.carteVitale, i.id, i.infosPratiques, i.detailAcces,')
      ->join('m.abonne', 'a')
      ->join('m.infosDoc24', 'i')
      ->where('m.rdvInternet = :internet')
      ->andwhere('m.idMedecin = :idMedecin')
      ->setParameter('idMedecin', $idMedecin)
      ->setParameter('internet', TRUE);

    return $qb->getQuery()
      ->getResult();

  }

  public function getMedecinsInArray($arrayIdMedecins)
  {
    $qb = $this->createQueryBuilder('m')
      ->select('m.idMedecin, m.nom, m.prenom, m.generaliste, m.generaliste as rdv, t.numero, i.photoPath, a.latitude, a.longitude, a.voie, a.codePostal, a.ville, s.nom as specialite')
      ->join('m.abonne', 'a')
      ->join('m.infosDoc24', 'i')
      ->join('m.telephones', 't')
      ->join('m.specialites', 's');

    $qb->add('where', $qb->expr()->in('m.idMedecin', $arrayIdMedecins));

    $qb->andwhere('t.type = :typtel')
      ->setParameter('typtel', 1);

    return $qb->getQuery()->getResult();
  }

  public function getMedecinBySpecialiteByNom($encodedName, $specialite, $medecinInternet)
  {
    $qb = $this->createQueryBuilder('m')
      ->select('m.idMedecin, a.voie, t.numero, a.codePostal, a.longitude, a.latitude,
               i.photoPath, i.presentation, i.presentationLongue, i.conventionnement, i.tiers_payant, i.paiement_cb,
               i.paiement_ch, i.paiement_esp, i.site, i.email, i.carteVitale, i.infosPratiques, i.detailAcces, i.detailTelephone,
               m.nom, m.prenom, a.ville, s.nom as specialite, i.id')
      ->join('m.abonne', 'a')
      ->join('m.specialites', 's')
      ->join('m.infosDoc24', 'i')
      ->join('m.telephones', 't')
      ->where('m.rdvInternet = :internet')
      ->andwhere('s.nom = :specialite')
      ->andwhere("replace(lower(concat(substring(m.nom,4,255), ' ', m.prenom)),' ','-') = :nomMedecin")
      ->andwhere('t.type = :typtel')
      ->setParameter('typtel', 1)
      ->setParameter('nomMedecin', $encodedName)
      ->setParameter('specialite', $specialite)
      ->setParameter('internet', $medecinInternet);

    return $qb->getQuery()
      ->getResult();
  }

    public function findOneByNomUrl($encodedName) {
      $qb = $this->createQueryBuilder('m')
          ->where("replace(lower(concat(substring(m.nom,4,255), ' ', m.prenom)),' ','-') = :nomMedecin")
          ->setParameter('nomMedecin', $encodedName);

      return $qb->getQuery()
        ->getSingleResult();
    }

    public function findByNomRemplacant($encodedName) {
        $qb = $this->createQueryBuilder('m')
            ->where("lower(m.nom) = :nomMedecin")
            ->setParameter('nomMedecin', $encodedName);

        return $qb->getQuery()
            ->getSingleResult();
    }
}
