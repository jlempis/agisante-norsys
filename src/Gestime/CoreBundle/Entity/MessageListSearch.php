<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Entity;
/**
 * MessageListSearch
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class MessageListSearch
{
    public $texte;
    public $action;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->action = 't';
    }
}
