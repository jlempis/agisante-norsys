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
use Symfony\Component\OptionsResolver\Options;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\MedecinRepository;

/**
 * Formulaire de saisie du medecin et de la date dans l'agenda
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AgendaListType extends AbstractType
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
                    'class'         => 'GestimeCoreBundle:Medecin',
                    'multiple'      => false,
                    'expanded'      => false,
                    'choice_label'  => 'nomPrenom',
                    'query_builder' => function (MedecinRepository $cr) use ($options) {
                        return $cr->getMedecinsUser(
                            $options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS'),
                            $options['attr']['user']->getSite(),
                            $options['attr']['user']->getId()
                        );
                    },
                    'auto_initialize' => false, ))
            ->add('dateAgenda', 'datePicker');
    }

    /**
     * @param OptionsResolver $resolver
     * @return none
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Evenement',
            'validation_groups' => function (Options $options) {
                if ($options['action'] == 'suppr') {
                    return false;
                } else {
                    return 'Default';
                }
            },
            )
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'agenda';
    }
}
