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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setMail($mail)
    {
        $this->_mail = $mail;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setTrackingClicksEnabled($trackingClicksEnabled)
    {
        $this->_trackingClicksEnabled = $trackingClicksEnabled;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setTrackingOpensEnabled($trackingOpensEnabled)
    {
        $this->_trackingOpensEnabled = $trackingOpensEnabled;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setTrackingEnabled($trackingEnabled)
    {
        $this->_trackingEnabled = $trackingEnabled;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setAttachments($attachments)
    {
        $this->_attachments = $attachments;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setInlines($inlines)
    {
        $this->_inlines = $inlines;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setDeliveryTime($deliveryTime)
    {
        $this->_deliveryTime = $deliveryTime;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setTags($tags)
    {
        $this->_tags = $tags;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setResult($result)
    {
        $this->_result = $result;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSent()
    {
        $result = $this->getResult();

        if(empty($result))
        {
            return null;
        }


    }

    /**
     * @param $sent
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setSent($sent)
    {
        $this->_sent = $sent;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setTestMode($testMode)
    {
        $this->_testMode = $testMode;
        return $this;
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
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setRecipientVariables($recipientVariables)
    {
        $this->_recipientVariables = $recipientVariables;
        return $this;
    }

    public function getPostFiles()
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
                $mailgunClient->sendMessage(
                    $this->_config->getMailgunDomain(),
                    $parameters,
                    $this->getPostFiles()
                )
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
