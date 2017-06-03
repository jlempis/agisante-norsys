<?php

namespace Gestime\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\ApiBundle\Form\DataTransformer\StringToDateTransformer;

class ResetPasswordType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $builder->add('email');
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class'        => 'Gestime\ApiBundle\ModelResetPassword',
      'csrf_protection'   => false,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'resetPassword';
  }
}
