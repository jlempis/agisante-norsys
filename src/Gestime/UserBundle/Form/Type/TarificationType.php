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
use Gestime\CoreBundle\Entity\Parametre;
use Gestime\CoreBundle\Entity\ParametreRepository;

/**
 * Formulaire de saisie de tarification
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class TarificationType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   * @return Form
   */
  public function buildForm(FormBuilderInterface $builder,  array $options)
  {
    $builder
      ->add('acte', 'text')
      ->add('tarifMini')
      ->add('tarifMaxi');
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Gestime\CoreBundle\Entity\Tarification',
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'tarification';
  }
}
