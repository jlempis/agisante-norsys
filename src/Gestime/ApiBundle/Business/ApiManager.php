<?php

namespace Gestime\apiBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\ApiBundle\Model\UtilisateurWeb;




class ApiManager {
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


  /**
   * @param \Gestime\ApiBundle\Model\UtilisateurWeb $user
   * @return \Gestime\apiBundle\Business\Response
   */


}
