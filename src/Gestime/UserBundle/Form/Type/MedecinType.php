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
use Symfony\Component\OptionsResolver\Options;
use Gestime\CoreBundle\Form\Type\TelephoneType;
use Gestime\CoreBundle\Entity\AbonneRepository;
use Gestime\CoreBundle\Entity\ParametreRepository;
use Gestime\CoreBundle\Entity\SpecialiteRepository;

/**
 * Formulaire de saisie d'un médecin
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class MedecinType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('nom', 'text', array(
                'attr' => array('autofocus' => 'autofocus'),
            ))
            ->add('prenom')
            ->add('remplacant')
            ->add('generaliste')
            ->add('rdvInternet')
            ->add('agenda')
            ->add('msgRappel', 'text', array('required' => false))
            ->add('dureeRdv')
            ->add('abonneSms')
            ->add('tempsAvantRappel', 'integer')
            ->add('specialites', 'entity', array(
                'class' => 'GestimeCoreBundle:Specialite',
                'property' => 'nom',
                'query_builder' => function (SpecialiteRepository $cr) {
                  return $cr->getUniqueSpecialite();
                },
                'empty_value'  => null,
                'multiple'     => true,
                'label'        => false,
                'expanded'     => false,
                'required'     => false,
                ))
            ->add('telephones', 'collection', array(
                    'type'         => new TelephoneType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'label'        => false,
                    'by_reference' => false,
                    'prototype'   => true,
                    'prototype_name' => '__tel_prot__',
                    'error_bubbling' => false,
                    'cascade_validation' => true, ))
            ->add('horaires', 'collection', array(
                    'type'         => new HoraireType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'label'        => false,
                    'by_reference' => false,
                    'prototype'   => true,
                    'prototype_name' => '__hor_prot__',
                    'error_bubbling' => false,
                    'cascade_validation' => true, ))
            ->add('abonnement', 'entity', array(
                    'class' => 'GestimeCoreBundle:Parametre',
                    'property' => 'value',
                    'query_builder' => function (ParametreRepository $cr) {
                        return $cr->getParamByType('TypeAboDoc24');
                    },
                    'attr' => array('class' => 'typerdv  sel2')))
            ->add('Annuler', 'reset');

        if ($options['attr']['action'] == 'suppr') {
            if ($options['attr']['interdit'] == false) {
                $builder->add('Enregistrer', 'submit', array('attr' => array('formnovalidate' => 'novalidate')));
            } else {
                $builder->add('Enregistrer', 'submit', array('disabled' => true));
            }
        } else {
            $builder->add('Enregistrer', 'submit');
        }

        $factory = $builder->getFormFactory();
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($factory) {
                $medecin = $event->getData();
                $medecinSite = $medecin->getSite();
                $form = $event->getForm();

                $formOptions = array(
                    'class' => 'GestimeCoreBundle:Abonne',
                    'multiple' => false,
                    'required' => false,
                    'expanded' => false,
                    'empty_value' => 'Le médecin n\'appartient pas à un abonné',
                    'property' => 'raisonSociale',
                    'query_builder' => function (AbonneRepository $cr) use ($medecinSite) {
                        return $cr->getAbonnes($medecinSite);
                    },
                    'auto_initialize' => false,
                );
                    $form->add($factory->createNamed('abonne', 'entity', null, $formOptions));
            }
        );
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
                if ($options['attr']['action'] == 'suppr') {
                    return false;
                } else {
                    return array('Default');
                }
            },
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'medecin';
    }
}
