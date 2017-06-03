<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\EventBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gestime\CoreBundle\Entity\MedecinRepository;

/**
 * Formulaire d'un remplacement pendant  absence d'un médecin
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class RemplacementType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @return Form
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('debut', 'datePicker')
            ->add('fin', 'datePicker')
            ->add('medecinRemplacant', 'entity', array(
                'class' => 'GestimeCoreBundle:Medecin',
                'choice_label' => 'nomPrenom',
                    'query_builder' => function (MedecinRepository $cr) use ($options) {
                        return $cr->getListMedecinsRemplacants(
                            $options['attr']['user']->getSite()
                        );
                    },
                    'attr' => array('class' => 'remplacant sel2'),
                    )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Remplacement',
            'error_mapping' => array(
                 'finSupDebut' => 'fin',
                 'debutSupDebAbsence' => 'fin',
                 'finSupFinAbsence' => 'fin',
                 'periodesValides' => 'fin',
             ),

        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'remplacement';
    }
}
