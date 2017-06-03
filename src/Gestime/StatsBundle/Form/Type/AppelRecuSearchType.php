<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\StatsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Formulaire de recherche d'un appel reçu
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class AppelRecuSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('datedebut', 'datePicker', array('data' => new \DateTime('now')))
            ->add('datefin', 'datePicker', array('data' => new \DateTime('now')));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'AppelRecuSearch';
    }
}
