<?php

namespace Gestime\SynchroV1Bundle\Business;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Evenement;
use Gestime\CoreBundle\Entity\Personne;

use Gestime\SynchroV1Bundle\Entity\Personne as PersonneV1;

/**
 * SynchroManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class SynchroPersonneManager
{
    /**
     * @var
     */
    protected $managerRegistry;
    /**
     * @var ContainerInterface
     */
    protected $container;


    /**
     * @param ContainerInterface $container
     * @param EntityManager $entityManager
     */
    public function __construct(ContainerInterface $container, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->container = $container;
    }


    /**
     * @param Personne $personne
     */
    public function create_peronne(Personne $personne)
    {
        $personneV1 = new PersonneV1();

        $entityManager = $this->managerRegistry->getManagerForClass(get_class($personneV1));
        $entityManager->persist($personneV1);
        $entityManager->flush();
    }

    /**
     * @param Personne $personne
     */
    public function update_personne(Personne $personne)
    {
    }

    /**
     * @param Personne $personne
     */
    public function delete_personne(Personne $personne)
    {
    }
}
