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
use Symfony\Component\OptionsResolver\Options;

/**
 * Formulaire de saisie d'une ligne
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class LigneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('numero', 'text', array(
                'attr' => array('autofocus' => 'autofocus'),
            ))
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
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return boolean
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Ligne',
            'validation_groups' => function (Options $options) {
                if ($options['action'] == 'suppr') {
                    return false;
                } else {
                    return 'Default';
                }
            },
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_corebundle_Ligne';
    }
}
