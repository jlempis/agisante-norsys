<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\AgendaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\Options;
use Gestime\CoreBundle\Entity\Medecin;
use Gestime\CoreBundle\Entity\Personne;
use Gestime\CoreBundle\Entity\MedecinRepository;
use Gestime\CoreBundle\Entity\PersonneRepository;

/**
 * Formulaire de saisie d'une consigne
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class ConsigneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('description', 'text', array(
                'attr' => array('autofocus' => 'autofocus'),
            ))
            ->add('debut', 'datePicker')
            ->add('fin', 'datePicker')
            ->add('visible')
            ->add('bloquante')
            ->add('medecin', 'entity', array(
                    'class' => 'GestimeCoreBundle:Medecin',
                    'multiple' => false,
                    'expanded' => false,
                    'property' => 'nomPrenom',
                    'query_builder' => function (MedecinRepository $cr) use ($options) {
                        return $cr->getMedecinsUser(
                            $options['attr']['user']->hasRole('ROLE_VISU_AGENDA_TOUS'),
                            $options['attr']['user']->getSite(),
                            $options['attr']['user']->getId()
                        );
                    },
                    'auto_initialize' => false, ))

            ->add('patient_form_nom', 'genemu_jqueryautocomplete_text', array(
                'route_name' => 'ajax_nom_patient',
            ))
            ->add('patient', 'entity', array(
                    'class' => 'GestimeCoreBundle:Personne',
                    'multiple' => false,
                    'expanded' => false,
                    'property' => 'nom',
                    'query_builder' => function (PersonneRepository $cr) {
                        return $cr->findPatientById(0);
                    },
                    'auto_initialize' => false, ))
            ->add('patient_form_id', 'hidden')
            ->add('Annuler', 'reset');

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $consigne = $event->getData();
                if ($consigne->getIdConsigne() !== null) {
                    $consigne->setPatientFormId($consigne->getPatient()->getId());
                    $consigne->setPatientFormNom($consigne->getPatient()->getNom());
                    $event->setData($consigne);
                }
            }
        );

        if ($options['action'] == 'suppr') {
            $builder->add('Enregistrer', 'submit', array('attr' => array('formnovalidate' => 'novalidate')));
        } else {
            $builder->add('Enregistrer', 'submit');
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return none
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Consigne',
            'validation_groups' => function (Options $options) {
                if ($options['action'] == 'suppr') {
                    return false;
                } else {
                    return 'Default';
                }
            },
            'error_mapping' => array(
                'finSupDebut'       => 'fin',
                'supNow'            => 'debut',
                'valide'            => 'patient_form_nom',
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
