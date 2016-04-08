<?php
/**
 * Copyright 2016 Shockwave-Design - J. & M. Kramer, all rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Shockwavedesign\Mail\Mailgun\Model\Transports;

use Mailgun\Mailgun;
use stdClass;

class MailgunTransport implements \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
{
    const ATTACHMENT_FOLDER = 'attachments';
    /**
     * @var \Zend_Mail $_message
     */
    protected $_message;

    /** @var  \Shockwavemk\Mail\Base\Model\Mail */
    protected $_mail;

    protected $_config;

    protected $_parameters;

    protected $_result;

    /** @var \Shockwavemk\Mail\Base\Model\Config */
    protected $_baseConfig;


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
     * @return \Shockwavemk\Mail\Base\Model\Mail
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
    public function getResult()
    {
        return $this->_result;
    }





    public function getPostFiles()
    {
        $attachmentPathes = [];

        if(empty($mailId = $this->getMail()->getId())) {
            $mailId = $this->getMail()->getParentId();
        }

        foreach($this->getMail()->getAttachments() as $attachment) {
            $attachmentPathes[] =  $this
                    ->getMail()
                    ->getStoreage()
                    ->getTempFilePath() .
                $mailId . DIRECTORY_SEPARATOR .
                self::ATTACHMENT_FOLDER .
                $attachment->getFilePath();
        }

        return array(
            'attachment' => $attachmentPathes,
            'inline' => $this->getMail()->getAdditionalInlines(),
            'message' => $this->getMail()->getAdditionalMessages()
        );
    }

    /**
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param null $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Shockwavedesign\Mail\Mailgun\Model\Config $config,
        \Shockwavemk\Mail\Base\Model\Config $baseConfig,
        \Magento\Framework\Mail\MessageInterface $message,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $parameters = null)
    {
        $this->_config = $config;
        $this->_baseConfig = $baseConfig;
        $this->_message = $message;
        $this->_parameters = $parameters;
        $this->_dateTime = $dateTime;
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

            $postFiles = $this->getPostFiles();

            $test = $this->createTestResult();

            $this->getMail()->setResult(

                $mailgunClient->sendMessage(
                    $this->_config->getMailgunDomain(),
                    $parameters,
                    $postFiles
                )

                //$test
            );

            $this->getMail()
                ->setSent($this->createSent())
                ->setSentAt($this->createSentAt())
                ->setTransportId($this->createTransportId())
            ;
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

    /**
     * @return null|string
     */
    public function createSentAt()
    {
        return $this->_dateTime->formatDate(
            new \DateTime()
        );
    }

    public function createTransportId()
    {
        /** @var stdClass $result */
        $result = $this->getMail()->getResult();

        if(empty($result))
        {
            return null;
        }

        $responseBody = $result->http_response_body;
        if(!empty($responseBody) && !empty($responseBody->id))
        {
            return $responseBody->id;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function createSent()
    {
        /** @var stdClass $result */
        $result = $this->getMail()->getResult();

        if(empty($result))
        {
            return null;
        }

        if(!empty($result->http_response_code) && $result->http_response_code == 200)
        {
            return true;
        }

        return false;
    }



    /**
     * @return \stdClass
     */
    public function createTestResult()
    {
        $test = new \stdClass();
        $test->http_response_code = 200;

        $testBody = new \stdClass();
        $testBody->id = '<xyz>';

        $test->http_response_body = $testBody;
        return $test;
    }
}
