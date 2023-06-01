<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Verifico\Ageverify\Model\Config\Source;

class Allspecificcountries implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Verify All Countries')],
            ['value' => 1, 'label' => __('Specific Countries')]
        ];
    }
}
