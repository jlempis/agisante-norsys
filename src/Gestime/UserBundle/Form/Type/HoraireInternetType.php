<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use Gestime\CoreBundle\Entity\ParametreRepository;

/**
 * Formulaire de gestion des Horaires spécifiques
 * Les plages renseignées par ce formulaire sont utilisées dans l'agenda'
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class HoraireInternetType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   * @return Form
   */
  public function buildForm(FormBuilderInterface $builder,  array $options)
  {
    $builder
      ->add('jour', 'entity', array(
        'class' => 'GestimeCoreBundle:Parametre',
        'property' => 'value',
        'query_builder' => function (ParametreRepository $cr) {
          return $cr->getParamByType('Jour');
        },
        'attr' => array('class' => 'jour  sel2'),
      ))
      ->add('debut', 'timePicker')
      ->add('fin', 'timePicker')
      ->add('nbRdvMax', 'text');
  }

  /**
   * @param OptionsResolverInterface $resolver
   * @return none
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Gestime\CoreBundle\Entity\HoraireInternet',
      'error_mapping' => array(
        'finSupDebut' => 'fin',
      ),

    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'horaireInternet';
  }
}
