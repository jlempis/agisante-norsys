<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Gestime\CoreBundle\Entity\MedecinRepository;

/**
 * Formulaire de recherche d'un rendez vous
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RechercheType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $medecinOptions = array(
                        'class' => 'GestimeCoreBundle:Medecin',
                        'multiple' => false,
                        'expanded' => false,
                        'choice_label' => 'nomPrenom',
                        'auto_initialize' => false,
                        'query_builder' => function (MedecinRepository $cr) use ($options) {
                            return $cr->getMedecinsUser(
                                $options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS'),
                                $options['attr']['user']->getSite(),
                                $options['attr']['user']->getId()
                            );
                        },
        );
        $emptyOptions = array('empty_value' => 'Tous les médecins');

        if ($options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS')) {
            $medecinOptions = array_merge($medecinOptions, $emptyOptions);
        }

        $builder
            ->add('medecin', 'entity', $medecinOptions)
            ->add('nom', 'text')
            ->add('prenom', 'text')
            ->add('telephone', 'text');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'recherche';
    }
}
