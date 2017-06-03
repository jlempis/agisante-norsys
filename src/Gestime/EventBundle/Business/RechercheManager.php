<?php

namespace Gestime\EventBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RechercheManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RechercheManager
{
    protected $entityManager;
    protected $container;

    /**
     * [__construct description]
     * @param ContainerInterface $container
     * @param EntityManager      $entityManager
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
}
