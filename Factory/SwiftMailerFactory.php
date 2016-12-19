<?php

namespace DavidKmenta\CommandSupervisorBundle\Factory;

class SwiftMailerFactory
{
    /**
     * @param string $subject
     * @param string $body
     * @param string $recipient
     * @param string|null $from
     * @return \Swift_Message
     */
    public function getMessage($subject, $body, $recipient, $from = null)
    {
        $message = \Swift_Message::newInstance($subject, $body);
        $message->addTo($recipient);
        $message->addFrom($from ?: $recipient);

        return $message;
    }
}
