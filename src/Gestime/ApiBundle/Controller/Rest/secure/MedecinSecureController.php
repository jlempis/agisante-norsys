<?php

namespace Gestime\ApiBundle\Controller\Rest\secure;

use Gestime\ApiBundle\Model\Page;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * MedecinController
 *
 * @FOSRest\NamePrefix("api_")
 */
class MedecinSecureController extends Controller
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
    public function getSpecialiteesAction()
    {
      $medecinMgr = $this->container->get('gestime.medecin.manager');

      return  $medecinMgr->getSpecialites();
    }

    /**
     *
     * @throws AccessDeniedException
     * @return Page
     *
     * @FOSRest\View()
     */
  public function getVillesAction()
  {
    $abonneMgr = $this->container->get('gestime.abonne.manager');

    //return  $abonneMgr->getVilles();
  }

}
