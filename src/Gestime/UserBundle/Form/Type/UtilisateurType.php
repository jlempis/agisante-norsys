<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Formulaire de saisie d'un utilisateur
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class UtilisateurType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text', array(
                'attr' => array('autofocus' => 'autofocus'),
            ))
            ->add('prenom')
            ->add('username');

        if ($options['attr']['action'] == 'ajouter') {
            $builder
            ->add('password');
        }

        $builder
            ->add('email')
            ->add('dateNaissance','datePicker')
            ->add('sexe', 'choice', array(
                'expanded' => false,
                'choices'   => array('m' => 'Homme', 'f' => 'Femme')
              ))
            ->add('groups', 'entity', array(
                'empty_value'  => null,
                'multiple'     => true,
                'label'        => false,
                'expanded'     => false,
                'required'     => false,
                'class'        => 'Gestime\CoreBundle\Entity\Group', ))
            ->add('medecins', 'entity', array(
                'empty_value'  => null,
                'multiple'     => true,
                'label'        => false,
                'expanded'     => false,
                'required'     => false,
                'class'        => 'Gestime\CoreBundle\Entity\Medecin', ))
            ->add('medecindefault', 'entity', array(
                'empty_value'  => null,
                'multiple'     => false,
                'label'        => false,
                'expanded'     => false,
                'required'     => false,
                'class'        => 'Gestime\CoreBundle\Entity\Medecin', ))
            ->add('locked', 'checkbox', array('required' => false))
            ->add('phoneNumber','text', array('required' => false))
            ->add('emailAuthCode','text', array('required' => false))
            ->add('userWeb','checkbox', array('required' => false))


          ->add('Enregistrer', 'submit')
            ->add('Annuler', 'reset');
    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Utilisateur',
            'validation_groups' => function (Options $options) {
                if ($options['attr']['action'] == 'suppr') {
                    return false;
                } else {
                    return array('Default', 'Utilisateur');
                }
            },
            'error_mapping' => array(
                'medecinDefaultOK'  => 'medecindefault',
                'rolesMessagerieOK' => 'groups',
                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'utilisateur';
    }
}
