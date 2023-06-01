<?php    
namespace Verifico\Ageverify\Ui\Component\Listing\Column;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;

class UnityAgeVerificationStatus extends Column
{
    /**
     * @var Magento\Customer\Model\CustomerFactory $block
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepository;

    /**
     * @var Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @param Repository $assetRepository
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerFactory $customerFactory
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Repository $assetRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerFactory $customerFactory,
        UrlInterface $urlBuilder,
        array $components = [], array $data = [])
    {
        $this->assetRepository = $assetRepository;
        $this->customerFactory = $customerFactory;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $resultText = '<div title="This order was placed before the Age Verification plugin was installed or when the plugin was disabled">ⓘ</div>';
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {

                if(empty($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = $resultText;
                }
                
            }
        }

        return $dataSource;
    }
}


