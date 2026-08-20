<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Verifico\Ageverify\Model\Config\Source;

class Allspecificproducts implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('All Customers')],
            ['value' => 2, 'label' => __('Specific Categories')],
            ['value' => 1, 'label' => __('Specific Products')]
        ];
    }
}
