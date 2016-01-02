<?php
/**
 * Copyright © 2015 Martin Kramer. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Shockwavemk\Smtp\Model\Config\Source\Smtp;

use Shockwavemk\Smtp\Model\Config;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('SMTP'), 'value' => Config::TYPE_SMTP],
            ['label' => __('Mailgun'), 'value' => Config::TYPE_MAILGUN],
            ['label' => __('Mandrill'), 'value' => Config::TYPE_MANDRILL],
            ['label' => __('Sendgrid'), 'value' => Config::TYPE_SENDGRID]
        ];
    }
}
