<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Gestime\CoreBundle\Entity\MedecinRepository;

/**
 * Formulaire de recherche d'un message
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class MessageListSearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {

        $medecinOptions = array(
            'class' => 'GestimeCoreBundle:Medecin',
            'multiple' => false,
            'expanded' => false,
            'property' => 'nomPrenom',
            'auto_initialize' => false,
            'query_builder' => function (MedecinRepository $cr) use ($options) {
                return $cr->getMedecinsUser(
                    $options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS'),
                    $options['attr']['user']->getSite(),
                    $options['attr']['user']->getId()
                );
            },
        );

        $emptyOptions = array('required'    => false,
                              'empty_value' => 'Tous les médecins',
                               );/*'mapped' => false*/

        $defaultMedecin = $options['attr']['user']->getMedecindefault()->getIdMedecin();

        if ($options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS')) {
            $medecinOptions = array_merge($medecinOptions, $emptyOptions, array('data' => 0));
        } else {
            $medecinOptions = array_merge($medecinOptions, array('data' => $defaultMedecin ));
        }
        $builder
            ->add('medecin', 'entity', $medecinOptions)
            ->add('texte', 'text', array('required'  => false))
            ->add('action', 'choice', array(
                    'choices'   => array('t' => 'Tous les messages', 'l' => 'Lus',  'n' => 'Non lus'),
                    'required'  => false,
            ))
            ->add('Rechercher', 'submit');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'MessageListSearch';
    }
}
