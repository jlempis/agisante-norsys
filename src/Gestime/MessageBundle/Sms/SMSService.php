<?php

namespace Gestime\MessageBundle\Sms;

use Gestime\CoreBundle\Entity\Message;
use Gestime\CoreBundle\Entity\Evenement;
use Gestime\CoreBundle\Entity\LogMessage;

/**
 * Gestion des SMS
 *
 */
class SMSService
{
    protected $clientCode;
    protected $passCode;
    protected $url;
    protected $entityManager;

    /**
     * [__construct description]
     * @param EntityManager $entityManager
     * @param string        $user
     * @param string        $password
     * @param string        $url
     */
    public function __construct($entityManager, $user = 'sophiesylvie', $password = 'up74u37f51', $url = 'https://api.msinnovations.com/http/sendSms_v8.php')
    {
        $this->entityManager = $entityManager;
        $this->clientCode = $user;
        $this->passCode = $password;
        $this->url = $url;
    }

    /**
     * Prépare le message
     * @param  [type] $idMessage [description]
     * @param  [type] $texte     [description]
     * @param  [type] $numero    [description]
     * @return [Json            [description]
     */
    protected function formatteMessage($texte, $numero, $idMessage=0)
    {
        $smsData = array();
        $smsData['DATA']['MESSAGE'] = $texte;
        $smsData['DATA']['CLIMSGID'] = $idMessage;
        $smsData['DATA']['TPOA'] = "SMF";
        $numero = $numero;
        $smsData['DATA']['SMS'][]['MOBILEPHONE'] = $numero;

        return json_encode($smsData);
    }

    /**
     * [envoiFlux description]
     * @param  string $url
     * @param  array  $fields
     * @return array
     */
    protected function envoiFlux($url, $fields)
    {
        $fieldsString = '';

        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result;
    }

    protected function envoiMessage($message, $numero, $idMessage)
    {
        $fields = array(
        'clientcode' => urlencode($this->clientCode),
        'passcode'   => urlencode($this->passCode),
        'smsData'    => urlencode($this->formatteMessage($message, $numero, $idMessage)),
        );

        return $this->envoiFlux($this->url, $fields);
    }

    /**
     * Envoi un message directement
     * @param string $message
     */
    /**
     * sendMessage
     * @param  integer $idSms  Numero (clientId)
     * @param  string  $texte
     * @param  string  $numero
     * @return array
     */
    public function sendMessage($idSms, $texte, $numero)
    {
        $result = $this->envoiMessage($idSms, $texte, $numero);
    }

    /**
     *
     * Ajoute à la file RabbitMQ
     * @param Evenement $evenement
     * @param string    $texte
     */
    public function addRappelToQueue(Evenement $evenement, $texte)
    {
        $logmessage = new LogMessage();
        $logmessage->setMessageId($evenement->getIdEvenement());
        $logmessage->setNumeroEnvoi($evenement->getPatient()->getTelephone());
        $logmessage->setType('R');
        $logmessage->setUserId($evenement->getMedecin()->getIdMedecin());
        $logmessage->setTexte($texte);
        $logmessage->setPatientId($evenement->getPatient()->getId());
        $logmessage->setNbEssais(0);
        $logmessage->setDateEnvoi(new \DateTime('now'));
        $logmessage->setStatut('A');
        $logmessage->setMedecin($evenement->getMedecin());
        $logmessage->setResultat(0);

        $this->entityManager->persist($logmessage);
        $this->entityManager->flush();
    }

    /**
     * Ajoute à la file RabbitMQ
     * @param Message $message
     */
    public function addMessageToQueue(Message $message)
    {
        foreach ($message->getMedecins() as $medecin) {
            $logmessage = new LogMessage();
            $logmessage->setMessageId($message->getIdMessage());
            $logmessage->setNumeroEnvoi($medecin->getTelephoneSMS()->getNumero());
            $logmessage->setType('M');
            $logmessage->setUserId($medecin->getIdMedecin());
            $logmessage->setTexte($message->getObjet());
            $logmessage->setNbEssais(0);
            $logmessage->setDateEnvoi(new \DateTime('now'));
            $logmessage->setStatut('A');
            $logmessage->setMedecin($medecin);
            $logmessage->setResultat(0);

            $this->entityManager->persist($logmessage);
        }

        $this->entityManager->flush();
    }

    /**
     * Traite la file d'attente des SMS
     * @param  integer $idMessage
     * @param  string  $texte
     * @param  string  $numero
     * @return boolean
     */
    public function traiteQueue($idMessage, $texte, $numero)
    {
        $messages = $this->entityManager
                           ->getRepository('GestimeCoreBundle:LogMessage')
                           ->findById($idMessage);
        if (!empty($messages)) {
            $messageTraite = $messages[0];
        }
        $result = $this->envoiMessage($messageTraite->getMessageId(), $texte, $numero);
        $messageTraite->addEssai();
        $messageTraite->setResultat($result['status']);
        if ($result['status'] == 100) {
            $messageTraite->setCampagneId($result['campaignId']);
            $messageTraite->setStatut('R');
        } else {
            $messageTraite->setStatut('E');
        }

        $this->entityManager->persist($messageTraite);
        $this->entityManager->flush();

        return true;
    }

    /**
     * [ReceptionReponse description]
     * @param Message $message [description]
     */
    public function ReceptionReponse(Message $message)
    {
        $urlReception = 'https://api.msinnovations.com/http/getPull_v8.php';
        $fields = array(
        'clientcode' => urlencode($this->clientCode),
        'passcode'   => urlencode($this->passCode),
        'campId'    => urlencode($message->getCampagneId()),
        );

        $reponse =  $this->envoiFlux($urlReception, $fields);

        if (array_key_exists('cliMsgId', $reponse)) {
            $logmessage = $this->entityManager
                            ->getRepository('GestimeCoreBundle:LogMessage')
                            ->findByMessage_Id(intval($reponse['cliMsgId']));

            if (!empty($logmessage) && !is_null($reponse['mos'][0]['message'])) {
                $messageReponse = new LogMessage();
                $messageReponse->setDateReception(new \DateTime('now'));
                $messageReponse->setMessageId($logmessage[0]->getMessageId());
                $messageReponse->setNumeroEnvoi($logmessage[0]->getNumeroEnvoi());
                $messageReponse->setResultat(0);
                $messageReponse->setTexte($reponse['mos'][0]['message']);
                $messageReponse->setCampagneId($logmessage[0]->getCampagneId());
                $messageReponse->setUserId($logmessage[0]->getUserId());
                $messageReponse->setType('M');
                $messageReponse->setNbEssais(0);
                $messageReponse->setStatut('T');

                $this->entityManager->persist($messageReponse);
                $this->entityManager->flush();
            }
        }
    }

    /**
     * Recupère le nombre de SMS Restants
     * @return integer
     */
    public function getStatus()
    {
        $urlInfosSMS =  'https://api.msinnovations.com/http/getInfo_v8.php';

        $fields = array(
        'clientcode' => urlencode($this->clientCode),
        'passcode'   => urlencode($this->passCode),
        );

        $fieldsString = '';

        foreach ($fields as $key => $value) {
            $fieldsString .= $key.'='.$value.'&';
        }
        rtrim($fieldsString, '&');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $urlInfosSMS);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return (json_decode($result)->credits)/15;
    }
}
