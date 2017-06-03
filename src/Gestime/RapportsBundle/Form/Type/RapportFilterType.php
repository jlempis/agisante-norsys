<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\RapportsBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Gestime\CoreBundle\Entity\MedecinRepository;

/**
 * Formulaire de  filtre d'un rapport (Filtre sur Médecin et date)
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RapportFilterType extends AbstractType
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
        $emptyOptions = array('empty_value' => 'Tous les médecins');

        if ($options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS')) {
            $medecinOptions = array_merge($medecinOptions, $emptyOptions);
        }
        $builder
            ->add('medecin', 'entity', $medecinOptions)
            ->add('debut', 'datePicker', array('data' => new \DateTime('now')))
            ->add('fin', 'datePicker', array('data' => new \DateTime('now')));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'rapportFilter';
    }
}
