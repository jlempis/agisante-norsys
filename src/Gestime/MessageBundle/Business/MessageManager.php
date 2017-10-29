<?php

namespace Gestime\MessageBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\MessageBundle\Event\MessagePostEvent;
use Gestime\MessageBundle\Event\MessagesEvents;
use Gestime\CoreBundle\Entity\Utilisateur;
use Gestime\CoreBundle\Entity\Message;
use Gestime\CoreBundle\Entity\Medecin;

/**
 * MessageManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class MessageManager
{
    protected $entityManager;
    protected $container;
    protected $synchroMsgMgr;

    /**
     * [__construct description]
     * @param ContainerInterface $container
     * @param EntityManager      $entityManager
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->synchroMsgMgr = $this->container->get('gestime.synchro.message.manager');
    }

    /**
     * [getCategories description]
     * @param Site $site
     * @return querybuilder
     */
    public function getCategories($site)
    {
        return $this->entityManager
                     ->getRepository('GestimeCoreBundle:Categorie')
                     ->getAllBySite($site);
    }

    /**
     * Retourne la signification de la valeur de sens
     * @param Utilisateur $user
     * @return integer
     */
    public function getSensEmission(Utilisateur $user)
    {
        return ($user->hasRole('ROLE_MESSAGERIE_SITE') ? 1 : 2);
    }

    /**
     * Retourne la signification de la valeur de sens
     * @param Utilisateur $user
     * @return integer
     */
    public function getSensReception(Utilisateur $user)
    {
        return ($user->hasRole('ROLE_MESSAGERIE_SITE') ? 2 : 1);
    }

    /**
     * Retourne la liste des médecins gérés par un utilisateur
     * @param Utilisateur $user
     * @return array
     */
    public function getArrayUserMedecins($user)
    {
        if ($this->getSensEmission($user) == 2) {
            //L'utilisateur n'a pas les droits de site
            //Il a forcement, s'il est ici, le droit de gérer les messages
            //Il peut gérer les messages de tous les médecins qu'il gère.
            $medecinsArray = $this->entityManager
                ->getRepository('GestimeCoreBundle:Utilisateur')
                ->getListMedecins($user);

            $medecinsIds = array();
            foreach ($medecinsArray as $medecinId) {
                $medecinsIds[] = $medecinId['idMedecin'];
            }
        } else {
            //L'utilisateur a les droits de site
            //Il a forcement, s'il est ici, le droit de gérer les messages
            //Il peut gérer les messages de tous les médecins qu'il gère.
            $medecinsIds = null;
        }

        return $medecinsIds;
    }

    /**
     * Renvoi d'un SMS
     * @param integer $idSMS
     * @return boolean
     */
    public function ResetSMS($idSMS)
    {
        $sms = $this->entityManager
                  ->getRepository('GestimeCoreBundle:LogMessage')
                  ->findById($idSMS);

        if (count($sms) >0) {
            $sms[0]->setNbEssais(0);
            $sms[0]->setStatut('A');
            $sms[0]->setResultat(0);
            $this->entityManager->persist($sms[0]);
            $this->entityManager->flush();
        }

        return true;
    }

    /**
     * [verifConfigHabilitationOK description]
     * @param Utilisateur $user
     * @return [type]
     */
    public function verifConfigHabilitationOK(Utilisateur $user)
    {
        //Si l'utilisateur n'a ni le role ROLE_MESSAGERIE_SITE, ni le role 'MEDECIN'
        //ou si il a les deux roles
        //C'est une erreur de configuration des rôles
        //Arret sur erreur

        if (!$user->hasRole('ROLE_MESSAGERIE_SITE') &&
            !$user->hasRole('ROLE_MESSAGERIE_MEDECIN')) {
            throw  new \Exception('M001 : Erreur de configuration de messagerie ');
        }

        if ($user->hasRole('ROLE_MESSAGERIE_SITE') &&
            $user->hasRole('ROLE_MESSAGERIE_MEDECIN')) {
            throw  new \Exception('M002 : Erreur de configuration de messagerie ');
        }

        return true;
    }

    /**
     * [getMessagesListePaginee description]
     * @param Utilisateur $user
     * @param boolean     $search
     * @param string      $filtre         (Ex Réception, Envoyés)
     * @param string      $statutLecture  (ex L:Lus, T:Tous)
     * @param integer     $page           (Numero de la page)
     * @param integer     $maxItemParPage
     * @return array
     */
    public function getMessagesListePaginee($user, $medecin, $search, $filtre, $statutLecture, $page, $maxItemParPage)
    {
        //Si la selection est "Tous les médecins",
        //On affiche les massages de tous les médecins auxquels l'utilisateur est habilité ...

        if (is_null($medecin)) {
            $medecinsIds = $this->getArrayUserMedecins($user);
        } else {
            $medecinsIds = array();
            $medecinsIds[] = $medecin->getIdMedecin();
        }

        $sens = ($filtre == 'Envoyes') ? $this->getSensEmission($user) : $this->getSensReception($user);

        $messages = $this->entityManager
                         ->getRepository('GestimeCoreBundle:Message')
                         ->getLisPagineetMessages($search,
                             $sens,
                             $medecinsIds,
                             $statutLecture,
                             $user,
                             $filtre,
                             $page,
                             $maxItemParPage
                         );

        $messagescount = $this->entityManager
                              ->getRepository('GestimeCoreBundle:Message')
                              ->getCountMessages($search,
                                  $sens,
                                  $medecinsIds,
                                  $statutLecture,
                                  $user,
                                  $filtre
                              );

        return array('messagesList' => $messages, 'messagesCount' => $messagescount);
    }

    /**
     * Retourne la réponse à un message
     * @param Message $message
     * @return querybuilder
     */
    public function messageResponse($message)
    {
        return $this->entityManager
                  ->getRepository('GestimeCoreBundle:Message')
                  ->getMessageResponse($message);
    }

    /**
     * [getMessageMirroir description]
     * @param Message $message
     * @return Message
     */
    public function getMessageMirroir(Message $message)
    {
        if (!empty($this->messageResponse($message))) {
            //Le message a une réponse
            return $this->messageResponse($message)[0];
        } else {
            if ($message->isResponse()) {
                //Le message est une réponse
                return $message->getMsgOrigine();
            }
        }
    }



    /**
     * [saveMessage description]
     * @param Message $message
     * @return integer Id du message crée
     */
    public function saveMessage(Message $message)
    {
        $message->setDateEnvoi(new \DateTime());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        //Synchro avec la base V1
        $this->synchroMsgMgr->create_message($message);

        if ($message->getSms()) {
            $event = new MessagePostEvent($message);

            $this->container->get('event_dispatcher')
                ->dispatch(MessagesEvents::onMessagePost, $event);
        }

        return $message->getIdMessage();
    }

    /**
     * [saveResponse description]
     * @param Message     $messageOrigine
     * @param Message     $message
     * @param Utilisateur $user
     * @return integer Id du message crée
     */
    public function saveResponse($messageOrigine, Message $message, Utilisateur $user)
    {
        $message->setMedecins($user->getMedecindefault());
        $message->setDateEnvoi(new \DateTime());
        $message->setMsgOrigine($messageOrigine);
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        //Synchro avec la base V1

        $this->synchroMsgMgr->create_reponse($message);
        return $message->getIdMessage();
    }

    /**
     * [deleteMessage description]
     * @param Message $message
     * @return array
     */
    public function deleteMessage(Message $message)
    {
        $message->setEtat('A');
        $this->entityManager->persist($message);
        $this->entityManager->flush();

        //Synchro avec la base V1
        $this->synchroMsgMgr->delete_message($message);

        $statut['success'] = true;

        return $statut;
    }

    /**
     * [setReadStatus description]
     * @param string  $set
     * @param Message $message [description]
     * @return array
     */
    public function setReadStatus($set, Message $message)
    {
        switch ($set) {
            case 'set':
                  $message->setLu(true);
                  $statut['success'] = true;
                break;

            case 'unset':
                  $message->setLu(false);
                  $statut['success'] = true;
                break;

            default:
                  $statut['success'] = false;
                break;
        }
        if ($statut['success']) {
            $this->entityManager->persist($message);
            $this->entityManager->flush();
        }

        return $statut;
    }

    /**
     * Enregistre un message comme favori pour l'utilisateur
     * @param string      $set
     * @param Message     $message
     * @param Utilisateur $user
     * @return array
     */
    public function setFavori($set, $message, $user)
    {
        switch ($set) {
            case 'set':
                $message[0]->removeFavori($user);
                $message[0]->setFavori($user);
                $statut['success'] = true;
                break;

            case 'unset':
                $message[0]->removeFavori($user);
                $statut['success'] = true;
                break;

            default:
                $statut['success'] = false;
                break;
        }
        if ($statut['success']) {
                $this->entityManager->persist($message[0]);
                $this->entityManager->flush();
        }

        return $statut;
    }

    /**
     * [Récupération de l'objet Message par son Id]
     * @param integer $id
     * @return Message
     */
    public function getMessage($id)
    {
        return $this->entityManager->getRepository('GestimeCoreBundle:Message')
            ->findById($id);
    }

    /**
     * [getCompteursMessages description]
     * @param Utilisateur $user
     * @return querybuilder
     */
    public function getCompteursMessages($user)
    {
        return $this->entityManager->getRepository('GestimeCoreBundle:Message')
                                   ->getCountMessagesNonLus($user,
                                       $this->getSensReception($user),
                                       $this->getArrayUserMedecins($user)
                                   );
    }
}
