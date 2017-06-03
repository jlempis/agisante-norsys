<?php

namespace Gestime\MessageBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Gestime\CoreBundle\Entity\Message;

/**
 * MessagePostEvent
 *
 */
class MessagePostEvent extends Event
{
    protected $message;

    /**
     * [__construct description]
     * @param Message $message [description]
     */
    public function __construct(Message $message)
    {
        $this->message  = $message;
    }

    /**
     * Récupération du message
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
