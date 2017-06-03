<?php
namespace Gestime\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Gestime\CoreBundle\Entity\Fermeture;

/**
 * Gestion des chevauchements de dates
 *
 */
class DateOverlapValidator
{
    protected $container;

    /**
     * [__construct description]
     * @param [type] $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * [handleEvent description]
     * @param  Fermeture $fermeture
     * @param  [type]    $unique
     * @return void
     */
    private function handleEvent(Fermeture $fermeture, $unique)
    {
        $fermeture->setUnique($unique);
    }

    /**
     * [prePersist description]
     * @param  LifecycleEventArgs $args
     * @return void
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Fermeture) {
            $entityManager = $args->getEntityManager();
        }
    }

    /**
     * [prePersist description]
     * @param  LifecycleEventArgs $args
     * @return void
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof Fermeture) {
            $entityManager = $args->getEntityManager();
            $repository = $entityManager->getRepository('GestimeCoreBundle:Fermeture');
            $listePeriodes = $repository->getAllPeriodesFermeture($entity->getIdFermeture());
            $utils = $this->container->get('gestime_core.utilities');

            foreach ($listePeriodes as $periode) {
                $tableauPeriodesAValider[] = array( 'start' => $periode['datedebut']->format('Y-m-d ').$periode['heuredebut'],
                                                    'end' => $periode['datefin']->format('Y-m-d ').$periode['heurefin'], );
            }
            $this->handleEvent($entity,
                !$utils->time_overlap(
                    $entity->getDatedebut()->format('Y-m-d ').$entity->getHeuredebut(),
                    $entity->getDatefin()->format('Y-m-d ').$entity->getHeurefin(),
                    $tableauPeriodesAValider
                )
            );
        }
    }
}
