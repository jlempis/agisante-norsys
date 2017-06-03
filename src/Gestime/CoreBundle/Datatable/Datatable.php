<?php

namespace Gestime\CoreBundle\Datatable;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Ali\DatatableBundle\Util\Datatable as BaseDatatable;
use Doctrine\ORM\EntityManager;

/**
 * Datatable
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class Datatable extends BaseDatatable
{
    /**
     * class constructor
     *
     * @param ContainerInterface $container
     */
    protected $_container;
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->_container = $container;
        $this->_em = $this->_container->get('doctrine.orm.entity_manager');
        //$this->_queryBuilder = new DoctrineBuilder($container);
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->_queryBuilder = new DoctrineBuilder($this->_container, $this->_em);
        return $this;
    }
}
