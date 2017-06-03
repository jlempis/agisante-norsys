<?php

/**
 * @category Forms
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Services;

use Gestime\CoreBundle\Entity\Utilisateur;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Scheb\TwoFactorBundle\Mailer\AuthCodeMailerInterface;
use Gestime\MessageBundle\Sms\SMSService;

/**
 * Interface entre le mail et le SMS
 *
 */
class SmsMailer implements AuthCodeMailerInterface
{
    private $smsSender;
    private $senderMail;
    private $mailer;
    private $isSmsDisabled;
    private $deliveryPhoneNumber;
    private $senderAddress;

    /**
     * __construct
     * @param SMSService    $smsSender
     * @param \Swift_Mailer $mailer
     * @param string        $senderAddress
     */
    public function __construct(SMSService $smsSender, \Swift_Mailer $mailer, $senderAddress)
    {
        $this->smsSender = $smsSender;
        $this->mailer = $mailer;
        $this->isSmsDisabled = false;
        $this->deliveryPhoneNumber = '0102030405';
        $this->senderAddress = $senderAddress;
    }

    /**
     * sendAuthCode
     * @param  TwoFactorInterface $user
     * @return [type]
     */
    public function sendAuthCode(TwoFactorInterface $user)
    {
        $msg = 'Votre code de validation est le ' . $user->getEmailAuthCode();
        $fromName = 'Doc24.fr';
        $this->sendSMS($user, $msg, $fromName);
    }

    /**
     * [sendSMS description]
     * @param  User   $user
     * @param  string $msg
     * @param  string $fromName
     * @return [type]
     */
    public function sendSMS(Utilisateur $user, $msg, $fromName)
    {
        // Fallback to mail if isSmsDisabled
        if ($this->isSmsDisabled) {
            $this->sendMail($fromName, 'Contact@doc24.fr', $user->getEmail(), $msg);
        } else {
            if ($this->deliveryPhoneNumber !== null) {
                $number = $this->deliveryPhoneNumber;
            } else {
                $number = $user->getPhoneNumber();
            }

            $this->smsSender->sendMessage(9999, $msg, $number);
        }
    }

    /**
     * [sendMail description]
     * @param  string $deliveryAddress [description]
     * @param  string $msg             [description]
     * @param  string $fromName        [description]
     * @return string                  [description]
     */
    public function sendMail($fromName, $senderAddress, $deliveryAddress, $password='')
    {
      

      return true;
    }
}
