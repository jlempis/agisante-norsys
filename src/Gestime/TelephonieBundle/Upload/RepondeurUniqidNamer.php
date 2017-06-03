<?php

namespace Gestime\TelephonieBundle\Upload;

use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;

/**
 * RepondeurUniqidNamer
 *
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 */
class RepondeurUniqidNamer implements NamerInterface
{
    /**
     * {@inheritDoc}
     */
    public function name($obj, PropertyMapping $mapping)
    {
        $refObj = new \ReflectionObject($obj);

        $refProp = $refObj->getProperty($mapping);
        $refProp->setAccessible(true);

        $file = $refProp->getValue($obj);

        $prefix = 'rep';
        $name = uniqid();

        if ($extension = $file->guessExtension()) {
            $name = sprintf('%s%s.%s', $prefix, $name, $file->guessExtension());
        }

        return $name;
    }
}
