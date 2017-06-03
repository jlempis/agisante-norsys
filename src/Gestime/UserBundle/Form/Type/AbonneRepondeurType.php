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
use Gestime\CoreBundle\Entity\ParametreRepository;

/**
 * Formulaire de saisie des periodes non travaillées d'un abonné
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class AbonneRepondeurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('jour', 'entity', array(
                'class' => 'GestimeCoreBundle:Parametre',
                'property' => 'value',
                'query_builder' => function (ParametreRepository $cr) {
                    return $cr->getParamByType('Jour');
                },
                'attr' => array('class' => 'jour  sel2'),
                ))
            ->add('debut', 'timePicker')
            ->add('fin', 'timePicker')
            ->add('repondeur', 'entity', array(
                'class' => 'GestimeCoreBundle:Repondeur',
                'property' => 'tag',
                'attr' => array('class' => 'repondeur  sel2'),
                ))
            ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\AbonneRepondeur',
            'error_mapping' => array(
                'periodesValides' => 'debut',
                'finSupDebut' => 'fin',
             ),

        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_corebundle_abonne_repondeur';
    }
}
