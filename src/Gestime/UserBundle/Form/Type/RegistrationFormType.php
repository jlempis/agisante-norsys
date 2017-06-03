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

/**
 * Enregistrement d'un utilisateur
 *
 */
class RegistrationFormType extends AbstractType
{
    /**
     * buildForm
     * @param  FormBuilderInterface $builder
     * @param  array                $options
     * @return [type]
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //type : B : BackOffice, W:Web
        $builder->add('userWeb');

        //Obligatoire pour le User Web
        $builder->add('phoneNumber', 'text', array('required' => false));

    }

    /**
     * @param OptionsResolverInterface $resolver
     * @return void
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Utilisateur',
            'validation_groups' => 'Web'
        ));
    }

    /**
     * [getParent description]
     * @return string
     */
    public function getParent()
    {
        return 'fos_user_registration';
    }

    /**
     * [getName description]
     * @return string
     */
    public function getName()
    {
        return 'gestime_user_registration';
    }
}