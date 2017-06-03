<?php

namespace Gestime\SynchroV1Bundle\Business;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Evenement;
use Gestime\CoreBundle\Entity\Message;

use Gestime\SynchroV1Bundle\Entity\Message as MessageV1;

/**
 * SynchroManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class SynchroMessageManager
{
    /**
     * @var
     */
    protected $managerRegistry;
    protected $entityManagerV1;
    protected $entityManagerV2;

    /**
     * @var ContainerInterface
     */
    protected $container;


    /**
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     */
    public function __construct(ContainerInterface $container, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->container = $container;
        $this->entityManagerV1 = $this->managerRegistry->getManagerForClass('Gestime\SynchroV1Bundle\Entity\Message');
        $this->entityManagerV2 = $this->managerRegistry->getManagerForClass('Gestime\CoreBundle\Entity\Message');
    }

    private function getMessageV1Id($message) {
        $messagesV1 = $this->entityManagerV1
            ->getRepository('GestimeSynchroV1Bundle:Message')
            ->findById($message->getMsgOrigine()->getOldMsgId());

        return $messagesV1[0];
    }

    /**
     * @param Evenement $evenement
     */
    public function create_reponse(Message $message){
        $messageV1 = $this->getMessageV1Id($message);

        $messageV1->setLreponse($message->getSujet().' '.$message->getObjet());
        $this->entityManagerV1->persist($messageV1);
        $this->entityManagerV1->flush();
    }

    /**
     * @param array $evenementV1
     * @return Evenement
     */
    public function CreateMessageFromMessageV1($user, $messageV1)
    {
        if ($messageV1['emmetteur'] == 0) {
            $sens=1;
            $idMedecin=$messageV1['destinataire'];
        } else {
            $sens=2;
            $idMedecin=$messageV1['emmetteur'];
        }

        $message = new Message($user, $sens);

        //Récupération du médecin
        $medecin = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Medecin')
            ->findOneByIdAgenda($idMedecin);

        //Récupération du message d'origine
        $msgOrigine = $this->entityManagerV2
            ->getRepository('GestimeCoreBundle:Message')
            ->findOneByOldMsgId($messageV1['id']);

        $message->setEtat('V');
        $message->setDateEnvoi($messageV1['dateMessage']);
        $message->addMedecin($medecin);
        $message->setSujet('Importé de gestime V1');
        $message->setObjet($messageV1['objet']);
        $message->setOldMsgId($messageV1['id']);

        if ($messageV1['reponse'] == 1) {
            $message->setMsgOrigine($msgOrigine);
        }

        $this->entityManagerV2->persist($message);
        $this->entityManagerV2->flush();

    }



            /**
     * @param Evenement $evenement
     */
    public function create_message(Message $message)
    {
        $messageV1 = new MessageV1();
        $messageV1->setDateMessage($message->getDateEnvoi());
        $messageV1->setDemande($message->getSujet().' '. $message->getObjet());

        $medecin = $message->getMedecins()->first();
        if ($message->getSens() == 1) {
            $messageV1->setDestinataire($medecin->getIdAgenda());
            $messageV1->setEmetteur(0);
        } else {
            $messageV1->setDestinataire(0);
            $messageV1->setEmetteur($medecin->getIdAgenda());
        }
        $messageV1->setReponse(null);
        $messageV1->setObjet($message->getObjet());
        $messageV1->setSujet($message->getSujet());
        $messageV1->setSujetObjet($message->getSujet().' '. $message->getObjet());
        $messageV1->setStatut(($message->getLu() == 1)?'L':null);
        $messageV1->setReference(null);
        $messageV1->setRegroupement(null);
        $messageV1->setType(null);
        $messageV1->setSuppr(null);

        $this->entityManagerV1->persist($messageV1);
        $this->entityManagerV1->flush();

        $message->setOldMsgId($messageV1->getId());
        $this->entityManagerV2->persist($message);
        $this->entityManagerV2->flush();
    }

    /**
     * @param Evenement $evenement
     */
    public function update_message(Message $message)
    {
        //Gestion des messages Lus
    }

    /**
     * @param Evenement $evenement
     */
    public function delete_message(Evenement $message)
    {
        $messageV1 = $this->getMessageV1Id($message);
        $this->entityManagerV1->delete($messageV1);
        $this->entityManagerV1->flush();

    }
}
