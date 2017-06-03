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
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Gestime\CoreBundle\Entity\Adresse;

/**
 * Formulaire de saisie d'une adresse
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class AdresseType extends AbstractType
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
        $factory = $builder->getFormFactory();

        $builder
            ->add('voie', 'text', array('required' => false))
            ->add('complement', 'text', array('required' => false))
            ->add('codePostal', 'text', array('required' => false));

        $refreshVille = function (Form $form, $villeId) use ($factory) {
             $formOptions = array(
                 'class'        => 'GestimeCoreBundle:Ville',
                 'attr'         => array('class' => 'sel2'),
                 'multiple'     => false,
                 'required'     => false,
                 'expanded'     => false,
                 'empty_value'  => 'Sélectionner la ville',
                 'choice_label' => 'nom',
                    'query_builder' => function (EntityRepository $repository) use ($villeId) {
                             return  $repository->getVillesById($villeId);
                    },
                 'auto_initialize' => false,
                 );
             $form->add($factory->createNamed('ville', 'entity', null, $formOptions));
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refreshVille) {
            $form = $event->getForm();
            $data = $event->getData();
            if ($data === null) {
                $refreshVille($form, null);
            }
            if ($data instanceof Adresse) {
                $refreshVille($form, $data->getVille()->getId());
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($refreshVille) {
            $form = $event->getForm();
            $data = $event->getData();
            if (array_key_exists('codePostal', $data)) {
                $event->setData($data);
                $refreshVille($form, $data['ville']);
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
            'data_class' => 'Gestime\CoreBundle\Entity\Adresse',
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();
                if ($data->getAdresseComplete() != '') {
                    return array('Default', 'obligatoire');
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
        return 'adresse';
    }
}
