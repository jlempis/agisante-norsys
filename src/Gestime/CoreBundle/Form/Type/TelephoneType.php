<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\CoreBundle\Entity\Parametre;
use Gestime\CoreBundle\Entity\ParametreRepository;

/**
 * Formulaire de saisie d'es téléphone d'un médecin
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class TelephoneType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('numero', 'text')
            ->add('type', 'entity', array(
                'class' => 'GestimeCoreBundle:Parametre',
                'property' => 'value',
                'query_builder' => function (ParametreRepository $cr) {
                    return $cr->getParamByType('Telephone');
                },
                'attr' => array('class' => 'jour  sel2'),
                ))
            ->add('envoiSMS')
            ->add('token', 'text', array('required' => false));
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Telephone',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_form_telephone';
    }
}
