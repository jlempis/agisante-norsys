<?php

namespace Gestime\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\ApiBundle\Form\DataTransformer\StringToDateTimeTransformer;

class RdvWebType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {

    $transformer = new StringToDateTimeTransformer();

    $builder->add('email')
            ->add('telephone')
            ->add('medecinId')
            ->add('specialiteId');

    $builder->add(
      $builder->create('dateRdv', 'text')
        ->addModelTransformer($transformer));

    $builder->add('codeActivation')
            ->add('dejaVenu')
            ->add('raison')
            ->add('sexe')
            ->add('naissance');
  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class'        => 'Gestime\ApiBundle\Model\RdvWeb',
      'csrf_protection'   => false,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'rdvWeb';
  }
}

