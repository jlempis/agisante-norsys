<?php

namespace Gestime\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * AuthenticationController
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class AuthenticationController extends Controller
{
    /**
     *
     * @return JsonResponse
     */
    public function pingAction()
    {
        return new JsonResponse();
    }
}
