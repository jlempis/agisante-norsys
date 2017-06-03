<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\TelephonieBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Formulaire de saisie d'une fermeture
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class FermetureType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('datedebut', 'datePicker', array(
                'attr' => array('autofocus' => 'autofocus'),
            ))
            ->add('heuredebut', 'timePicker')
            ->add('datefin', 'datePicker')
            ->add('heurefin', 'timePicker')
            ->add('repondeur', 'entity', array(
                'class' => 'GestimeCoreBundle:Repondeur',
                'property' => 'tag',
                'error_bubbling' => true,
                ))
            ->add('commentaire')
             ->add('Annuler', 'reset');;

        switch ($options['action']) {
            case 'suppr':
                $builder->add('Enregistrer', 'submit', array('attr' => array('formnovalidate' => 'novalidate')));
                break;
            default:
                $builder->add('Enregistrer', 'submit');
                break;
        }
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Fermeture',
            'error_mapping' => array(
                'finSupDebut'       => 'datefin',
                'supNow'            => 'datedebut',
                'periodesValides'   => 'datefin',
                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_telephoniebundle_Fermeture';
    }
}
