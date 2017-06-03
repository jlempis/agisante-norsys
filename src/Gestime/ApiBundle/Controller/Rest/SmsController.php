<?php

namespace Gestime\ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations as FOSRest;

use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Gestime\ApiBundle\Model\RdvWeb;
use Gestime\CoreBundle\Entity\Utilisateur;
use Gestime\ApiBundle\Form\Type\RdvWebType;


/**
 * ReservationController
 *
 * @FOSRest\NamePrefix("api2_")
 */
class SmsController extends Controller {


    /**
     * @param $numero
     * @return array
     * @FOSRest\View
     */
    public function getValidationCodeAction($numero)
    {
        $doc24Mgr = $this->container->get('gestime.doc24.manager');
        $code = $doc24Mgr->envoiSmsInscription($numero);
        $logger = $this->get('monolog.logger.doc24');
        $logger->info(sprintf('Demande de code : Numero: %s, IP: %s, Telephone: %s, Code Obtenu: %s',
            $numero,
            ''));
        return array("smsSend"=>$code);
    }

    /**
     * @param $smsId
     * @param $smsMoId
     * @param $receptionDate
     * @param $phoneNumber
     * @param $campaignId
     * $message
     * @return array
     *
     * @FOSRest\Get("/sms/{smsId}/{smsMoId}/{receptionDate}/{phoneNumber}/{camapignId}/{message}")
     * @FOSRest\View
     */
    public function getReponseSMSAction($smsId, $smsMoId, $receptionDate, $phoneNumber, $campaignId, $message) {
        $doc24Mgr = $this->container->get('gestime.doc24.manager');


        $user_manager = $this->get('gestime.utilisateur.manager');

        return array("codeOk" => false);
    }

}
