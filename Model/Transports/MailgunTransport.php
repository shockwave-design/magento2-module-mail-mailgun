<?php
/**
 * Copyright © 2015 Martin Kramer. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Shockwavemk\Smtp\Model\Transports;

use Mailgun\Mailgun;

class MailgunTransport implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Zend_Mail $_message
     */
    protected $_message;

    protected $_config;

    protected $_parameters;

    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param null $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Shockwavemk\Smtp\Model\Config $config,
        \Magento\Framework\Mail\MessageInterface $message,
        $parameters = null)
    {
        $this->_config = $config;
        $this->_message = $message;
        $this->_parameters = $parameters;
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {

            /** @var $mailgunClient Mailgun */
            $mailgunClient = new Mailgun(
                $this->_config->getMailgunKey()
            );

            $recipients = implode(',', $this->_message->getRecipients());
            $parameters = array(
                'from'    => $this->_message->getFrom(),
                'to'      => $recipients,
                'subject' => quoted_printable_decode($this->_message->getSubject()),
                'text'    => quoted_printable_decode($this->_message->getBodyText(true)),
                'html'    => quoted_printable_decode($this->_message->getBodyHtml(true))
            );

            // TODO add attachment support
            $attachments = array();
            /*
            $attachments = array(
                'attachment' => array(
                    '/path/to/file.txt',
                    '/path/to/file.txt'
                )
            );
            */

            # Make the call to the client.
            $result = $mailgunClient->sendMessage(
                $this->_config->getMailgunDomain(),
                $parameters,
                $attachments
            );



        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
