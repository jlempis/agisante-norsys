<?php

namespace Gestime\TelephonieBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\Repondeur;

/**
 * RepondeurManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RepondeurManager
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
     * Créé un répondeur en base
     * @param Repondeur $repondeur
     * @return integer id du répondeur créé
     */
    public function save_repondeur(Repondeur $repondeur)
    {
        $this->entityManager->persist($repondeur);

        //Mets  le repondeur à traiter dans la message queue
        if ($this->container->get('traite_repondeur')->process($repondeur)) {
            $this->entityManager->flush();
        }

        return $repondeur->getIdRepondeur();
    }

    /**
     * Modifie un répondeur en base
     * @param Repondeur $repondeur
     * @return integer id du répondeur créé
     */
    public function save_edited_repondeur(Repondeur $repondeur)
    {
        $this->entityManager->persist($repondeur);
        $this->entityManager->flush();
        //Mets  le repondeur à traiter dans la message queue
        if ($this->container->get('traite_repondeur')->process($repondeur)) {
        }

        return $repondeur->getIdRepondeur();
    }

    /**
     * Supprime un répondeur en base
     * @param Repondeur $repondeur
     * @return boolean
     */
    public function save_deleted_repondeur(Repondeur $repondeur)
    {
        $this->entityManager->persist($repondeur);
        $this->entityManager->remove($repondeur);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Renvoi vrai di un repondeur est actif
     * @param Repondeur $repondeur
     * @return boolean
     */
    public function isRepondeurActif(Repondeur $repondeur)
    {
        return ($this->entityManager
                                 ->getRepository('GestimeCoreBundle:AbonneRepondeur')
                                 ->hasRepondeur($repondeur) +
                $this->entityManager
                                 ->getRepository('GestimeCoreBundle:Fermeture')
                                 ->hasRepondeur($repondeur) > 0);
    }
}
