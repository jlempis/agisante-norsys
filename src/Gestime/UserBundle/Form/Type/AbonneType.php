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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Gestime\CoreBundle\Entity\LigneRepository;



/**
 * Formulaire de saisie d'un abonné
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class AbonneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return FormBuilder
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('raisonSociale', 'text', array(
                'attr' => array('autofocus' => 'autofocus'),
            ))
            ->add('voie', 'text')
            ->add('ville', 'text')
            ->add('codePostal', 'text')
            ->add('debutValidite', 'datePicker')
            ->add('finValidite', 'datePicker');

        $factory = $builder->getFormFactory();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($factory) {
                $abonne = $event->getData();
                $abonneSite = $abonne->getSite();

                if ($abonne->getIdAbonne() !== null) {
                    $numeroLigneActuelle = $abonne->getLigne()->getNumero();
                } else {
                    $numeroLigneActuelle  = null;
                }

                $form = $event->getForm();
                $formOptions = array(
                    'class' => 'GestimeCoreBundle:Ligne',
                    'multiple' => false,
                    'expanded' => false,
                    'property' => 'numero',
                    'query_builder' => function (LigneRepository $cr) use ($numeroLigneActuelle, $abonneSite) {
                        return $cr->getLignesDisponibles($abonneSite, $numeroLigneActuelle);
                    },
                    'auto_initialize' => false,
                );
                $form->add($factory->createNamed('Ligne', 'entity', null, $formOptions));
            }
        );

        $builder->add('periodes', 'collection', array(
                        'type'         => new AbonneRepondeurType(),
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'label'        => false,
                        'by_reference' => false,
                        'prototype'   => true,
                        'error_bubbling' => false,
                        'cascade_validation' => true, )
        );

        $builder->addEventListener(FormEvents::BIND,
            function (FormEvent $event) {
                $abonne = $event->getData();
                $periodes = $abonne->getPeriodes();
                $indexPeriode = 0;
                foreach ($periodes as &$periode) {
                    $periode->setIndexPeriode($indexPeriode++);
                    $periode->setAbonne($abonne);
                }
            });
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function getDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Abonne',
            'cascade_validation' => true,
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_corebundle_abonne';
    }
}
