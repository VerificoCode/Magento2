<?php

namespace Verifico\Ageverify\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class UnityAgeVerificationStatus extends Column
{
    /**
     * Markup AgeChecked Plus+ may use in the status (for example "Age Verified*<br /><small>...</small>").
     * Anything else, including script, event handlers and images, is stripped by the escaper.
     */
    public const ALLOWED_TAGS = ['br', 'small', 'strong', 'b', 'i', 'em', 'span', 'style'];

    private const NOT_SET = '<div title="This order was placed before the Age Verification plugin was installed or when the plugin was disabled">ⓘ</div>';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $name = $this->getData('name');
        foreach ($dataSource['data']['items'] ?? [] as $i => $item) {
            // Column renders as HTML and the value is written through the API, so only allow a few tags
            $dataSource['data']['items'][$i][$name] = empty($item[$name])
                ? self::NOT_SET
                : $this->escaper->escapeHtml((string) $item[$name], self::ALLOWED_TAGS);
        }

        return $dataSource;
    }
}
