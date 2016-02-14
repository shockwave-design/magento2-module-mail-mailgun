<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Model\Transports;

use DateTime;
use Mailgun\Mailgun;

class MailgunTransport implements \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
{
    /**
     * @var \Zend_Mail $_message
     */
    protected $_message;

    protected $_mail;

    protected $_config;

    protected $_parameters;

    protected $_result;

    protected $_tags;

    protected $_testMode;

    protected $_recipientVariables;

    protected $_deliveryTime;

    protected $_attachments;

    protected $_trackingEnabled;

    protected $_trackingOpensEnabled;

    protected $_trackingClicksEnabled;

    protected $_sent;

    protected $_messages;

    protected $_inlines;

    /**
     * @return \Zend_Mail
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @param \Zend_Mail $message
     */
    public function setMessage($message)
    {
        $this->_message = $message;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->_mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->_mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getTrackingClicksEnabled()
    {
        return $this->_trackingClicksEnabled;
    }

    /**
     * @param boolean $trackingClicksEnabled
     */
    public function setTrackingClicksEnabled($trackingClicksEnabled)
    {
        $this->_trackingClicksEnabled = $trackingClicksEnabled;
    }

    /**
     * @return mixed
     */
    public function getTrackingOpensEnabled()
    {
        return $this->_trackingOpensEnabled;
    }

    /**
     * @param boolean $trackingOpensEnabled
     */
    public function setTrackingOpensEnabled($trackingOpensEnabled)
    {
        $this->_trackingOpensEnabled = $trackingOpensEnabled;
    }

    /**
     * @return mixed
     */
    public function getTrackingEnabled()
    {
        return $this->_trackingEnabled;
    }

    /**
     * @param boolean $trackingEnabled
     */
    public function setTrackingEnabled($trackingEnabled)
    {
        $this->_trackingEnabled = $trackingEnabled;
    }

    /**
     * @return mixed
     */
    public function getAttachments()
    {
        return $this->_attachments;
    }

    /**
     * @param array $attachments
     */
    public function setAttachments($attachments)
    {
        $this->_attachments = $attachments;
    }

    /**
     * @return mixed
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * @param array $messages
     */
    public function setMessages($messages)
    {
        $this->_messages = $messages;
    }

    /**
     * @return mixed
     */
    public function getInlines()
    {
        return $this->_inlines;
    }

    /**
     * @param array $inlines
     */
    public function setInlines($inlines)
    {
        $this->_inlines = $inlines;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTime()
    {
        return $this->_deliveryTime;
    }

    /**
     * @param DateTime $deliveryTime
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->_deliveryTime = $deliveryTime;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * @param $tags
     */
    public function setTags($tags)
    {
        $this->_tags = $tags;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * @param $result
     */
    public function setResult($result)
    {
        $this->_result = $result;
    }

    /**
     * @return mixed
     */
    public function getSent()
    {
        return $this->_sent;
    }

    /**
     * @param $sent
     */
    public function setSent($sent)
    {
        $this->_sent = $sent;
    }

    /**
     * @return mixed
     */
    public function getTestMode()
    {
        return $this->_testMode;
    }

    /**
     * @param $testMode
     */
    public function setTestMode($testMode)
    {
        $this->_testMode = $testMode;
    }

    public function getTransportId()
    {
        return 0; // TODO
    }

    /**
     * @return string
     */
    public function getRecipientVariables()
    {
        return $this->_recipientVariables;
    }

    /**
     * @param mixed $recipientVariables
     */
    public function setRecipientVariables($recipientVariables)
    {
        $this->_recipientVariables = $recipientVariables;
    }

    protected function getPostFiles()
    {
        return array(
            'attachment' => $this->getAttachments(),
            'inline' => $this->getInlines(),
            'message' => $this->getMessages()
        );
    }

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

        $this->setTags(
            array('Magento')
        );


        $this->setRecipientVariables(
            '{"bob@example.com": {"first":"Bob", "id":1},
              "alice@example.com": {"first":"Alice", "id": 2}}'
        );

        $this->setTrackingEnabled(true);
        $this->setTrackingClicksEnabled(true);
        $this->setTrackingOpensEnabled(true);
    }

    /**
     * Send a mail using this transport
     *
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try
        {
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

            $this->setResult(
                /*
                $mailgunClient->sendMessage(
                    $this->_config->getMailgunDomain(),
                    $parameters,
                    $this->getPostFiles()
                )
                */
                new \stdClass()
            );
        }
        catch (\Exception $e)
        {
            throw new \Magento\Framework\Exception\MailException(
                new \Magento\Framework\Phrase(
                    $e->getMessage()
                ),
                $e
            );
        }
    }
}
