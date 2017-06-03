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
 * Formulaire de saisie de photos du cabinet médecin pour Doc24
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class PhotoCabinetType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   * @return Form
   */
  public function buildForm(FormBuilderInterface $builder,  array $options)
  {
    $builder
      ->add('photo', 'file', array('required' => false));
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Gestime\CoreBundle\Entity\PhotoCabinet',
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'photo_cabinet';
  }
}
