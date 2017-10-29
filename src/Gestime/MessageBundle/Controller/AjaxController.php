<?php
/**
 * Ajax Controller class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
namespace Gestime\MessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Ajax
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class AjaxController extends Controller
{
    /**
     * @Route("/suppr/{idMessage}", name="messages_ajax_suppr",options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     *
     * @param integer $idMessage
     * @return json
     */
    public function SupprAction($idMessage)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager
            ->getRepository('GestimeCoreBundle:Message')
            ->findByIdMessage($idMessage);

        if (count($message) == 0) {
            $statut['success'] = false;
        } else {
            $statut = $messageMgr->deleteMessage($message[0]);
        }

        $response = new JsonResponse();
        $response->setContent(json_encode($statut));

        return $response;
    }

    /**
     * @Route("/read/{set}/{idMessage}", name="messages_ajax_set_read", options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     *
     * @param integer $set
     * @param integer $idMessage
     * @return json
     */
    public function ReadAction($set, $idMessage)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager
            ->getRepository('GestimeCoreBundle:Message')
            ->findByIdMessage($idMessage);

        if (count($message) == 0) {
            $statut['success'] = false;
        } else {
            $statut = $messageMgr->setReadStatus($set, $message[0]);
        }

        $response = new JsonResponse();
        $response->setContent(json_encode($statut));

        return $response;
    }

    /**
     * @Route("/favori/{set}/{idMessage}", name="messages_ajax_set_favori", options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     *
     * @param integer $set
     * @param integer $idMessage
     * @return json
     */
    public function FavoriAction($set, $idMessage)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $entityManager = $this->getDoctrine()->getManager();
        $message = $entityManager
            ->getRepository('GestimeCoreBundle:Message')
            ->findByIdMessage($idMessage);

        if (count($message) == 0) {
            $statut['success'] = false;
        } else {
            $statut = $messageMgr->setFavori($set, $message, $this->getUser());
        }
        $response = new JsonResponse();
        $response->setContent(json_encode($statut));

        return $response;
    }

    /**
     * @Route("/compteurs/sms", name="messages_ajax_compteurs_sms", options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     *
     * @return json
     */
    public function CompteursSMSAction()
    {
        $serviceSMS = $this->get('gestime.sms');
        $result['compteurs'] = floor($serviceSMS->getStatus());
        $result['success'] = true;



        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }

    /**
     * @Route("/compteurs", name="messages_ajax_compteurs", options={"expose"=true})
     * @Method("POST")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     *
     * @return json
     */
    public function CompteursAction()
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $result['compteurs'] = $messageMgr->getCompteursMessages($this->getUser());
        $result['success'] = true;

        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }

    /**
     * @Route("/sms/reset/{idSms}", name="reset_sms", options={"expose"=true})
     * @Secure("ROLE_GESTION_MESSAGERIE")
     *
     * @param integer $idSms
     * @return json
     */
    public function ResetSMSAction($idSms)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $result = $messageMgr->ResetSMS($idSms);

        $response = new JsonResponse();
        $response->setContent(json_encode($result));

        return $response;
    }
}
