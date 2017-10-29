<?php

namespace Gestime\UserBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Utilisateur;
use Gestime\ApiBundle\Model\UtilisateurWeb;
use Gestime\ApiBundle\Model\InfoUserWeb;

/**
 * UtilisateurManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class UtilisateurManager
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
     * Recupere un Utilisateur par son Id
     * @param integer $utilisateurId
     * @return Utilisateur, boolean
     */
    public function getUtilisateurById($utilisateurId)
    {
        $utilisateur = $this->entityManager
            ->getRepository('GestimeCoreBundle:Utilisateur')
            ->findById($utilisateurId);

        if (count($utilisateur) > 0) {
            return $utilisateur[0];
        }

        return false;
    }

    /**
     * Créé un utilisateur en base
     * @param Site        $site        site de l'utilisateur
     * @param Form        $userForm
     * @param Utilisateur $utilisateur
     * @param Boolean     $webUser
     * @return integer id de l'utilisateur crée
     */
    public function saveUtilisateur($site, $userForm, Utilisateur $utilisateur)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userForm;
        $user->setSite($site);
        $user->setPlainPassword($user->getPassword());
        $user->setEnabled(true);

        $userManager->updateUser($user);

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return $utilisateur->getId();
    }

    /**
     * Créé un utilisateur en base
     * @param Utilisateur $utilisateur
     * @param array       $data               données du formulaire de saisie
     * @param array       $medecinsAvantModif
     * @return integer id de l'utilisateur crée
     */
    public function editUtilisateur(Utilisateur $utilisateur, $userForm, $medecinsAvantModif)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        //Suppression des médecins autorisés si besoin
        foreach ($utilisateur->getMedecins() as $medecin) {
            foreach ($medecinsAvantModif as $key => $toDel) {
                if ($toDel->getIdMedecin() === $medecin->getIdMedecin()) {
                    unset($medecinsAvantModif[$key]);
                }
            }
        }

        foreach ($medecinsAvantModif as $medecin) {
            $medecin->getUtilisateurs()->removeElement($medecin);
            $this->entityManager->persist($medecin);
        }

        foreach ($utilisateur->getMedecins() as $medecin) {
            $medecin->addUtilisateur($utilisateur);
            $this->entityManager->persist($medecin);
        }

        $user = $userForm;
        $user->setPlainPassword($user->getPassword());
        $userManager->updateUser($user);

        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();
    }

    /**
     * Supprime un utilisateur en base
     * @param Utilisateur $utilisateur
     * @return boolean
     */
    public function deletedUtilisateur(Utilisateur $utilisateur)
    {
        $this->entityManager->remove($utilisateur);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Créé un utilisateur en base
     * @param Utilisateur $utilisateur
     * @param string      $password    nouveau mot de passe
     * @return string
     */
    public function changeUserPassword(Utilisateur $utilisateur, $password)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $utilisateur->setPlainPassword($password);
        $userManager->updateUser($utilisateur);
        $this->entityManager->persist($utilisateur);
        $this->entityManager->flush();

        return 'success';
    }

    public function UtilisateurExiste($email) {
        return (null != $this->entityManager
                          ->getRepository('GestimeCoreBundle:Utilisateur')
                          ->findOneByEmail($email));
    }

    private function getWebUserSite() {
      return $this->entityManager
          ->getRepository('GestimeCoreBundle:Site')
          ->findOneById(1);
    }

    private function generateWebUserName($prenom, $nom) {
      $aujourdhui = new \Datetime();

      return substr(strtolower($prenom),0,1).strtolower($nom).$aujourdhui->format("Ymdhis");
    }

    public function saveWebUser(UtilisateurWeb $userWeb) {
      $userManager = $this->container->get('fos_user.user_manager');
      $site = $this->getWebUserSite();

      $user = $userManager->createUser();

      $user->setSite($site);
      $user->setAuthCode('');
      $user->setNom($userWeb->getNom());
      $user->setPrenom($userWeb->getPrenom());
      $user->setSexe($userWeb->getSexe());
      $user->setEmail($userWeb->getEmail());
      $user->setDateNaissance($userWeb->getNaissance());
      $user->setUserWeb(true);
      $user->setTrusted(true);
      $user->setPlainPassword($userWeb->getPassword());
      $user->setUsername($this->generateWebUserName($userWeb->getPrenom(), $userWeb->getNom()));
      $user->setEnabled(false);
      $user->setNotifications(0);

      $userManager->updateUser($user);

      return $user->getId();
    }

  public function updateWebUser(InfoUserWeb $userWeb) {
    $userManager = $this->container->get('fos_user.user_manager');

    $user = $userManager->findUserByEmail($userWeb->getEmail());
    if (!$user) {
      return false;
    }
    $user->setNom($userWeb->getNom());
    $user->setPrenom($userWeb->getPrenom());
    $user->setSexe($userWeb->getSexe());
    $user->setDateNaissance($userWeb->getNaissance());
    $userManager->updateUser($user);

    return true;
  }

    public function resetPassword($user, $newPassword) {
      $userManager = $this->container->get('fos_user.user_manager');
      $user->setPlainPassword($newPassword);
      $userManager->updateUser($user);

      return true;
    }

    public function findWebUserByEmail($email) {
      $user = $this->entityManager
        ->getRepository('GestimeCoreBundle:Utilisateur')
        ->findOneByEmail($email);
      if ($user !== null) {
        if ($user->isUserWeb() && !$user->isLocked()) {
          return $user;
        }
      }

      return null;
    }

    public function findWebUserById($idUtilisateur) {
      $user = $this->entityManager
        ->getRepository('GestimeCoreBundle:Utilisateur')
        ->findOneById($idUtilisateur);
      if ($user !== null) {
        if ($user->isUserWeb() && !$user->isLocked()) {
          return $user;
        }
      }

      return null;
    }

    public function getIdPatientByUser($user) {
      $patient = $this->entityManager
        ->getRepository('GestimeCoreBundle:Personne')
        ->findOneByEmail($user->getEmail());
      if ($patient !== null) {
          return $patient;
      }

      return null;
    }

    public function majCodeAuthentification($email, $code, $expiration, $numero) {
      $user = $this->findWebUserByEmail($email);
      if ($user) {
        $user->setAuthCode($code);
        $user->setAuthCodeExpiresAt($expiration);
        $user->setPhoneNumber($numero);

        $userManager = $this->container->get('fos_user.user_manager');
        $userManager->updateUser($user);
      }
    }

  public function deleteCompteWeb($user) {
    $userManager = $this->container->get('fos_user.user_manager');
    $userManager->deleteUser($user);

  }


}
