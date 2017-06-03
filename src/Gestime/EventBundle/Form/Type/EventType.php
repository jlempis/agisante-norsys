<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\MedecinRepository;
use Gestime\CoreBundle\Entity\ParametreRepository;
use Ivory\GoogleMapBundle\Form\Type\PlaceAutocompleteType;

/**
 * Formulaire de saisie d'un rendez-vous
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class EventType extends AbstractType
{
    protected $entityManager;

    /**
     * [__construct description]
     * @param [type] $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('idEvenement', 'hidden')
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
            ->add('debutRdv', 'datePicker')
            ->add('heureDebut', 'timePicker')
            ->add('heureFin', 'timePicker')
            ->add('type', 'entity', array(
                'class' => 'GestimeCoreBundle:Parametre',
                'choice_label' => 'value',
                'query_builder' => function (ParametreRepository $cr) {
                    return $cr->getParamByType('TypeRdv');
                },
                'attr' => array('class' => 'typerdv  sel2'),
                ))

            ->add('objet', 'textarea')
            ->add('patient', new PersonneType($this->entityManager),
                array('error_bubbling' => true)
            )

            ->add('nonExcuse')
            ->add('nouveauPatient')
            ->add('rappel')
            ->add('nonExcuse');

    }

    /**
     * @param OptionsResolver $resolver
     * @return none
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Evenement',
            'validation_groups' => function (FormInterface $form) {

                if ($form->get('type')->getData()->getCode() == 'P') {
                    return 'TempsReserve';
                }

                if (!$form->get('medecin')->getData()->isGeneraliste()) {
                    return array('Evenement', 'Specialiste', 'Personne');
                }

                return array('Evenement', 'Personne');
            },
            'error_mapping' => array(
                'dateRDVValide'     => 'debutRdv',
                'heureDebutValide'  => 'heureDebut',
                'heureFinValide'    => 'heureFin',
                'heureCoherente'    => 'heureFin',
                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'evenement';
    }
}
