<?php
namespace Verifico\Ageverify\Controller\Adminhtml\Register;

use Verifico\Ageverify\Model\Register;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory = false;
    protected $register;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
    Context $context,
    PageFactory $resultPageFactory,
    Register $register
    ) {
    parent::__construct($context);
    $this->resultPageFactory = $resultPageFactory;
    $this->register = $register; 
  }


    /**
     * Hello test controller page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {   
        return $this->resultPageFactory->create();
    }

    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Verifico_Ageverify::register');
    }
}