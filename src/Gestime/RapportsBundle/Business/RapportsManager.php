<?php

namespace Gestime\RapportsBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * RapportsManager
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RapportsManager
{
    protected $entityManager;
    protected $container;

    /**
     * [getFin description]
     * @param datetime $fin
     * @return datetime
     */
    private function getFin($fin)
    {
        return (is_object($fin)) ? $fin->add(new \DateInterval('P1D')) : $fin .= ' 23:59';
    }

    /**
     * [__construct description]
     * @param ContainerInterface $container     [description]
     * @param EntityManager      $entityManager [description]
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    /**
     * [getMedecinbyId description]
     * @param integer $medecinId
     * @return querybuilder
     */
    public function getMedecinbyId($medecinId)
    {
        return $this->entityManager
                    ->getRepository('GestimeCoreBundle:Medecin')
                    ->find($medecinId);
    }

    /**
     * Retourne la liste des patients non excusés d'un médecin
     * @param integer  $medecinId
     * @param datetime $debut
     * @param datetime $fin
     * @return querybuilder
     */
    public function getListeNonExcuses($medecinId, $debut, $fin)
    {
        return $this->entityManager
                    ->getRepository('GestimeCoreBundle:Evenement')
                    ->ListeNonExcuses($medecinId, $debut, $this->getFin($fin));
    }

    /**
     * Retourne la liste des messages d'un médecin
     * @param integer  $medecinId
     * @param datetime $debut
     * @param datetime $fin
     * @return querybuilder
     */
    public function getListeMessages($medecinId, $debut, $fin)
    {
        return $this->entityManager
                    ->getRepository('GestimeCoreBundle:Message')
                    ->getListeMessages($medecinId, $debut, $this->getFin($fin));
    }

    /**
     * Retourne la liste des sms d'un médecin
     * @param integer  $medecinId
     * @param datetime $debut
     * @param datetime $fin
     * @return querybuilder
     */
    public function getListeSms($medecinId, $debut, $fin)
    {
        return $this->entityManager
                    ->getRepository('GestimeCoreBundle:LogMessage')
                    ->getListSMS($medecinId, $debut, $this->getFin($fin));
    }

    /**
     * Retourne la liste des mouvements d'un médecin
     * @param integer  $medecinId
     * @param datetime $debut
     * @param datetime $fin
     * @return querybuilder
     */
    public function getListeMouvements($medecinId, $debut, $fin)
    {
        return $this->entityManager
                    ->getRepository('GestimeCoreBundle:RelationEvenement')
                    ->ListeMouvements($medecinId, $debut, $this->getFin($fin));
    }

    /**
     * Retourne la liste des rendez-vous d'un médecin
     * @param integer  $medecinId
     * @param datetime $debut
     * @param datetime $fin
     * @return querybuilder
     */
    public function getListeRendezVous($medecinId, $debut, $fin)
    {
        return $this->entityManager
                    ->getRepository('GestimeCoreBundle:Evenement')
                    ->ListeRendezVous($medecinId, $debut, $this->getFin($fin));
    }
}
