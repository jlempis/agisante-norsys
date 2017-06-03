<?php

namespace Gestime\AgendaBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * ConsigneManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class ConsigneManager
{
    protected $entityManager;
    protected $container;

    /**
     * [__construct description]
     * @param ContainerInterface $container     [description]
     * @param [type]             $entityManager [description]
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }
}
