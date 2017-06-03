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
 * Formulaire de saisie d'infosTransport pour Doc24
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class TransportType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   * @return Form
   */
  public function buildForm(FormBuilderInterface $builder,  array $options)
  {
    $builder
      ->add('type', 'entity', array(
        'class' => 'GestimeCoreBundle:Parametre',
        'property' => 'value',
        'query_builder' => function (ParametreRepository $cr) {
          return $cr->getParamByType('TypeTransport');
        },
        'attr' => array('class' => 'jour  sel2'),
      ))
      ->add('numeroLigne')
      ->add('arret');
  }

  /**
   * @param OptionsResolverInterface $resolver
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Gestime\CoreBundle\Entity\Transport',
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'transport';
  }
}
