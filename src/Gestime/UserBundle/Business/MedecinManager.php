<?php

namespace Gestime\UserBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Medecin;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * MedecinManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class MedecinManager {
  protected $entityManager;
  protected $container;

  /**
   * [__construct description]
   * @param ContainerInterface $container
   * @param EntityManager $entityManager
   */
  public function __construct(ContainerInterface $container, $entityManager) {
    $this->entityManager = $entityManager;
    $this->container = $container;
  }

  /**
   * Créé un medecin en base
   * @param Medecin $medecin
   * @return integer id du medecin crée
   */
  public function saveMedecin(Medecin $medecin) {
    // association des numeros au medecin
    foreach ($medecin->getTelephones() as $telephone) {
      $telephone->setMedecin($medecin);
      $this->entityManager->persist($telephone);
    }

    // association des horaires au medecin
    if (!is_null($medecin->getHoraires())) {
      foreach ($medecin->getHoraires() as $horaire) {
        $horaire->setMedecin($medecin);
        $this->entityManager->persist($horaire);
      }
    }

    //Ajout des jours chomés
    $absenceMgr = $this->container->get('gestime.absence.manager');
    $absenceMgr->majFeries($medecin);

    $this->entityManager->persist($medecin);
    $this->entityManager->flush();
  }

  private function hasInfoWeb($medecin) {
    return (get_class($medecin->getInfosDoc24()) == "Gestime\CoreBundle\Entity\InfosDoc24");
  }

  /**
   * Modifie un medecin en base
   * @param Medecin $medecin
   * @return array
   */
  public function getTelephones(Medecin $medecin) {
    $telephones = array();
    foreach ($medecin->getTelephones() as $telephone) {
      $telephones[] = $telephone;
    }

    return $telephones;
  }

  /**
   * @param $medecinId
   * @return mixed
   */
  public function getMedecinbyId($medecinId)
  {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->find($medecinId);
  }

  /**
   * @param $specialiteId
   * @return mixed
   */
  public function getSpecialitebyId($specialiteId)
  {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:Specialite')
      ->find($specialiteId);
  }

  /**
   * Récupère toutes les plages horaires définies
   * @param Medecin $medecin
   * @return array
   */
  public function getHoraires(Medecin $medecin) {
    $horaires = array();
    foreach ($medecin->getHoraires() as $horaire) {
      $horaires[] = $horaire;
    }

    return $horaires;
  }

  /**
   * Récupère les specialites
   * @return array
   */
  public function getSpecialites() {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:Specialite')
      ->getSpecialites();
  }

  /**
   * Récupère les raisons de rendez-vous pour une spécialité
   * @return array
   */
  public function getRaisons($specialite) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:Specialite')
      ->getRaisons($specialite);
  }

  /**
   * @param \Gestime\CoreBundle\Entity\Medecin $medecin
   * @return mixed
   */
  public function getHorairesMedecin(Medecin $medecin) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:Horaire')
      ->getHorairesMedecin($medecin->getIdMedecin());

  }

  /**
   * @param \Gestime\CoreBundle\Entity\Medecin $medecin
   * @return mixed
   */
  public function getHorairesInternetMedecin(Medecin $medecin) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:HoraireInternet')
      ->getHorairesInternetMedecin($medecin->getIdMedecin());

  }

  /**
   * Récupère toutes les plages horaires Internet définies
   * @param Medecin $medecin
   * @return array
   */
  public function getHorairesInternet(Medecin $medecin) {
    $horairesInternet = array();
    foreach ($medecin->getHorairesInternet() as $horaireInternet) {
      $horairesInternet[] = $horaireInternet;
    }

    return $horairesInternet;
  }

  /**
   * Récupère tous les tarifs affichés sur Doc24
   * @param Medecin $medecin
   * @return array
   */
  public function getInfosDoc24Tarifs(Medecin $medecin) {
    if (!$this->hasInfoWeb($medecin)) {
      return null;
    }

    $tarifs = array();
    foreach ($medecin->getInfosDoc24()->getTarification() as $tarif) {
      $tarifs[] = $tarif;
    }

    return $tarifs;
  }

  /**
   * Récupère tous les moyens de transport affichés sur Doc24
   * @param Medecin $medecin
   * @return array
   */
  public function getInfosDoc24Transports(Medecin $medecin) {
    if (!$this->hasInfoWeb($medecin)) {
      return null;
    }
    $transports = array();
    foreach ($medecin->getInfosDoc24()->getTransport() as $transport) {
      $transports[] = $transport;
    }

    return $transports;
  }

  /**
   * Renvoie vrai si le médecin est rattaché à un utilisateur
   * @param Medecin $medecin
   * @return boolean
   */
  public function hasActiveUser(Medecin $medecin) {
    return (count($medecin->getUtilisateurs()) > 0);
  }

  /**
   * Modifie un medecin en base
   * @param Medecin $medecin
   * @param array $telsAvantModif
   * @param array $horairesAvantModif
   * @return integer id du medecin crée
   */
  public function saveEditedMedecin(
    Medecin $medecin,
    $telsAvantModif,
    $horairesAvantModif,
    $horairesInternetAvantModif,
    $tarifsAvantModif = null,
    $transportsAvantModif = null
  ) {
    //******************************************************
    //Suppression des téléphones si besoin
    //******************************************************
    foreach ($medecin->getTelephones() as $telephone) {
      foreach ($telsAvantModif as $key => $toDel) {
        if ($toDel->getId() === $telephone->getId()) {
          unset($telsAvantModif[$key]);
        }
      }
    }
    foreach ($telsAvantModif as $telephone) {
      $this->entityManager->remove($telephone);
    }

    //******************************************************
    //Suppression des horaires si besoin
    //******************************************************
    foreach ($medecin->getHoraires() as $horaire) {
      foreach ($horairesAvantModif as $key => $toDel) {
        if ($toDel->getId() === $horaire->getId()) {
          unset($horairesAvantModif[$key]);
        }
      }
    }
    foreach ($horairesAvantModif as $horaire) {
      $this->entityManager->remove($horaire);
    }

    //******************************************************
    //* Suppression des horaires Internet si besoin
    //******************************************************
    foreach ($medecin->getHorairesInternet() as $horaireInternet) {
      foreach ($horairesInternetAvantModif as $key => $toDel) {
        if ($toDel->getId() === $horaireInternet->getId()) {
          unset($horairesInternetAvantModif[$key]);
        }
      }
    }
    foreach ($horairesInternetAvantModif as $horaire) {
      $this->entityManager->remove($horaire);
    }

    if ($tarifsAvantModif !=null) {
      //******************************************************
      //* Suppression des tarifs si besoin
      //******************************************************
      foreach ($medecin->getInfosDoc24()->getTarification() as $tarif) {
        foreach ($tarifsAvantModif as $key => $toDel) {
          if ($toDel->getId() === $tarif->getId()) {
            unset($tarifsAvantModif[$key]);
          }
        }
      }
      foreach ($tarifsAvantModif as $tarif) {
        $this->entityManager->remove($tarif);
      }
    }
    if ($this->hasInfoWeb($medecin)) {
      foreach ($medecin->getInfosDoc24()->getTarification() as $tarif) {
        $tarif->setInfo($medecin->getInfosDoc24());
        $this->entityManager->persist($tarif);
      }
    }

    if ($transportsAvantModif !=null) {
      //******************************************************
      //* Suppression des moyens de transports si besoin
      //******************************************************
      foreach ($medecin->getInfosDoc24()->getTransport() as $transport) {
        foreach ($transportsAvantModif as $key => $toDel) {
          if ($toDel->getId() === $tarif->getId()) {
            unset($transportsAvantModif[$key]);
          }
        }
      }
      foreach ($transportsAvantModif as $transport) {
        $this->entityManager->remove($transport);
      }
    }
    if ($this->hasInfoWeb($medecin)) {
      if ($medecin->getInfosDoc24()->getTransport()) {
        foreach ($medecin->getInfosDoc24()->getTransport() as $transport) {
          $transport->setInfo($medecin->getInfosDoc24());
          $this->entityManager->persist($transport);
        }
      }
    }

    foreach ($medecin->getTelephones() as $telephone) {
      $telephone->setMedecin($medecin);
      $this->entityManager->persist($telephone);
    }

    foreach ($medecin->getHoraires() as $horaire) {
      $horaire->setMedecin($medecin);
      $this->entityManager->persist($horaire);
    }

    foreach ($medecin->getHorairesInternet() as $horaireInternet) {
      $horaireInternet->setMedecin($medecin);
      $this->entityManager->persist($horaireInternet);
    }

    $this->entityManager->persist($medecin);
    $this->entityManager->flush();


  }

  /**
   * Supprime un medecin en base
   * @param Medecin $medecin
   * @return integer id du medecin crée
   */
  public function saveDeletedMedecin(Medecin $medecin) {
    $this->entityManager->remove($medecin);
    $this->entityManager->flush();
  }

  /**
   * Recupere le nb de rendez vous d'un médecin (globalement)
   * @param Medecin $medecin
   * @return integer
   */
  public function getCountRdv(Medecin $medecin) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:Evenement')
      ->getCountRdv($medecin);
  }

  /**
   * Recupere le numero du telephone sur lequel envoyer les SMS
   * @param integer $idMedecin
   * @return array
   */
  public function getNumerosSMS($idMedecin) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->getNumeroSMSbyMedecin($idMedecin)->getQuery()->getArrayResult();
  }

  /**
   * @param $medecins
   * @param $id
   * @return mixed
   */
  private function findOneByIdMedecin($medecins, $id) {
    foreach ($medecins as $medecin) {
      if ($medecin->getIdMedecin() == $id) {
        return $medecin;
      }
    }
  }

  /**
   * @return mixed
   */
  private function getArrayMedecinsBySpecialiteByLieu($idSpecialite, $lieu, $distance, $rdvInternet )
  {
    $utils = $this->container->get('gestime_core.utilities');
    $coordonnees = $utils->getGeoLoc($lieu);
    $medecins_ = $this->entityManager->getRepository('GestimeCoreBundle:Abonne')
                       ->getMedecinsProches($coordonnees['lat'], $coordonnees['lng'], $distance, $rdvInternet );
    $this->entityManager->clear();

    // Ce tableau permettent d'éviter de faire beaucoup de requetes..
    $medecinsArray = $this->entityManager->getRepository('GestimeCoreBundle:Medecin')->getListMedecinsSpecialites();

    foreach ($medecins_ as $key => $_medecin) {
      $medecin = $this->findOneByIdMedecin($medecinsArray, $_medecin->getIdMedecin());

      if ($medecin->getSpecialites()->count() == 0) {
        unset ($medecins_[$key]);
      }
      else {
        foreach ($medecin->getSpecialites() as $specialite) {
          if ($specialite->getId() != $idSpecialite) {
            unset ($medecins_[$key]);
          }
        }
      }
    }
    $arrayMedecins = array();
    foreach ($medecins_ as $medecin) {
      $arrayMedecins[] = $medecin->getIdMedecin();
    }

    return $arrayMedecins;
  }

  /**
   * @return mixed
   */
  private function getArrayMedecinsBySpecialite($idSpecialite, $distance, $rdvInternet )
  {

    $medecins_ = $this->entityManager->getRepository('GestimeCoreBundle:Medecin')->findAll();

    // Ce tableau permettent d'éviter de faire beaucoup de requetes..
    $medecinsArray = $this->entityManager->getRepository('GestimeCoreBundle:Medecin')->getListMedecinsSpecialites();

    foreach ($medecins_ as $key => $_medecin) {
      $medecin = $this->findOneByIdMedecin($medecinsArray, $_medecin->getIdMedecin());

      if ($medecin->getSpecialites()->count() == 0) {
        unset ($medecins_[$key]);
      }
      else {
        foreach ($medecin->getSpecialites() as $specialite) {
          if ($specialite->getId() != $idSpecialite) {
            unset ($medecins_[$key]);
          }
        }
      }
    }
    $arrayMedecins = array();
    foreach ($medecins_ as $medecin) {
      $arrayMedecins[] = $medecin->getIdMedecin();
    }

    return $arrayMedecins;
  }

  public function getDureeConsultation($medecin) {
    return $this->entityManager->getRepository('GestimeCoreBundle:Medecin')->getDureeConsultation($medecin->getIdMedecin())["dureeRdv"];
  }


  /**
   * @return mixed
   */
  private function jsonRechercheDoc24($idSpecialite, $arrayMedecins, $debut, $fin) {
    if (count($arrayMedecins) == 0 ) {
      return array();
    }
    $doc24Mgr = $this->container->get('gestime.doc24.manager');

    $medecins = $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->getMedecinsInArray($arrayMedecins);

    $temp = array();
    foreach ($medecins as $medecin) {
      $rdvlibres = $doc24Mgr->getRdvInternetLibres($medecin['idMedecin'], $debut, $fin);
      $temp[] = array('nom'=>$medecin['nom'],
                    'prenom'=>$medecin['prenom'],
                    'coordonnees' => array('latitude'=>$medecin['latitude'], 'longitude'=>$medecin['longitude']),
                    'idMedecin'=>$medecin['idMedecin'],
                    'avatar'=>$medecin['photoPath'].'.jpg',
                    'idSpecialite'=>$idSpecialite,
                    'adresse'=>$medecin['voie'],
                    'ville'=>$medecin['codePostal'].' '.strtoupper($medecin['ville']),
                    'telephone' => $medecin['numero'],
                    'specialite'=>$medecin['specialite'],
                    'prochainRdv'=>$rdvlibres['prochain'],
                    'rdv'=>$rdvlibres['rdvs']
              );
    }

    return $temp;
  }

  /**
   * @return mixed
   */
  public function getMedecinsBySpecialiteByLieuByDate($idSpecialite,$lieu, $debut, $fin, $distance = 20, $rdvInternet = TRUE ) {

    return $this->jsonRechercheDoc24($idSpecialite,
                                     $this->getArrayMedecinsBySpecialiteByLieu($idSpecialite, $lieu, $distance, $rdvInternet),
                                     $debut,
                                     $fin);
  }


  /**
   * @return mixed
   */
  public function getMedecinsBySpecialiteByDate($idSpecialite, $debut, $fin, $distance = 20, $rdvInternet = TRUE ) {

    return $this->jsonRechercheDoc24($idSpecialite,
        $this->getArrayMedecinsBySpecialite($idSpecialite, $distance, $rdvInternet),
        $debut,
        $fin);
  }



  public function getMedecinsByNomBySpecialiteByDate($nom ,$nomSpecialite, $debut, $fin) {

    $specialite = $this->entityManager
      ->getRepository('GestimeCoreBundle:Specialite')
      ->findOneByNom($nomSpecialite);

    $medecin = $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->findOneByNomUrl($nom);

    if (!$specialite || !$medecin) {
      return false;
    }

    $arrayMedecins = array();
    $arrayMedecins[] = $medecin->getIdMedecin();

    return $this->jsonRechercheDoc24($specialite->getId(), $arrayMedecins,  $debut, $fin);
  }

  private function getAvatar($photoPath, $type) {
    $directory_avatars = $this->container->get('kernel')->getRootDir().'/../web/avatars/';
    $filename_avatar = ($type != 'small')?$photoPath.'.jpg':'small/'.$photoPath.'.png';
    $filename_avatar_default = ($type != 'small')?'avatar_low.png':'small/'.'avatar_low.png';

    $fs = new Filesystem();

    if ($fs->exists($directory_avatars.$filename_avatar)) {
      $imagedata = file_get_contents($directory_avatars.$filename_avatar);
    }
    else
    {
      $imagedata = file_get_contents($directory_avatars.$filename_avatar_default);
    }

    return base64_encode($imagedata);
  }

  /**
   * @return mixed
   */
  public function getProspects($idSpecialite, $lieu) {

    $utils = $this->container->get('gestime_core.utilities');
    $coordonnees = $utils->getGeoLoc($lieu);

    $prospects = $this->entityManager->getRepository('GestimeCoreBundle:Prospect')
      ->getProspectsProches($idSpecialite, $coordonnees['lat'], $coordonnees['lng'], 5);
    $this->entityManager->clear();

    return $prospects;
  }


  /**
   * @return mixed
   */
  public function getMedecinsByNom($nom, $medecinInternet = TRUE ) {
    $medecins =  $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->findByName($nom);

    $response = array();

    foreach ($medecins as $medecin) {
      $response[] = array(
        'idMedecin'   => $medecin['idMedecin'],
        'avatar'      => $medecin['photoPath'].'.jpg', // $this->getAvatar($medecin['photoPath'], 'small'),
        'nom'         => $medecin['nom'],
        'prenom'      => $medecin['prenom'],
        'ville'       => $medecin['ville'],
        'specialite'  => $medecin['specialite'],
      );
    }
      return $response;
  }

  private function getTarif($id) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:InfosDoc24')
      ->getTarifs($id)['tarifications'];
  }

  private function getTransport($id) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:InfosDoc24')
      ->getTransports($id);
  }

  private function getLangues($id) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:InfosDoc24')
      ->getLangues($id)['langues'];
  }

  private function getSpecialitesMedicales($id) {
    return $this->entityManager
      ->getRepository('GestimeCoreBundle:InfosDoc24')
      ->getSpecialitesMedicales($id)['specialitesMedicales'];
  }

  /**
   * @return mixed
   */
  public function getMedecinBySpecialiteByNom($encodedName, $specialite, $medecinInternet = TRUE ) {
    $infos =  $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->getMedecinBySpecialiteByNom($encodedName, $specialite, $medecinInternet);

    if ($infos) {
      $info = $infos[0];

      $response = array(
        'idMedecin'           => $info['idMedecin'],
        'nom'                 => $info['nom'],
        'prenom'              => $info['prenom'],
        'telephone'           => $info['numero'],
        'voie'                => $info['voie'],
        'codePostal'          => $info['codePostal'],
        'ville'               => $info['ville'],
        'specialite'          => $info['specialite'],
        'latitude'            => $info['latitude'],
        'longitude'           => $info['longitude'],
        'avatar'              => $info['photoPath'].'.jpg',  //$this->getAvatar($info['photoPath'], 'big'),
        'presentation'        => $info['presentation'],
        'presentationLongue'  => $info['presentationLongue'],
        'tarification'        => $this->getTarif($info['id']),
        'paiement_cb'         => $info['paiement_cb'],
        'paiement_ch'         => $info['paiement_ch'],
        'paiement_esp'        => $info['paiement_esp'],
        'site'                => $info['site'],
        'email'               => $info['email'],
        'tiers_payant'        => $info['tiers_payant'],
        'conventionnement'    => $info['conventionnement'],
        'carteVitale'         => $info['carteVitale'],
        'langues'             => $this->getLangues($info['id']),
        'specialitesMedicales'=> $this->getSpecialitesMedicales($info['id']),
        'infosPratiques'      => $info['infosPratiques'],
        'detailAcces'         => $info['detailAcces'],
        'detailTelephone'     => $info['detailTelephone'],
        'transport'           => $this->getTransport($info['id']),
      );

      return $response;
    }

    return false;
  }

  /**
   * @return mixed
   */
  public function getInfosWeb($idMedecin) {

    $infos =  $this->entityManager
      ->getRepository('GestimeCoreBundle:Medecin')
      ->getInfosweb($idMedecin);

    if ($infos) {
      $info = $infos[0];
      $response = array('idMedecin'           => $info['idMedecin'],
        'latitude'            => $info['latitude'],
        'longitude'           => $info['longitude'],
        'avatar'              => $this->getAvatar($info['id'], $info['photoPath']),
        'presentation'        => $info['presentation'],
        'presentationLongue'  => $info['presentationLongue'],
        'tarification'        => $this->getTarif($info['id']),
        'paiement_cb'         => $info['paiement_cb'],
        'paiement_ch'         => $info['paiement_ch'],
        'paiement_esp'        => $info['paiement_esp'],
        'site'                => $info['site'],
        'email'               => $info['email'],
        'tiers_payant'        => $info['tiers_payant'],
        'conventionnement'    => $info['conventionnement'],
        'carteVitale'         => $info['carteVitale'],
        'langues'             => $this->getLangues($info['id']),
        'specialitesMedicales'=> $this->getSpecialitesMedicales($info['id']),
        'infosPratiques'      => $info['infosPratiques'],
        'detailAcces'         => $info['detailAcces'],
        'transport'           => $this->getTransport($info['id']),
      );

      return $response;
    }
    return false;
  }

  /**
   * @return mixed
   */
  public function getFAQ() {

    return $this->entityManager
        ->getRepository('GestimeCoreBundle:Question')->findAll();
    }

}
