<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Model\Suppressions;

use DateTime;
use Mailgun\Mailgun;

class MailgunSuppression
{
    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param null $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Shockwavedesign\Mail\Mailgun\Model\Config $config,
        \Magento\Framework\Mail\MessageInterface $message,
        $parameters = null)
    {
        $this->_config = $config;
        $this->_message = $message;
        $this->_parameters = $parameters;
    }

    public function getComplaints()
    {
        $result = $mgClient->get("$domain/complaints", array(
            'limit' => 10,
            'skip'  => 5
        ));
    }
}
