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
use Gestime\CoreBundle\Entity\PersonneRepository;

use Ivory\GoogleMap\Places\AutocompleteComponentRestriction;
use Ivory\GoogleMap\Helper\Builder\PlaceAutocompleteHelperBuilder;

use Ivory\GoogleMap\Base\Bound;
use Ivory\GoogleMap\Base\Coordinate;


/**
 * Formulaire de saisie d'une personne (Patient)
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class PersonneType extends AbstractType
{
    protected $entityManager;
    protected $region;

    /**
     * [__construct description]
     * @param [type] $entityManager
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->region = new Bound(
            new Coordinate(51.08, 1.5936),
            new Coordinate(49.82, 4.34)
        );

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {


        $builder
            ->add('civilite', 'choice', array(
                'choices' => array(1 => 'Mr', 2 => 'Mme', 3 => 'Melle'),
                'preferred_choices' => array('Mr'),
            ))
            ->add('entreprise', 'genemu_jqueryautocomplete_text', array(
                'route_name' => 'ajax_nom_entreprise',
                'required' => false,
            ))
            ->add('_entreprise', 'entity', array(
                    'required' => false,
                    'class' => 'GestimeCoreBundle:Personne',
                    'multiple' => false,
                    'expanded' => false,
                    'choice_label' => 'nom',
                        'query_builder' => function (PersonneRepository $cr) {
                            return $cr->findPatientById(0);
                        },
                    'auto_initialize' => false, ))
            ->add('entreprise_id', 'hidden')


            ->add('prenom', 'text')
            ->add('nomJF', 'text', array('required' => false))
            ->add('telephone', 'text')
            ->add('nom', 'genemu_jqueryautocomplete_text', array(
                'route_name' => 'ajax_nom_patient',
            ))

            ->add('nom_entity', 'entity', array(
                    'class' => 'GestimeCoreBundle:Personne',
                    'multiple' => false,
                    'expanded' => false,
                    'choice_label' => 'nom',
                        'query_builder' => function (PersonneRepository $cr) {
                            return $cr->findPatientById(0);
                        },
                    'auto_initialize' => false, ))
            ->add('nom_id', 'hidden')


            ->add('adresse', 'places_autocomplete', array(
                'prefix' => 'js_prefix_',
                 'bound' => $this->region,
                 'attr' => array(
                     'placeholder' => 'Adresse'
                 ),
                'component_restrictions' => array(
                    AutocompleteComponentRestriction::COUNTRY => 'fr',
                ),
                'language' => 'fr',
            ));


    }

    /**
     * @param OptionsResolver $resolver
     * @return none
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Personne',
            'validation_groups' => function (FormInterface $form) {
                $type = $form->getParent()->getData()->getType();
                if ($type == 'P') {
                    return false;
                } else {
                    return 'Default';
                }
            },
            'error_mapping' => array(

                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'personne';
    }
}
