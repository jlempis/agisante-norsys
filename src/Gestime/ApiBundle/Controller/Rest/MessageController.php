<?php

namespace Gestime\ApiBundle\Controller\Rest;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;

use FOS\RestBundle\Controller\Annotations\Route,
    FOS\RestBundle\Controller\Annotations\NoRoute,
    FOS\RestBundle\Controller\Annotations\Get,
    FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * MessageController
 *
 * @author Jean-Loup Empis <jlempis@gmail.com>
 *
 */
class MessageController extends Controller
{
    /**
     *
     * @var Request $request
     * @return array
     * @throws AccessDeniedException
     * @FOSRest\View(statusCode=201)
     * @Post("/message")
     */
    public function postMessageAction(Request $request)
    {
        if (!$this->isGranted('ROLE_API')) {
            //throw new AccessDeniedException();
        }

        $messageV1 = unserialize($request->request->get('message'));
        $synchroV1Mgr = $this->get('gestime.synchro.message.manager');
        $event = $synchroV1Mgr->CreateMessageFromMessageV1($this->getUser(), $messageV1);

    }

    /**
     *
     * @param integer $id Message id
     * @FOSRest\View
     * @return FOS\RestBundle\View\View HTTP response
     *
     * @ApiDoc(
     *   section="Messages",
     *   description="Return a message"
     * )
     */
    public function getMessageAction($id)
    {
        try {
            $manager = $this->get('gestime.message.manager');
            $message = $manager->getMessage($id);

            $statusCode = 200;

        } catch (Exception $e) {
            $statusCode = '400';
        }

        return View::create()
                    ->setStatusCode($statusCode)
                    ->setData($message);

    }

    /**
     *
     * @return FOS\RestBundle\View\View HTTP response
     *
     * @ApiDoc(
     *   section="Messages",
     *   description="List messages",
     *   statusCodes={
     *     201="Message created",
     *     422="Unprocessable entity"
     *   }
     * )
     */
    public function getMessagesAction()
    {
        $messages = $manager->findAll();

        return View::create()
                    ->setStatusCode('201')
                    ->setData($messages);

    }

    /**
     *
     * @var Request $request
     * @return array
     * @FOSRest\View(statusCode=201)
     * @Delete("/message")/{id}")
     */
    public function deleteMessageAction($id)
    {
        $manager = $this->get('gestime.message.manager');
        $message = $manager->getMessage($id);
        $manager->deleteMessage($message);

    }
}
