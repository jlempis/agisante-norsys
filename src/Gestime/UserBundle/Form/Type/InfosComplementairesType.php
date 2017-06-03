<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Formulaire des informations complémentaires d'un médecin
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class InfosComplementairesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('infosDoc24',  new  InfosDoc24Type())
            ->add('carence')
            ->add('horairesInternet', 'collection', array(
                'type'         => new HoraireInternetType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => false,
                'by_reference' => false,
                'prototype'   => true,
                'prototype_name' => '__horin_prot__',
                'error_bubbling' => false,
                'cascade_validation' => true, ))
            ->add('Enregistrer', 'submit')
            ->add('Annuler', 'reset');
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Medecin',
            'cascade_validation' => true,
            'validation_groups' => function (Options $options) {
              return array('Default');

            },
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'infosComplementaires';
    }
}
