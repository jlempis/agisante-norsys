<?php

/**
 * @category Entities
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\SynchroV1Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * V1 : Log_SMS
 *
 * @ORM\Table(name="LOG_SMS")
 * @ORM\Entity(repositoryClass="Gestime\SynchroV1Bundle\Entity\SmsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Sms
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="c_id_pers")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
}
