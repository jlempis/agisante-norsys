<?php

namespace Gestime\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\ApiBundle\Form\DataTransformer\StringToDateTimeTransformer;

class ContactWebType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $builder->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('telephone')
            ->add('message');
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class'        => 'Gestime\ApiBundle\Model\ContactWeb',
      'csrf_protection'   => false,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'contactWeb';
  }
}

