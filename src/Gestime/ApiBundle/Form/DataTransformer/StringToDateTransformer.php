<?php

namespace Gestime\ApiBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class StringToDateTransformer implements DataTransformerInterface
{

  // transforms the Date to String
  public function transform($val)
  {
    if (null === $val) {
      return '';
    }
    return $val->format('Y-m-d');
  }

  // transforms the string to Date
  public function reverseTransform($val)
  {
    if (!$val) {
      return null;
    }

    if (!strtotime($val)) {
      throw new TransformationFailedException(sprintf('La date de naissance est incorrecte', $val));
    }

    return new \DateTime($val);
  }
}
