<?php

/**
 * MededinController class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\UserBundle\Form\Type\MedecinType;


/**
 * Médecin
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class MedecinController extends Controller
{
    /**
     * @Secure(roles="ROLE_GESTION_MEDECINS")
     */
    private function _datatable()
    {
        $qb = $this->getDoctrine()->getManager()
            ->getRepository('GestimeCoreBundle:medecin')
            ->getListMedecins($this->getUser()->getSite());

        $datatable = $this->get('datatable')
                ->setFields(
                    array(
                        'Abonné'         => 'a.raisonSociale',
                        'Nom'            => 'm.nom',
                        'Prenom'         => 'm.prenom',
                        'Durée RDV'      => 'm.dureeRdv',
                        'Remplacant'     => 'm.remplacant',
                        'Généraliste'    => 'm.generaliste',
                        'Rappel SMS'     => 'm.abonneSms',
                        '_identifier_'   => 'm.idMedecin', )
                )
                ->setSearch(true)
                ->setSearchFields(array(0, 3))
                ->setOrder('x.nom', 'desc')
                ->setHasAction(true);
        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

        return $datatable;
    }
    /**
     * @Route("/grille", name="medecins_grille")
     * @Method("GET")
     * @Secure("ROLE_GESTION_MEDECINS")
     *
     * @return json
     */
    public function grilleAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/", name="medecins_liste")
     * @Method("GET")
     * @Secure("ROLE_GESTION_MEDECINS")
     *
     * @return Template
     */
    public function indexAction()
    {
        $this->_datatable();

        return $this->render('GestimeUserBundle:medecins:index.html.twig',
            array('menuactif' => 'Utilisateurs')
        );
    }

    /**
     * @Route("/ajouter", name="medecins_ajouter")
     * @Secure("ROLE_GESTION_MEDECINS")
     * @Template("GestimeUserBundle:medecins:page.html.twig")
     *
     * @param request $request
     * @return Template
    */
    public function ajouterAction(Request $request)
    {
        $medecin = new Medecin($this->getUser()->getSite());
        $form = $this->createForm(new MedecinType(), $medecin,
            array('attr' => array('read_only' => false, 'action' => 'add' ))
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $medecinMgr = $this->container->get('gestime.medecin.manager');
            $medecinMgr->saveMedecin($medecin);

            return $this->redirect($this->generateUrl('medecins_liste'));
        }
        $this->_datatable();

        return array('action'        => 'Ajouter un médecin',
                      'medecin'       => $medecin,
                      'rdvExistants'  => 0,
                      'utilisateurs'  => false,
                      'form'          => $form->createView(),
                      'menuactif'     => 'Utilisateurs',
        );
    }

    /**
     * @Route("/edit/{idMedecin}", name="medecins_edit")
     * @Secure("ROLE_GESTION_MEDECINS")
     * @Template("GestimeUserBundle:medecins:page.html.twig")
     *
     * @param request $request
     * @param Medecin $medecin
     * @return Template
    */
    public function editAction(Request $request, Medecin $medecin)
    {
        $medecinMgr = $this->container->get('gestime.medecin.manager');
        $telsAvantModif = $medecinMgr->getTelephones($medecin);
        $horairesAvantModif = $medecinMgr->getHoraires($medecin);
        $horairesInternetAvantModif = $medecinMgr->getHorairesInternet($medecin);
        $rdvExistants = $medecinMgr->getCountRdv($medecin);
        $utilisateurs = $medecinMgr->hasActiveUser($medecin);
        $redis = $this->container->get('snc_redis.doctrine');

        $form = $this->createForm(
            new MedecinType(),
            $medecin,
            array('attr' => array('read_only' => false, 'action' => 'edit' ))
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $medecinMgr->saveEditedMedecin($medecin, $telsAvantModif, $horairesAvantModif, $horairesInternetAvantModif);
            $redis->flushall();

            return $this->redirect($this->generateUrl('medecins_liste'));
        }
        $this->_datatable();

        return array('action'        => 'Mettre à jour un médecin',
                      'rdvExistants'  => $rdvExistants,
                      'utilisateurs'  => $utilisateurs,
                      'form'          => $form->createView(),
                      'menuactif'     => 'Utilisateurs',
        );
    }

    /**
     * @Route("/suppr/{idMedecin}", name="medecins_delete")
     * @Secure("ROLE_GESTION_MEDECINS")
     * @Template("GestimeUserBundle:medecins:page.html.twig")
     *
     * @param request $request
     * @param Medecin $medecin
     * @return Template
    */
    public function deleteAction(Request $request, Medecin $medecin)
    {
        $medecinMgr = $this->container->get('gestime.medecin.manager');
        $rdvExistants = $medecinMgr->getCountRdv($medecin);
        $hasActiveUser = $medecinMgr->hasActiveUser($medecin);

        $form = $this->createForm(new MedecinType(),
            $medecin, array('attr' => array( 'action' => 'suppr',
                           'interdit' => $hasActiveUser,
                          ))
        );
        $form->handleRequest($request);

        if ($form->isValid()) {
            $medecinMgr->saveDeletedMedecin($medecin);

            return $this->redirect($this->generateUrl('medecins_liste'));
        }
        $this->_datatable();

        return array('action'       => 'Supprimer un médecin',
                      'rdvExistants' => $rdvExistants,
                      'utilisateurs' => $hasActiveUser,
                      'form'         => $form->createView(),
                      'menuactif'    => 'Utilisateurs',
        );
    }
  
    /**
     * @Route("/numerosms/{idMedecin}", name="medecin_get_numerosms")
     * @Method("POST")
     * @Secure("ROLE_GESTION_MEDECINS")
     *
     * @param integer $idMedecin
     * @return json
    */
    public function getnumerosmsAction($idMedecin)
    {
        $medecinMgr = $this->container->get('gestime.medecin.manager');
        $numeros = $medecinMgr->getNumerosSMS($idMedecin);

        $response = new JsonResponse();
        if (empty($numeros)) {
            $response->setData(array( 'success' => false));
        } else {
            $response->setData(array( 'success' => true, 'numero' => $numeros[0]['numero']));
        }

        return ($response);
    }


}
