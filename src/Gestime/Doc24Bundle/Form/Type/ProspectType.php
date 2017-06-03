<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\Doc24Bundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\CoreBundle\Entity\SpecialiteRepository;

/**
 * Formulaire de saisie d'une fermeture
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class ProspectType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder,  array $options)
  {
    $builder
      ->add('nom', 'text', array(
        'attr' => array('autofocus' => 'autofocus'),
      ))
      ->add('specialites', 'entity', array(
        'class' => 'GestimeCoreBundle:Specialite',
        'property' => 'nom',
        'query_builder' => function (SpecialiteRepository $cr) {
          return $cr->getUniqueSpecialite();
        },
        'empty_value'  => null,
        'multiple'     => true,
        'label'        => false,
        'expanded'     => false,
        'required'     => false,
      ))
      ->add('prenom')
      ->add('adresse1', 'text', array('required' => false))
      ->add('adresse2', 'text',array('required' => false))
      ->add('adresse3')
      ->add('adresse4', 'text',array('required' => false))
      ->add('codePostal')
      ->add('ville')
      ->add('telephone', 'text',array('required' => false))
      ->add('fax', 'text',array('required' => false))
      ->add('email', 'text',array('required' => false))
      ->add('latitude', 'number',array('required' => false))
      ->add('longitude', 'number',array('required' => false))
      ->add('Annuler', 'reset');;

    switch ($options['action']) {
      case 'suppr':
        $builder->add('Enregistrer', 'submit', array('attr' => array('formnovalidate' => 'novalidate')));
        break;
      default:
        $builder->add('Enregistrer', 'submit');
        break;
    }
  }

  /**
   * @param OptionsResolverInterface $resolver
   * @return void
   */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'data_class' => 'Gestime\CoreBundle\Entity\Prospect',
    ));
  }

  /**
   * @return string
   */
  public function getName()
  {
    return 'prospect';
  }
}
