<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Formulaire de saisie d'un message
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class MessageResponseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('sujet', 'text')
            ->add('objet', 'textarea', array(
                    'attr' => array('cols' => '40', 'rows' => '6',
                                    'autofocus' => 'autofocus', ), ));
        $builder
            ->add('Envoyer', 'submit')
            ->add('Annuler', 'reset');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Message',
            'error_mapping' => array(
                'destinataire'       => 'medecins',
                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_messagebundle_Message';
    }
}
