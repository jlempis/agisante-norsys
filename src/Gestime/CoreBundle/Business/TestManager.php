<?php

namespace Gestime\CoreBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\BrowserKit\Cookie;

/**
 * Helper Tests
 *
 */
class TestManager
{
    protected $container;

    /**
     * [__construct description]
     * @param ContainerInterface $container [description]
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * [getAuthenticatedSession description]
     * @param  [type] $client [description]
     * @return [type]         [description]
     */
    public function getAuthenticatedSession($client)
    {
        return $this->_getAuthenticatedSession($client, 'main', 'usertest');
    }

    /**
     * Permet au test de passer l'authentification
     * @param  [type] $client       [description]
     * @param  [type] $firewallName [description]
     * @param  [type] $username     [description]
     * @return [type]               [description]
     */
    private function _getAuthenticatedSession($client, $firewallName, $username)
    {
        $session = $this->container->get('session');

        $userManager = $this->container->get('fos_user.user_manager');
        $loginManager = $this->container->get('fos_user.security.login_manager');

        $user = $userManager->findUserBy(array('username' => $username));
        $loginManager->loginUser($firewallName, $user);

         // save the login token into the session and put it in a cookie
         $this->container->get('session')
                         ->set('_security_'.$firewallName,
                             serialize($this->container
                                                ->get('security.token_storage')
                                                ->getToken()));

        $this->container->get('session')->save();
        $client->getCookieJar()
                ->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }
}
