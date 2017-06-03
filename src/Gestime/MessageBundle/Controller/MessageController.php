<?php
/**
 * Message Controller class file
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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Gestime\CoreBundle\Entity\Message;
use Gestime\CoreBundle\Entity\MessageListSearch;
use Gestime\MessageBundle\Form\Type\MessageNewType;
use Gestime\MessageBundle\Form\Type\MessageResponseType;
use Gestime\MessageBundle\Form\Type\MessageListSearchType;

/**
 * Message
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class MessageController extends Controller
{
    /**
     * @Route("/liste/{filtre}/{page}", name="messages_liste", defaults={"search" = false, "filtre" = "Reception", "page" = 1})
     * @Secure("ROLE_GESTION_MESSAGERIE")
     * @Template("GestimeMessageBundle:Messages:liste.html.twig")
     *
     * [ListeAction description]
     * @param string  $filtre
     * @param integer $page
     * @param Request $request
     * @param string  $search
     * @return Template
     */
    public function ListeAction($filtre, $page, Request $request, $search = false )
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $messageMgr->verifConfigHabilitationOK($this->getUser());

        $maxItemParPage = $this->container->getParameter('maxitemspage');
        $categories = $messageMgr->getCategories($this->getUser()->getSite());

        $searchText = new MessageListSearch();
        $form = $this->createForm(new MessageListSearchType(), $searchText);
        $form->handleRequest($request);

        if ($form->getData()->texte  != '') {
            $search = true;
            $filtre = $form->getData()->texte;
        }

        $listMessagesArray = $messageMgr->getMessagesListePaginee($this->getUser(),
            $search,
            $filtre,
            $form->getData()->action,
            $page,
            $maxItemParPage
        );

        $pagination = array(
            'page' => $page,
            'route' => 'messages_liste',
            'pages_count' => ceil($listMessagesArray['messagesCount']/ $maxItemParPage),
            'route_params' => array('filtre' => $filtre),
        );

        return array('form' => $form->createView(),
                      'categories' => $categories,
                      'compteurs' => array('nonlus' => 0, 'nonluFavoris' => 0),
                      'filtre' => ($search === true) ? 'Recherche' : $filtre,
                      'messagesCount' => $listMessagesArray['messagesCount'],
                      'messages' => $listMessagesArray['messagesList'],
                      'pagination' => $pagination,
                      'maxItemParPage' =>  $maxItemParPage,
                      'action' => 'Liste des messages',
                      'menuactif' => 'Messages', );
    }

    /**
     * @Route("/view/{idMessage}", name="messages_view")
     * @Method("GET")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     * @Template("GestimeMessageBundle:Messages:view.html.twig")
     *
     * @param request $request
     * @param Message $message
     * @return Template
     */
    public function ViewAction(Request $request, Message $message)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $categories = $messageMgr->getCategories($this->getUser()->getSite());
        $messageMirroir = $messageMgr->getMessageMirroir($message);

        $messageMgr->setReadStatus('set', $message);

        return array('categories'      => $categories,
                      'message'         => $message,
                      'message_mirroir' => $messageMirroir,
                      'compteurs'       => array('nonlus' => 0, 'nonluFavoris' => 0),
                      'action'          => 'Visualisation d\'un message',
                      'menuactif'       => 'Messages', );
    }

    /**
     * @Route("/reponse/{idMessage}", name="messages_reponse")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     * @Template("GestimeMessageBundle:Messages:response.html.twig")
     *
     * @param request $request
     * @param Message $message
     * @return Template
     */
    public function ResponseAction(Request $request, Message $message)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $categories = $messageMgr->getCategories($this->getUser()->getSite());
        $sens = $messageMgr->getSensEmission($this->getUser());

        $messageOrigine = $message;

        $message = new Message($this->getUser(), $sens);
        $message->setSujet('Rep: '.$messageOrigine->getSujet());

        $request = $this->getRequest();
        $form = $this->createForm(new MessageResponseType(), $message);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $messageMgr->saveResponse($messageOrigine, $message, $this->getUser());

            return $this->redirect($this->generateUrl('messages_liste'));
        }

        return array('categories' => $categories,
                      'message' => $message,
                      'message_origine' => $messageOrigine,
                      'compteurs' => array('nonlus' => 0, 'nonluFavoris' => 0),
                      'action' => 'Composer un message',
                      'form' => $form->createView(),
                      'menuactif' => 'Messages',
        );
    }

    /**
     * @Route("/compose", name="message_compose")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     * @Template("GestimeMessageBundle:Messages:new.html.twig")
     *
     * @param request $request
     * @return Template
     */
    public function NewAction(Request $request)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $sens = $messageMgr->getSensEmission($this->getUser());

        $categories = $messageMgr->getCategories($this->getUser()->getSite());

        $message = new Message($this->getUser(), $sens);
        $request = $this->getRequest();

        $form = $this->createForm(new MessageNewType(), $message,
            array('attr' => array('sens' => $sens,
                              'user' => $this->getUser(), ),
            )
        );

        $form->handleRequest($request);

        if ($form->isValid()) {
            $messageMgr->saveMessage($message);

            return $this->redirect($this->generateUrl('messages_liste'));
        }

        return array('categories' => $categories,
                      'message' => $message,
                      'compteurs' => array('nonlus' => 0, 'nonluFavoris' => 0),
                      'action' => 'Composer un message',
                      'form' => $form->createView(),
                      'menuactif' => 'Messages',
                      'messageFromSite' => ($sens == 1),
        );
    }

    /**
     * @Route("/print/{idMessage}", name="messages_print", options={"expose"=true})
     * @Method("GET")
     * @Secure("ROLE_GESTION_MESSAGERIE")
     * @Template("GestimeMessageBundle:Messages:print.html.twig")
     *
     * @param request $request
     * @param Message $message
     * @return Template
     */
    public function PrintAction(Request $request, Message $message)
    {
        $messageMgr = $this->container->get('gestime.message.manager');
        $messageMirroir = $messageMgr->getMessageMirroir($message);

        return array('message'         => $message,
                      'message_mirroir' => $messageMirroir, );
    }
}
