<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\MessageBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Gestime\CoreBundle\Entity\MedecinRepository;

/**
 * Formulaire de saisie d'un message
 *
 * @category Formes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class MessageViewType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder,  array $options)
    {
        $builder
            ->add('sujet')
            ->add('objet', 'textarea', array(
                    'attr' => array('cols' => '40', 'rows' => '6'), ))
            ->add('sms', 'checkbox', array('required'  => false))
            ->add('categories', 'entity', array(
                'empty_value'  => null,
                'multiple'     => true,
                'label'        => false,
                'expanded'     => false,
                'required'     => false,
                'empty_value'  => 'Entrez une catégorie',
                'property'     => 'nom',
                'class'        => 'Gestime\CoreBundle\Entity\Categorie', ));

        //if ($options['attr']['sens'] == 'SITE-VERS-MED') {
            $builder
                ->add('medecins', 'entity', array(
                    'empty_value'  => null,
                    'multiple'     => true,
                    'label'        => false,
                    'expanded'     => false,
                    'required'     => false,
                    'class'        => 'Gestime\CoreBundle\Entity\Medecin', )
                );
        //}

        $builder
            ->add('Imprimer', 'button')
            ->add('Répondre', 'button')
            ->add('MarquerNonLu', 'button')
            ->add('MarquerFavori', 'button');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gestime\CoreBundle\Entity\Message',
            'error_mapping' => array(
                'destinataire'       => 'medecins',
                ),
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'gestime_messagebundle_Message';
    }
}
