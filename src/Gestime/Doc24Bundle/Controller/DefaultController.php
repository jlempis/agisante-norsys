<?php

namespace Gestime\Doc24Bundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/mail/{name}")
     * @Template("")
     */
    public function indexAction($name)
    {

        $message = \Swift_Message::newInstance()
            ->setSubject('Test de message')
            ->setFrom('inscription@doc24.fr')
            ->setTo('web-9yBUZ6@mail-tester.com')
            ->setBody($this->renderView('GestimeDoc24Bundle:Mail:inscription.txt.twig', array('name' => $name)))
        ;
        $this->get('mailer')->send($message);

        return array('Message envoyé');
    }
}
