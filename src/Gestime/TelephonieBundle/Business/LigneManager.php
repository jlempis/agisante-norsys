<?php

namespace Gestime\TelephonieBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Ligne;

/**
 * LigneManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class LigneManager
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
     * @param Ligne $ligne
     */
    public function saveLigne(Ligne $ligne)
    {
        $this->entityManager->persist($ligne);
        $this->entityManager->flush();
    }

    /**
     * @param Ligne $ligne
     */
    public function deleteLigne(Ligne $ligne)
    {
        $this->entityManager->remove($ligne);
        $this->entityManager->flush();
    }

    /**
     * Retourne vrai si la ligne est affectée à un abonné
     * @param Ligne $ligne
     * @return boolean
     */
    public function estAffectee(Ligne $ligne)
    {
        return ($ligne->getAffectations()->count() >0);
    }
}
