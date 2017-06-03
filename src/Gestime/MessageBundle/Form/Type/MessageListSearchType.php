<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
        $builder
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
