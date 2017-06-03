<?php

namespace Gestime\CoreBundle\QueueManager;

use Gestime\CoreBundle\Entity\Repondeur;

/**
 * Classe de tests des IHMs absence
 *
 */
class TraiteRepondeur
{
    private $producer;

    /**
     * [__construct description]
     * @param [type] $producer [description]
     */
    public function __construct($producer)
    {
        $this->producer = $producer;
    }

    /**
     * [process description]
     * @param  Repondeur $repondeur
     * @return boolean
     */
    public function process(Repondeur $repondeur)
    {
        $this->producer->publish(serialize($repondeur->getTag()));

        return true;
    }
}
