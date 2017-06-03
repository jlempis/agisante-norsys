<?php

namespace Gestime\ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * MedecinController
 *
 * @FOSRest\NamePrefix("api_")
 */
class MedecinController extends Controller
{
    /**
     * @throws AccessDeniedException
     * @return array
     *
     * @FOSRest\View(serializerGroups={"list"})
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Liste des spécialités des médecins",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     */
    public function getSpecialitesAction()
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');

      return  $medecinMgr->getSpecialites();
    }

    /**
     * @throws AccessDeniedException
     * @return array
     *
     * @FOSRest\View(serializerGroups={"list"})
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Liste des spécialités des médecins",
     *  filters={
     *      {"name"="a-filter", "dataType"="integer"},
     *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
     *  }
     * )
     */
      public function getRaisonsAction($specialite)
      {
        $medecinMgr = $this->container->get('gestime.medecin.manager');

        return  $medecinMgr->getRaisons($specialite);
      }

    /**
     * @FOSRest\View
     */
      public function getDispoRdvAction($rendezVous)
      {
        $medecinMgr = $this->container->get('gestime.medecin.manager');

        return  $medecinMgr->getRaisons($specialite=6);
      }


  /**
   *
   * @FOSRest\View(serializerGroups={"list"})
   * @ApiDoc(
   *  resource=true,
   *  description="Liste des rdv disponibles pour les medecins d'une spécialité dans un territoire",
   *  filters={
   *      {"name"="a-filter", "dataType"="integer"},
   *      {"name"="another-filter", "dataType"="string", "pattern"="(foo|bar) ASC|DESC"}
   *  }
   * )
   */
    public function getSpecialiteVilleDebutFinAction($specialite, $ville, $debut, $fin)
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');
      return $medecinMgr->getMedecinsBySpecialiteByLieuByDate($specialite, $ville, $debut, $fin);
    }

    /**
     * @param $nom
     * @param $specialite
     * @param $debut
     * @param $fin
     * @return bool|mixed
     * @FOSRest\View
     */
    public function getNomSpecialiteDebutFinAction($nom, $specialite, $debut, $fin)
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');
      return $medecinMgr->getMedecinsByNomBySpecialiteByDate($nom, $specialite, $debut, $fin);
    }

    /**
     * @param $specialite
     * @param $nom
     * @return mixed
     * @FOSRest\View
     */
    public function getSpecialiteNomAction($specialite, $nom)
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');
      return $medecinMgr->getMedecinBySpecialiteByNom($nom, $specialite);
    }

    /**
     * @param $specialite
     * @param $lieu
     * @return mixed
     * @FOSRest\View
     * @FOSRest\Get("/prospects/{specialite}/{lieu}")
     */
    public function getProspectsLieuAction($specialite, $lieu)
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');

      return $medecinMgr->getProspects($specialite, $lieu);
    }

    /**
     * @FOSRest\View
     * @param $nom
     * @return mixed
     */
    public function getPraticiensAction($nom)
      {
        $medecinMgr = $this->container->get('gestime.medecin.manager');

        return $medecinMgr->getMedecinsByNom($nom);
      }

    /**
     * @FOSRest\View
     * @param $idMedecin
     * @return mixed
     */
    public function getInfosAction($idMedecin)
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');

      return $medecinMgr->getInfosWeb($idMedecin);
    }

    /**
     * @FOSRest\View
     * @param $idMedecin
     * @return mixed
     */
    public function getFaqAction()
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');

      return $medecinMgr->getFAQ();
    }
}
