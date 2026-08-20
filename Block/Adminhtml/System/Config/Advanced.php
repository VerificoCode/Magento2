<?php

namespace Verifico\Ageverify\Block\Adminhtml\System\Config;

use Magento\Framework\App\ObjectManager;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

use Verifico\Ageverify\Model\Config;

class Advanced extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * Template path
     *
     * @var string
     */
    protected $_template = 'system/config/advanced/verifico.phtml';

    protected $unityConfig;

    public function __construct(
        Context $context,
        \Verifico\Ageverify\Model\Config $unityConfig,
        array $data = [],
        ?SecureHtmlRenderer $secureRenderer = null
    ) {
        parent::__construct($context, $data);
        $this->unityConfig = $unityConfig;
    }

    /**
     * Render fieldset html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $columns = $this->getRequest()->getParam('website') || $this->getRequest()->getParam('store') ? 5 : 4;
        return $this->_decorateRowHtml($element, "<td colspan='{$columns}'>" . $this->toHtml() . '</td>');
    }

    public function getUnityName() {
        return $this->unityConfig->getUnityName();
    }
}