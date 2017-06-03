<?php

namespace Gestime\CoreBundle\Datatable;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Ali\DatatableBundle\Util\Factory\Query\DoctrineBuilder as BaseDoctrine;
use Ali\DatatableBundle\Util\Factory\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * DoctrineBuilder
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class DoctrineBuilder extends BaseDoctrine implements QueryInterface {
    /**
     * class constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, $em) {
        parent::__construct($container, $em);
    }

    /**
     * get data
     *
     * @param int $hydration_mode
     *
     * @return array
     */
    public function getData($hydration_mode)
    {
        $request    = $this->request;
        $dql_fields = array_values($this->fields);

        // add sorting
        if ($request->get('iSortCol_0') != null)
        {
            $order_field = current(explode(' as ', $dql_fields[$request->get('iSortCol_0')]));
        }
        else
        {
            $order_field = null;
        }
        $qb = clone $this->queryBuilder;
        if (!is_null($order_field))
        {
            $qb->orderBy($order_field, $request->get('sSortDir_0', 'asc'));
        }
        else
        {
            ///JLE $qb->resetDQLPart('orderBy');
        }

        // extract alias selectors
        $select = array($this->entity_alias);
        foreach ($this->joins as $join)
        {
            $select[] = $join[1];
        }
        $qb->select(implode(',', $select));

        // add search
        $this->_addSearch($qb);

        // get results and process data formatting
        $query          = $qb->getQuery();
        $iDisplayLength = (int) $request->get('iDisplayLength');
        if ($iDisplayLength > 0)
        {
            $query->setMaxResults($iDisplayLength)->setFirstResult($request->get('iDisplayStart'));
        }
        $objects        = $query->getResult(Query::HYDRATE_OBJECT);
        $maps           = $query->getResult(Query::HYDRATE_SCALAR);
        $data           = array();

        $get_scalar_key = function($field) {
            $has_alias = preg_match_all('~([A-z]?\.[A-z]+)?\sas~', $field, $matches);
            if (preg_match('~GroupConcat~', $field)) {
                return 'gname';
            }
            if (preg_match('~Concat~', $field)) {
                $pieces = explode(' ', $field);
                $alias = array_pop($pieces);
                return $alias;
            }
            $_f        = ( $has_alias > 0 ) ? $matches[1][0] : $field;
            $_f        = str_replace('.', '_', $_f);

            return $_f;
        };


        $fields = array();
        foreach ($this->fields as $field)
        {
            $fields[] = $get_scalar_key($field);
        }
        foreach ($maps as $map)
        {
            $item = array();
            foreach ($fields as $_field)
            {
                $item[] = $map[$_field];
            }
            $data[] = $item;
        }
        return array($data, $objects);
    }

}
