<?php

namespace Gestime\UserBundle\Business;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Abonne;
use Gestime\CoreBundle\Entity\Medecin;

/**
 * AbonneManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AbonneManager
{
    protected $entityManager;
    protected $container;

    /**
     * [__construct description]
     * @param ContainerInterface $container
     * @param EntityManager      $entityManager
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * Envoi le fichier de config des répondeurs sur le serveur distant (ACP)
     * @param EntityManager $entityManager
     * @return integer id de l'absence crée
     */
    private function envoiFichierConfig(EntityManager $entityManager)
    {
        $fileConfigRepondeurs = $this->container->getParameter('fileConfigRepondeurs');
        $uploadDir = $this->container->getParameter('upload_dir');

        $listeRepondeurs = $entityManager
                              ->getRepository('GestimeCoreBundle:Abonne')
                              ->getAllAbonnePeriodeRepondeur();

        $utils = $this->container->get('gestime_core.utilities');
        $utils->writeArrayToFile($uploadDir, $fileConfigRepondeurs, $listeRepondeurs);

        if (!empty($this->container)) {
            $this->container->get('gestime.repondeur_uploader')
                            ->upload($uploadDir.'/'.$fileConfigRepondeurs, $fileConfigRepondeurs, $contentType = 'text/csv');
        }
    }




    /**
     * Créé un abonné en base
     * @param Abonne $abonne
     * @return integer id de l'abonné crée
     */
    public function save_abonne(Abonne $abonne)
    {
        $utils = $this->container->get('gestime_core.utilities');

        // Si l'adresse exacte n'est pas trouvée par l'API google Geoloc renvoie null

        if (!is_null($utils->getGeoLoc($abonne->getAdresse())['lng'])) {
            $abonne->setLongitude($utils->getGeoLoc($abonne->getAdresse())['lng']);
        } else {
            $abonne->setLongitude(0);
        }

        if (!is_null($utils->getGeoLoc($abonne->getAdresse())['lat'])) {
            $abonne->setLatitude($utils->getGeoLoc($abonne->getAdresse())['lat']);
        } else {
            $abonne->setLatitude(0);
        }

        $this->entityManager->persist($abonne);
        $this->entityManager->flush();
        $this->envoiFichierConfig($this->entityManager);

        return $abonne->getIdAbonne();
    }

    /**
     * Récupère toutes les périodes non travaillée de l'abonné
     * @param Abonne $abonne
     * @return array
     */
    public function getPeriodes(Abonne $abonne)
    {
        $periodesAvantModif = array();
        foreach ($abonne->getPeriodes() as $periode) {
            $periodesAvantModif[] = $periode;
        }

        return $periodesAvantModif;
    }

    /**
     * Modifie un abonné en base
     * @param Abonne $abonne
     * @param Array  $periodesAvantModif
     * @return integer id de l'abonne modifé
     */
    public function save_edited_abonne(Abonne $abonne, $periodesAvantModif)
    {
        //Suppression des periodes si besoin
        foreach ($abonne->getPeriodes() as $periode) {
            foreach ($periodesAvantModif as $key => $toDel) {
                if ($toDel->getId() === $periode->getId()) {
                    unset($periodesAvantModif[$key]);
                }
            }
        }

        foreach ($periodesAvantModif as $periode) {
            $this->entityManager->remove($periode);
        }

        foreach ($abonne->getPeriodes() as $periode) {
            $periode->setAbonne($abonne);
            $this->entityManager->persist($periode);
        }

        $utils = $this->container->get('gestime_core.utilities');

        $abonne->setLongitude($utils->getGeoLoc($abonne->getAdresse())['lng']);
        $abonne->setLatitude($utils->getGeoLoc($abonne->getAdresse())['lat']);

        $this->entityManager->persist($abonne);
        $this->entityManager->flush();
        $this->envoiFichierConfig($this->entityManager);

        return $abonne->getIdAbonne();
    }

    /**
     * Supprime un abonné en base
     * @param Abonne $abonne
     * @param Array  $affectation Periodes non travaillées
     * @return boolean
     */
    public function save_deleted_abonne(Abonne $abonne, $affectation)
    {
        if (!$affectation) {
            $this->entityManager->remove($affectation);
        }

        if ($abonne->getMedecins()->count() > 0) {
            foreach ($abonne->getMedecins() as $medecin) {
                $medecin->setAbonne(null);
                $this->entityManager->persist($medecin);
            }
        }

        $this->entityManager->remove($abonne);
        $this->entityManager->flush();
        $this->envoiFichierConfig($this->entityManager);
    }

    /**
     * @return mixed
     */
    public function getVilles()
      {
        return $this->entityManager
          ->getRepository('GestimeCoreBundle:Abonne')
          ->getVilles();
      }

}
