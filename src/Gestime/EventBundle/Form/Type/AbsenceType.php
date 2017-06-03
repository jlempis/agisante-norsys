<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\Options;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\MedecinRepository;

/**
 * Formulaire de saisie d'une absence
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AbsenceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('medecin', 'entity', array(
                    'class' => 'GestimeCoreBundle:Medecin',
                    'multiple' => false,
                    'expanded' => false,
                    'choice_label' => 'nomPrenom',
                    'query_builder' => function (MedecinRepository $cr) use ($options) {
                        return $cr->getMedecinsUser(
                            $options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS'),
                            $options['attr']['user']->getSite(),
                            $options['attr']['user']->getId()
                        );
                    },
                    'auto_initialize' => false, ))
            ->add('debut', 'datePicker')
            ->add('fin', 'datePicker')
            ->add('commentaire', 'text')
            ->add('remplacements', 'collection', array(
                        'type'          => new RemplacementType(),
                        'options'       => array('attr' => $options['attr']),
                        'allow_add'     => true,
                        'allow_delete'  => true,
                        'label'         => false,
                        'by_reference'  => false,
                        'prototype'     => true,
                        'error_bubbling' => false,
                        'cascade_validation' => true, ))
            ->add('Annuler', 'reset');

        if ($options['action'] == 'suppr') {
            $builder->add('Enregistrer', 'submit', array('attr' => array('formnovalidate' => 'novalidate')));
        } else {
            $builder->add('Enregistrer', 'submit');
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $absence = $event->getData();
            $periodes = $absence->getRemplacements();
            $indexPeriode = 0;
            foreach ($periodes as &$periode) {
                    $periode->setIndexPeriode($indexPeriode++);
                    $periode->setAbsence($absence);
            }
        });
    }

    /**
     * @param OptionsResolver $resolver
     * @return none
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Absence',
            'cascade_validation' => true,
            'validation_groups' => function (Options $options) {
                if ($options['action'] == 'suppr') {
                    return false;
                } else {
                    return 'Default';
                }
            },
            'error_mapping' => array(
                'finSupDebut'       => 'fin',
                'supNow'            => 'debut',
                'periodesValides'   => 'fin',
                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'absence';
    }
}
