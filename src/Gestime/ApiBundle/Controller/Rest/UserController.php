<?php

namespace Gestime\ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations as FOSRest;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Gestime\ApiBundle\Model\UtilisateurWeb;
use Gestime\ApiBundle\Form\Type\UtilisateurWebType;
use Gestime\ApiBundle\Model\InfoUserWeb;
use Gestime\ApiBundle\Form\Type\InfoUserWebType;
use Gestime\ApiBundle\Model\ContactWeb;
use Gestime\ApiBundle\Form\Type\ContactWebType;

/**
 * UserController
 *
 * @FOSRest\NamePrefix("api2_")
 */
class UserController extends Controller {

  private function processInscriptionForm(Request $request, UtilisateurWeb $user)
  {
    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Inscription: IP %s, Nom: %s, Prenom: %s, Email: %s, Naissance: %s, Sexe: %s, Password: %s',
          $request->getClientIp(),
          $request->request->get('utilisateurWeb')['email'],
          $request->request->get('utilisateurWeb')['nom'],
          $request->request->get('utilisateurWeb')['prenom'],
          $request->request->get('utilisateurWeb')['naissance'],
          $request->request->get('utilisateurWeb')['sexe'],
          $request->request->get('utilisateurWeb')['password']));

    $userMgr = $this->container->get('gestime.utilisateur.manager');
    $utilisateurExiste = $userMgr->utilisateurExiste($request->request->get('utilisateurWeb')['email'] );

    $statusCode = (! $utilisateurExiste) ? 201 : 204;

    $form = $this->createForm(new UtilisateurWebType(), $user);
    $form->handleRequest($request);

    if ($form->isValid()) {
      $response = new Response();
      $response->setStatusCode($statusCode);

      if ($statusCode == 201) {
        $result = $userMgr->saveWebUser($user);
        $response->setContent($result);
      }

      return $response;
    }

    return View::create($form, 201);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpFoundation\Response
   */
  public function postInscriptionAction(Request $request) {

    return $this->processInscriptionForm($request, new UtilisateurWeb());
  }

  private function processContactForm(Request $request, ContactWeb $contact)
  {
    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Contact: Email: %s, IP: %s, Nom: %s, Prenom: %s, Telephone: %s, Message: %s.',
              $request->getClientIp(),
              $request->request->get('contactWeb')['email'],
              $request->request->get('contactWeb')['nom'],
              $request->request->get('contactWeb')['prenom'],
              $request->request->get('contactWeb')['telephone'],
              $request->request->get('contactWeb')['message']));

    $form = $this->createForm(new ContactWebType(), $contact);
    $form->handleRequest($request);

    if ($form->isValid()) {

      $response = new Response();
      $message = \Swift_Message::newInstance()
        ->setSubject('Message de contact depuis Doc24.fr')
        ->setFrom(array('laura@doc24.fr' => 'Laura de doc24'))
        ->setTo('laura@doc24.fr', 'jlempis@gmail.com');

      $message->setBody($this->renderView('GestimeDoc24Bundle:Mail:contact.html.twig', array(
                                              'nom' => $contact->getNom(),
                                              'email' => $contact->getEmail(),
                                              'prenom' => $contact->getPrenom(),
                                              'telephone' => $contact->getTelephone(),
                                              'message' => $contact->getMessage()
                        )), 'text/html');
      $this->get('mailer')->send($message);
      $response->setStatusCode(201);

      return $response;
    }

    return View::create($form, 201);
  }

  /**
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @return \FOS\RestBundle\View\View|\Symfony\Component\HttpFoundation\Response
   */
  public function postContactAction(Request $request) {

    return $this->processContactForm($request, new ContactWeb());
  }

  /**
   * @param $email
   * @param $password
   * @return array
   * @FOSRest\View
   */
  public function getUserPasswordAction($email, $password)
  {
    $user_manager = $this->get('gestime.utilisateur.manager');
    $factory = $this->get('security.encoder_factory');
    $eventMgr = $this->container->get('gestime.event.manager');

    $user = $user_manager->findWebUserByEmail($email);
    if (!$user) {
      return array("ValidUser"=>false);
    }

    $encoder = $factory->getEncoder($user);
    $isValidUser =  ($encoder->isPasswordValid($user->getPassword(),$password,$user->getSalt())) ? true : false;

    if (!$isValidUser) {
      return array("ValidUser"=>false);
    }

    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Login: Email: %s, IP: %s, Password: %s',
      $email,
      '',
      $password ));

    $patient = $user_manager->getIdPatientByUser($user);
    if ($patient) {
      $nbRdv = count($eventMgr->getRdvWebByPatient($patient));
    } else
    {
      $nbRdv = 0;
    }
    //L'utilisateur est il suspendu ?
    //if ($user_manager->aSuspendre($eventMgr->getRdvWebByPatient($patient)))
    return array("validUser"=>$isValidUser,
                 "id" =>$user->getId(),
                 "nom"=>$user->getNom(),
                 "prenom"=>$user->getPreNom(),
                 "naissance"=>$user->getDateNaissance(),
                 "sexe"=>$user->getSexe(),
                 "nbRdv" => $nbRdv,
                 "etat" => $user->getTrusted(),
                 "notif" => $user->getNotifications());
  }

  /**
   * @param $newPassword
   * @param $email
   * @return bool
   */
  private function sendNewPasswordEmail($newPassword, $email) {

    $message = \Swift_Message::newInstance()
      ->setSubject('Ré-initialisation de votre mot de passe Doc24')
      ->setFrom(array('noreply.doc24.fr' => 'Contact doc 24'))
      ->setTo($email);

    $logo = $message->embed(\Swift_Image::fromPath('../web/logo-doc24.jpg'));

    $message->setBody($this->renderView('GestimeDoc24Bundle:Mail:inscription.html.twig',
      array('newPassword' => $newPassword,
            'logo'        => $logo,
            'email'       => $email)), 'text/html');


    $this->get('mailer')->send($message);

    return true;
  }

  /**
   * PUT Route annotation.
   * @FOSRest\Put("/{email}/reset")
   */
  public function putPasswordAction($email)
  {
    $fos_user_manager = $this->get('fos_user.user_manager');
    $user_manager = $this->get('gestime.utilisateur.manager');
    $doc24Mgr = $this->container->get('gestime.doc24.manager');

    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Demande de password : Email: %s, IP: %s',
      $email,
      'IP'));

    $response = new Response();

    $user = $fos_user_manager->findUserByEmail($email);
    if (!$user) {
      $logger->info(sprintf('Demande de password : Email: %s, l\'utilisateur n\'existe pas.',$email));
      $response->setStatusCode(204);
      return $response;
    }

      $logger->info(sprintf('Demande de password : Email: %s, l\'utilisateur existe',$email));

    $newPassword = $doc24Mgr->getCode(4);
    $user_manager->resetPassword($user, $newPassword);


    $this->sendNewPasswordEmail($newPassword, $email);

    $response->setStatusCode(200);

    return $response;

  }

  /**
   * PUT Route annotation.
   * @FOSRest\Put("/{id}/password/{old}/{new}")
   */
  public function putChangePasswordAction($id, $old, $new)
  {
    $user_manager = $this->get('gestime.utilisateur.manager');
    $factory = $this->get('security.encoder_factory');

    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Changement de password : IdUtil: %d, IP: %s',
      $id,
      'IP'));

    $response = new Response();

    $user = $user_manager->findWebUserById($id);
    if (!$user) {
      $logger->info(sprintf('Changement de password : Id: %d, l\'utilisateur n\'existe pas.',$id));
      $response->setStatusCode(204);
      return $response;
    }


    $encoder = $factory->getEncoder($user);
    $isValidUser =  ($encoder->isPasswordValid($user->getPassword(),$old,$user->getSalt())) ? true : false;

    if ($isValidUser) {
      $user_manager->resetPassword($user, $new);
      $response->setStatusCode(200);
    } else {
      $response->setStatusCode(205);
    }

    return $response;
  }

  /**
   * PUT Route annotation.
   * @FOSRest\Put("/change/{id}/{email}/{sexe}")
   */
  public function putUserAction(Request $request, $id, $email, $sexe) {

    $userManager = $this->container->get('fos_user.user_manager');
    $userMgr = $this->container->get('gestime.utilisateur.manager');
    $response = new Response();
    $user = $userMgr->findWebUserById($id);
    if (!$user) {
      return View::create($form, 204);
    }

    $user->setEmail($email);
    $user->setSexe($sexe);
    $userManager->updateUser($user);

    $statusCode=201;
    $response->setStatusCode($statusCode);

    return $response;
  }

  /**
   * PUT Route annotation.
   * @FOSRest\Delete("/delete/{id}")
   */
  public function deleteCompteAction(Request $request, $id)
  {
    $user_manager = $this->get('gestime.utilisateur.manager');

    $logger = $this->get('monolog.logger.doc24');
    $logger->info(sprintf('Suppression de compte : id: %d, IP: %s',
      $id,
      ''));

    $response = new Response();
    $user = $user_manager->findWebUserById($id);
    if (!$user) {
      $response->setStatusCode(404);
      return $response;
    }

    $user_manager->deleteCompteWeb($user);

    $response->setStatusCode(204);
    return $response;

  }




}
