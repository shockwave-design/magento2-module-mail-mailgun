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

    /** @var \Shockwavedesign\Mail\Mailgun\Model\Config */
    protected $_config;

    /** @var null|array */
    protected $_parameters;

    /** @var stdClass */
    protected $_result;

    /** @var \Shockwavemk\Mail\Base\Model\Config */
    protected $_baseConfig;

    /**
     * @param \Shockwavedesign\Mail\Mailgun\Model\Config $config
     * @param \Shockwavemk\Mail\Base\Model\Config $baseConfig
     * @param \Magento\Framework\Mail\MessageInterface $message
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param null $parameters
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
     * Returns wrapped message
     *
     * @return \Zend_Mail
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * Assign wrapped message
     *
     * @param \Zend_Mail $message
     * @return \Shockwavemk\Mail\Base\Model\Transports\TransportInterface
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * Returns result object created by mailgun service
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
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

            /** @var string $recipients comma separated */
            $recipients = implode(',', $this->_message->getRecipients());

            // Assign default parameters
            $parameters = array(
                'from' => $this->_message->getFrom(),
                // Email address for From header
                'to' => $recipients,
                /* to email address of the recipient(s).
                   Example: "Bob <bob@host.com>". You can use commas to separate multiple recipients.*/
                'subject' => quoted_printable_decode($this->_message->getSubject()),
                // Message subject
                'text' => quoted_printable_decode($this->_message->getBodyText(true)),
                // Body of the message. (text version)
                'html' => quoted_printable_decode($this->_message->getBodyHtml(true)),
                // Body of the message. (HTML version)
            );

            $parameters = $this->assignOptionalParameters($parameters);


            /** @var array $postFiles */
            $postFiles = $this->getPostFiles();

            $this->getMail()->setResult(

                $mailgunClient->sendMessage(
                    $this->_config->getMailgunDomain(),
                    $parameters,
                    $postFiles
                )

            );

            $this->getMail()
                ->setSent($this->createSent())
                ->setSentAt($this->createSentAt())
                ->setTransportId($this->createTransportId());
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(
                new \Magento\Framework\Phrase(
                    $e->getMessage()
                ),
                $e
            );
        }
    }

    /**
     * @param $parameters
     * @return mixed
     */
    protected function assignOptionalParameters($parameters)
    {
        // Same as To but for Cc
        if (!empty($this->getMail()->getCc())) {
            $parameters['cc'] = $this->getMail()->getCc();
        }

        //	Same as To but for Bcc
        if (!empty($this->getMail()->getBcc())) {
            $parameters['bcc'] = $this->getMail()->getBcc();
        }

        /* File attachment. You can post multiple attachment values.
           Important: You must use multipart/form-data encoding when sending attachments.*/
        if (!empty($this->getMail()->getMultipartAttachment())) {
            $parameters['attachment'] = $this->getMail()->getMultipartAttachment();
        }

        /* Attachment with inline disposition. Can be used to send inline images (see example).
        You can post multiple inline values.*/
        if (!empty($this->getMail()->getMultipartInline())) {
            $parameters['inline'] = $this->getMail()->getMultipartInline();
        }

        // Tag string. See Tagging for more information.
        if (!empty($this->getMail()->getTags())) {
            $parameters['o:tag'] = $this->getMail()->getTags();
        }

        // Id of the campaign the message belongs to. See um-campaign-analytics for details.
        if (!empty($this->getMail()->getCampaign())) {
            $parameters['o:campaign'] = $this->getMail()->getCampaign();
        }

        // 	Enables/disables DKIM signatures on per-message basis. Pass yes or no
        if (!empty($this->getMail()->getDkimEnabled())) {
            $parameters['o:dkim'] = $this->getMail()->getDkimEnabled();
        }

        /* Desired time of delivery. See Date Format.
           Note: Messages can be scheduled for a maximum of 3 days in the future.*/
        if (!empty($this->getMail()->getDeliveryTime())) {
            $parameters['o:deliverytime'] = $this->getMail()->getDeliveryTime();
        }

        // 	Enables sending in test mode. Pass yes if needed. See Sending in Test Mode
        if (!empty($this->getMail()->getTestMode())) {
            $parameters['o:testmode'] = $this->getMail()->getTestMode();
        }

        /* Toggles tracking on a per-message basis, see Tracking Messages for details.
           Pass yes or no.*/
        if (!empty($this->getMail()->getTrackingEnabled())) {
            $parameters['o:tracking'] = $this->getMail()->getTrackingEnabled();
        }

        /* Toggles clicks tracking on a per-message basis.
           Has higher priority than domain-level setting. Pass yes, no or htmlonly.*/
        if (!empty($this->getMail()->getTrackingClicksEnabled())) {
            $parameters['o:tracking-clicks'] = $this->getMail()->getTrackingClicksEnabled();
        }

        /* Toggles opens tracking on a per-message basis.
           Has higher priority than domain-level setting. Pass yes or no.*/
        if (!empty($this->getMail()->getTrackingOpensEnabled())) {
            $parameters['o:tracking-opens'] = $this->getMail()->getTrackingOpensEnabled();
        }

        /* If set to True this requires the message only be sent over a TLS connection.
          If a TLS connection can not be established, Mailgun will not deliver the message.
          If set to False, Mailgun will still try and upgrade the connection,
          but if Mailgun can not, the message will be delivered over a plaintext SMTP connection.
          The default is False.*/
        if (!empty($this->getMail()->getRequireTlsEnabled())) {
            $parameters['o:require-tls'] = $this->getMail()->getRequireTlsEnabled();
        }

        /* If set to True, the certificate and hostname will not be verified when trying
           to establish a TLS connection and Mailgun will accept any certificate during delivery.
           If set to False, Mailgun will verify the certificate and hostname.
          If either one can not be verified, a TLS connection will not be established.
          The default is False.*/
        if (!empty($this->getMail()->getSkipVerificationEnabled())) {
            $parameters['o:skip-verification'] = $this->getMail()->getSkipVerificationEnabled();
        }

        /* h: prefix followed by an arbitrary value allows to append a custom
          MIME header to the message (X-My-Header in this case).
          For example, h:Reply-To to specify Reply-To address.*/
        if (!empty($this->getMail()->getCustomHeaders())) {
            foreach ($this->getMail()->getCustomHeaders() as $key => $value) {
                $parameters['h:' . $key] = $value;
            }
        }

        /* v: prefix followed by an arbitrary name allows to attach a custom JSON data to the message.
           See Attaching Data to Messages for more information. */
        if (!empty($this->getMail()->getCustomVariables())) {
            foreach ($this->getMail()->getCustomVariables() as $key => $value) {
                $parameters['v:' . $key] = $value;
            }
            return $parameters;
        }
        return $parameters;
    }

    /**
     * Returns wrapped mail object
     *
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
     * Returns all attachments
     *
     * @return array
     * @throws \Magento\Framework\Exception\MailException
     */
    public function getPostFiles()
    {
        $attachmentPathes = [];

        if (empty($mailId = $this->getMail()->getId())) {
            $mailId = $this->getMail()->getParentId();
        }

        foreach ($this->getMail()->getAttachments() as $attachment) {
            $attachmentPathes[] = $this
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
     * @return mixed
     */
    public function createSent()
    {
        /** @var stdClass $result */
        $result = $this->getMail()->getResult();

        if (empty($result)) {
            return null;
        }

        if (!empty($result->http_response_code)
            && $result->http_response_code == 200
        ) {
            return true;
        }

        return false;
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

    /**
     * @return null|string
     */
    public function createTransportId()
    {
        /** @var stdClass $result */
        $result = $this->getMail()->getResult();

        if (empty($result)) {
            return null;
        }

        $responseBody = $result->http_response_body;
        if (!empty($responseBody)
            && !empty($responseBody->id)
        ) {
            return $responseBody->id;
        }

        return null;
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
