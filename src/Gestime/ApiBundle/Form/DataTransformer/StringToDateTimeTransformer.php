<?php

namespace Gestime\ApiBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTimeTransformer implements DataTransformerInterface
{

  // transforms the Date to String
  public function transform($val)
  {
    if (null === $val) {
      return '';
    }

    return $val->format('Y-m-d H:n:s');
  }

  // transforms the string to DateTime
  public function reverseTransform($val)
  {
    if (!$val) {
      return null;
    }

    if (!strtotime($val)) {
      throw new TransformationFailedException(sprintf('La date de rendez-vous est incorrecte', $val));
    }

    return new \DateTime($val);
  }
}
