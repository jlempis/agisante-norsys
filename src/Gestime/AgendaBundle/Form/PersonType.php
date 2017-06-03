<?php

namespace Gestime\AgendaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * PersonType
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class PersonType extends AbstractType
{
    /**
     * getName
     * @return string
     */
    public function getName()
    {
        return 'person_selector';
    }

    /**
     * getParent
     * @return string
     */
    public function getParent()
    {
        return 'autocomplete';
    }

    /**
     * [setDefaultOptions description]
     * @param OptionsResolverInterface $resolver
     * @return Form
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class' => 'GestimeCoreBundle:Medecin',
            'search_fields' => array('p.nom', 'p.prenom'),
            'template' => 'SamsonShowCaseBundle:Autocomplete:person_autocomplete.html.twig',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->leftJoin('p.company', 'c')
                    ->addSelect('c')
                ;
            },
        ));
    }
}
