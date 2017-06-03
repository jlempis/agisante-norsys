<?php

namespace Gestime\MessageBundle\Event;

/**
 * MessageListener
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class MessageListener
{
    protected $serviceSms;

    /**
     * [__construct description]
     * @param SMSService $service
     */
    public function __construct( $service)
    {
        $this->serviceSms = $service;
    }

    /**
     * Envoie du message dans la queue (Traitée par RabbitMq)
     * @param  MessagePostEvent $event [description]
     * @return [type]                  [description]
     */
    public function onMessagePost(MessagePostEvent $event)
    {
        $this->serviceSms
         ->addMessageToQueue($event->getMessage());
    }
}
