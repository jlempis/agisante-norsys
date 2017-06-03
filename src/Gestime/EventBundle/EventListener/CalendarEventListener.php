<?php

/**
 * CalendarEventListener class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\EventBundle\EventListener;

use ADesigns\CalendarBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;

/**
 * Appel des RDV à afficher dans l'agenda
 *
 */
class CalendarEventListener
{
    private $entityManager;
    private $utilities;
    private $router;

    /**
     * [__construct description]
     * @param EntityManager $entityManager
     * @param Router        $router
     * @param Utilities     $utilities
     *
     */
    public function __construct(EntityManager $entityManager, $router, $utilities)
    {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->utilities = $utilities;
    }

    /**
     * [loadEvents description]
     * @param  CalendarEvent $calendarEvent
     * @return void
     */
    public function loadEvents(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();
        $request = $calendarEvent->getRequest();

        $medecin = $request->get('medecin');
        $debut = $startDate->format('Y-m-d H:i:s');
        $fin = $endDate->format('Y-m-d H:i:s');

        $evenements = $this->entityManager->getRepository('GestimeCoreBundle:Evenement')
                            ->getRendezVous($debut, $fin, $medecin);

        foreach ($evenements as $evenement) {
            if ($evenement['type'] != 'P') {
                $civilite =  $this->utilities->civilite($evenement['civilite']);
                $eventEntity = new EventEntity($civilite.' '.$evenement['nom'].' '.$evenement['prenom'], $evenement['debutRdv'], $evenement['finRdv']);
            } else {
                $eventEntity = new EventEntity($evenement['objet'], $evenement['debutRdv'], $evenement['finRdv']);
            }
            $eventEntity->addField('nom', $evenement['nom']);
            $eventEntity->addField('prenom', $evenement['prenom']);
            $eventEntity->addField('id', $evenement['idEvenement']);
            $eventEntity->addField('nouveauPatient', $evenement['nouveauPatient']);
            $eventEntity->addField('telephone', $evenement['telephone']);
            $eventEntity->addField('entreprise', $evenement['entreprise']);
            $eventEntity->addField('type', $evenement['type']);
            $eventEntity->addField('idType', $evenement['idParametre']);
            $eventEntity->addField('objet', $evenement['objet'].'&nbsp;');
            $eventEntity->addField('qtip', true);
            $eventEntity->addField('couleur', $evenement['argument']);
            $eventEntity->setCssClass('fc-event-skin');

            $calendarEvent->addEvent($eventEntity);
        }
    }
}
