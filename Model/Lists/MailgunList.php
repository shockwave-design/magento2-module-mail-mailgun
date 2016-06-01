<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Model\Lists;

use DateTime;
use Mailgun\Mailgun;

class MailgunList
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

    public function addList()
    {
        # Instantiate the client.
        $mgClient = new Mailgun('YOUR_API_KEY');

# Issue the call to the client.
        $result = $mgClient->post("lists", array(
            'address'     => 'LIST@YOUR_DOMAIN_NAME',
            'description' => 'Mailgun Dev List'
        ));
    }

    public function addMember()
    {
        $listAddress = 'LIST@YOUR_DOMAIN_NAME';

# Issue the call to the client.
        $result = $mgClient->post("lists/$listAddress/members", array(
            'address'     => 'bar@example.com',
            'name'        => 'Bob Bar',
            'description' => 'Developer',
            'subscribed'  => true,
            'vars'        => '{"age": 26}'
        ));
    }
}
