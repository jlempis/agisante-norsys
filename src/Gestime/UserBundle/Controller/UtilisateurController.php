<?php

/**
 * UtilisateurController class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Gestime\UserBundle\Form\Type\UtilisateurType;
use Gestime\UserBundle\Form\Type\ChangePasswordType;
use Gestime\CoreBundle\Entity\ChangePassword;
use Gestime\CoreBundle\Entity\Utilisateur;

/**
 * Utiisateur
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class UtilisateurController extends Controller
{
    /**
     * @Secure(roles="ROLE_GESTION_UTILISATEURS")
     */
    /**
     * Peuplement de la liste des utilisateurs
     * @return json Ensemble des utilisateurs
     */
    private function _datatable()
    {
        $qb = $this->getDoctrine()->getManager()
                     ->getRepository('GestimeCoreBundle:Utilisateur')
                     ->getListUsers($this->getUser()->getSite());
        $controllerInstance = $this;
        //Attn : Modification de la classe DoctrineBuilder Methode getData
        //Pour l'utilisation de GroupContact

        $datatable = $this->get('datatable')
                ->setFields(
                    array(
                        'Nom'            => 'u.nom',
                        'Prenom'         => 'u.prenom',
                        'Login'          => 'u.username',
                        'email'          => 'u.email',
                        'Roles'          => 'GroupConcat(g.name)',
                        '_identifier_'   => 'u.id', )
                )
                ->setRenderer(
                    function (&$data) use ($controllerInstance) {
                        foreach ($data as $key => $value) {
                            if ($key == 4) {
                                $data2 = explode('&', $value);
                                $data[$key] = $controllerInstance
                                        ->get('templating')
                                        ->render(
                                            'GestimeUserBundle:utilisateurs/templates:grid_roles.html.twig',
                                            array('data' => $data2)
                                        );
                            }
                        }
                    }
                )
                ->setSearch(true)
                ->setSearchFields(array(0, 3))
                ->setOrder('u.nom', 'desc')
                ->setHasAction(true);

        $datatable->getQueryBuilder()->setDoctrineQueryBuilder($qb);

        return $datatable;
    }
    /**
     * @Route("/grille", name="utilisateurs_grille")
     * @Method("GET")
     * @Secure(roles="ROLE_GESTION_UTILISATEURS")
     *
     * @return json
     */
    public function grilleAction()
    {
        return $this->_datatable()->execute();
    }

    /**
     * @Route("/", name="utilisateurs_liste")
     * @Method("GET")
     * @Secure(roles="ROLE_GESTION_UTILISATEURS")
     *
     * @return Template
     */
    public function indexAction()
    {
        $this->_datatable();

        return $this->render('GestimeUserBundle:utilisateurs:index.html.twig',
            array('menuactif' => 'Utilisateurs')
        );
    }

    /**
     * @Route("/ajouter", name="utilisateurs_ajouter")
     * @Method({"GET", "POST"})
     * @Secure(roles="ROLE_GESTION_UTILISATEURS")
     *
     * @return Template
     */
    public function ajouterAction()
    {
        $request = $this->getRequest();
        $utilisateur = new Utilisateur($this->getUser()->getSite());

        $form = $this->createForm(new UtilisateurType(),
            $utilisateur,
            array('attr' => array('action' => 'ajouter',
                                  ))
        );
        $utilisateur->setPlainPassword('12345678');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $utilisateurMgr = $this->container->get('gestime.utilisateur.manager');
            $utilisateurMgr->saveUtilisateur($this->getUser()->getSite(),
                $form->getData(),
                $utilisateur
            );

            return $this->redirect($this->generateUrl('utilisateurs_liste'));
        }
        $this->_datatable();

        return $this->render('GestimeUserBundle:utilisateurs:ajouter.html.twig',
            array('form' => $form->createView(),
                   'action' => 'ajouter',
                   'menuactif' => 'Utilisateurs',
            )
        );
    }

    /**
     * @Route("/edit/{id}", name="utilisateurs_edit")
     * @Method({"GET", "POST"})
     * @Secure(roles="ROLE_GESTION_UTILISATEURS")
     *
     * [editAction description]
     * @param Request     $request
     * @param Utilisateur $utilisateur
     * @return Template
     */
    public function editAction(Request $request, Utilisateur $utilisateur)
    {
        $request = $this->getRequest();

        $medecinsAvantModif = array();
        foreach ($utilisateur->getMedecins() as $medecin) {
            $medecinsAvantModif[] = $medecin;
        }

        $form = $this->createForm(new UtilisateurType(),
            $utilisateur,
            array('attr' => array('action' => 'editer',))
        );

        $changePassword = new ChangePassword();
        $changePassword->username = $utilisateur->getUsername();
        $changePassword->userid = $utilisateur->getId();
        $passwordForm = $this->createForm(new ChangePasswordType(), $changePassword);

        $utilisateur->setPlainPassword($form->get('password')->getData());

        $form->handleRequest($request);

        if ($form->isValid()) {

            if(!empty($form->get('password')->getData())){
                $utilisateur->setPlainPassword($form->get('password')->getData());
            }

            $utilisateurMgr = $this->container->get('gestime.utilisateur.manager');
            $utilisateurMgr->editUtilisateur($utilisateur,
                $form->getData(),
                $medecinsAvantModif
            );

            return $this->redirect($this->generateUrl('utilisateurs_liste'));
        }

        $this->_datatable();

        return $this->render('GestimeUserBundle:utilisateurs:editer.html.twig',
            array('form' => $form->createView(),
                   'passwordForm' => $passwordForm->createView(),
                   'action' => 'edition',
                   'menuactif' => 'Utilisateurs',
            )
        );
    }

    /**
     * @Route("/suppr/{id}", name="utilisateurs_delete")
     * @Method({"GET", "POST"})
     * @Secure(roles="ROLE_GESTION_UTILISATEURS")
     *
     * [deleteAction description]
     * @param Request     $request
     * @param Utilisateur $utilisateur
     * @return Template
     */
    public function deleteAction(Request $request, Utilisateur $utilisateur)
    {
        $request = $this->getRequest();

        $form = $this->createForm(new UtilisateurType(),
            $utilisateur,
            array('attr' => array('action' => 'supprimer',)
            )
        );
        $utilisateur->setPlainPassword($form->get('password')->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $utilisateurMgr = $this->container->get('gestime.utilisateur.manager');
            $utilisateurMgr->deletedUtilisateur($utilisateur);

            return $this->redirect($this->generateUrl('utilisateurs_liste'));
        }
        $this->_datatable();

        return $this->render('GestimeUserBundle:utilisateurs:supprimer.html.twig',
            array( 'action'  => 'Supprimer un utilisateur',
                    'form' => $form->createView(),
                    'action' => 'suppression',
                    'menuactif' => 'Utilisateurs',

            ));
    }

    /**
     * @Route("/password/{idUtilisateur}/{newpassword}", name="utilisateur_ajax_password",options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_UTILISATEURS")
     *
     * [changePasswordAction description]
     * @param integer $idUtilisateur
     * @param string  $newpassword
     * @return Template
     */
    public function changePasswordAction($idUtilisateur, $newpassword)
    {
        $utilisateurMgr = $this->container->get('gestime.utilisateur.manager');
        $utilisateur = $utilisateurMgr->getUtilisateurById($idUtilisateur);
        $statut = $utilisateurMgr->changeUserPassword($utilisateur, $newpassword);

        $response = new JsonResponse();
        $response->setContent(json_encode($statut));

        return $response;
    }
}
