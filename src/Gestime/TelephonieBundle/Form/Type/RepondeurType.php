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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Formulaire de saisie d'un répondeur
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class RepondeurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder->add('tag', 'text', array(
                'attr' => array('autofocus' => 'autofocus'),
            ));

        if ($options['action'] != 'suppr' && $options['action'] != 'suppr_disabled') {
            $builder->add('file', 'file');
        }

        $builder->add('commentaire', 'textarea')
                ->add('Annuler', 'reset');

        switch ($options['action']) {
            case 'suppr':
                $builder->add('Enregistrer', 'submit', array('attr' => array('formnovalidate' => 'novalidate')));
                break;
            case 'suppr_disabled':
                $builder->add('Enregistrer', 'submit', array('attr' => array('disabled' => true)));
                break;
            default:
                $builder->add('Enregistrer', 'submit');
                break;
        }

        $factory = $builder->getFormFactory();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($factory) {
                $repondeur = $event->getData();

                if ($repondeur->getIdRepondeur() !== null) {
                    $form = $event->getForm();
                    $formOptions = array(
                        'label' => 'Nom du fchier',
                        'auto_initialize' => false,
                    );
                    $form->add($factory->createNamed('name', 'text', null, $formOptions));
                }
            }
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return boolean
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Repondeur',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_corebundle_Repondeur';
    }
}
