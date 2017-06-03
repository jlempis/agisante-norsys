<?php

namespace Gestime\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * DatePickerType
 * Composant date
 */
class DatePickerType extends AbstractType
{
    /**
     * [setDefaultOptions description]
     * @param OptionsResolverInterface $resolver [description]
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
        ));
    }

    /**
     * getParent
     * @return string
     */
    public function getParent()
    {
        return 'date';
    }

    /**
     * getName
     * @return string
     */
    public function getName()
    {
        return 'datePicker';
    }
}
