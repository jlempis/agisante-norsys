<?php

namespace Gestime\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\ApiBundle\Form\DataTransformer\StringToDateTransformer;

class InfoUserWebType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $transformer = new StringToDateTransformer();

    $builder
            ->add('id')
            ->add('nom')
            ->add('prenom');


    $builder->add(
            $builder->create('naissance', 'text')
                    ->addModelTransformer($transformer));

    $builder->add('sexe');

  }

  /**
   * {@inheritdoc}
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class'        => 'Gestime\ApiBundle\Model\InfoUserWeb',
      'csrf_protection'   => false,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getName()
  {
    return 'infoUserWeb';
  }
}
