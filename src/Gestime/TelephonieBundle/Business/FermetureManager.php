<?php

namespace Gestime\TelephonieBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Fermeture;

/**
 * FermetureManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class FermetureManager
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

    /**
     * Créé une fermeture en base
     * @param Fermeture $fermeture
     * @return integer id de la fermeture crée
     */
    public function saveFermeture(Fermeture $fermeture)
    {
        $this->entityManager->persist($fermeture);
        $this->entityManager->flush();

        return $fermeture->getIdFermeture();
    }

    /**
     * Supprimer un medecin en base
     * @param Fermeture $fermeture
     * @return boolean
     */
    public function deleteFermeture(Fermeture $fermeture)
    {
        $this->entityManager->remove($fermeture);
        $this->entityManager->flush();

        return true;
    }
}
