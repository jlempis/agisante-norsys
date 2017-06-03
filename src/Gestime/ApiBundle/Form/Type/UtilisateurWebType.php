<?php

namespace Gestime\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\ApiBundle\Form\DataTransformer\StringToDateTransformer;

class UtilisateurWebType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $transformer = new StringToDateTransformer();

    $builder->add('nom')
            ->add('prenom')
            ->add('sexe');

    $builder->add(
            $builder->create('naissance', 'text')
                    ->addModelTransformer($transformer));

    $builder->add('email', 'email')
            ->add('password', 'password')
            ->add('nbRdv')
            ->add('notif')
            ->add('etat');
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class'        => 'Gestime\ApiBundle\Model\UtilisateurWeb',
      'csrf_protection'   => false,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'utilisateurWeb';
  }
}
