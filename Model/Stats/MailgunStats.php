<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Model\Stats;

use DateTime;
use Mailgun\Mailgun;

class MailgunStats
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

    public function getTotal()
    {
        # Issue the call to the client.
        $result = $mgClient->get("$domain/stats/total", array(
            'event' => array('accepted', 'delivered', 'failed'),
            'duration' => '1m'
        ));

    }
}
