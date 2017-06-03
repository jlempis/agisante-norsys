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
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\MedecinRepository;
use Gestime\CoreBundle\Entity\InfosDoc24;
use Gestime\CoreBundle\Entity\InfosDoc24Repository;
use Gestime\UserBundle\Form\Type\TarificationType;

/**
 * Formulaire de saisie des informations affichees sur Doc24
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class InfosDoc24Type extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('photo', 'file', array('required' => false))
            ->add('presentation', 'textarea', array('required' => true, 'attr' => array('cols' => '5', 'rows' => '4')))
            ->add('presentationLongue', 'textarea', array('required' => false, 'attr' => array('cols' => '5', 'rows' => '3')))
            ->add('tiers_payant', 'checkbox', array('required' => false))
            ->add('conventionnement')
            ->add('detailAcces')
            ->add('detailTelephone', 'textarea', array('required' => false, 'attr' => array('cols' => '5', 'rows' => '3')))
            ->add('infosPratiques')
            ->add('paiement_cb', 'checkbox', array('required' => false))
            ->add('paiement_ch', 'checkbox', array('required' => false))
            ->add('paiement_esp', 'checkbox', array('required' => false))
            ->add('carteVitale', 'checkbox', array('required' => false))
            ->add('site', 'text', array('required' => false))
            ->add('email', 'text', array('required' => false))
            ->add('photoPath','hidden', array('mapped' => false))
            ->add('id','hidden', array('mapped' => false))
            ->add('langues', 'entity', array(
                'empty_value'  => null,
                'multiple'     => true,
                'label'        => false,
                'expanded'     => false,
                'required'     => false,
                'class'        => 'Gestime\CoreBundle\Entity\Langue', ))
            ->add('specialitesMedicales', 'entity', array(
                'empty_value'  => null,
                'multiple'     => true,
                'label'        => false,
                'expanded'     => false,
                'required'     => false,
                'class'        => 'Gestime\CoreBundle\Entity\SpecialiteMedicale', ))
            ->add('tarification', 'collection', array(
                'type'         => new TarificationType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => false,
                'by_reference' => false,
                'prototype'   => true,
                'prototype_name' => '__tarif_prot__',
                'error_bubbling' => false,
                'cascade_validation' => true, ))
            ->add('transport', 'collection', array(
                'type'         => new TransportType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'label'        => false,
                'by_reference' => false,
                'prototype'   => true,
                'prototype_name' => '__transport_prot__',
                'error_bubbling' => false,
                'cascade_validation' => true, ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return none
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\InfosDoc24',
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

                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'infosDoc24';
    }
}
