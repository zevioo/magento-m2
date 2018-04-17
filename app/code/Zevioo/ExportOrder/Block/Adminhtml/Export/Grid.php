<?php
namespace Zevioo\ExportOrder\Block\Adminhtml\Export;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Zevioo\ExportOrder\Model\exportFactory
     */
    protected $_exportFactory;

    /**
     * @var \Zevioo\ExportOrder\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Zevioo\ExportOrder\Model\exportFactory $exportFactory
     * @param \Zevioo\ExportOrder\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Zevioo\ExportOrder\Model\ExportFactory $ExportFactory,
        \Zevioo\ExportOrder\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_exportFactory = $ExportFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('order_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_exportFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'order_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'order_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
		$this->addColumn(
			'order_send_data',
			[
				'header' => __('Order Send Data'),
				'index' => 'order_send_data',
			]
		);
		
				
		$this->addColumn(
			'status',
			[
				'header' => __('Status'),
				'index' => 'status',
				'type' => 'options',
				'options' => \Zevioo\ExportOrder\Block\Adminhtml\Export\Grid::getOptionArray1()
			]
		);
				
				
		$this->addColumn(
			'order_request_data',
			[
				'header' => __('Order Request Data'),
				'index' => 'order_request_data',
			]
		);
		
		$this->addColumn(
			'created_at',
			[
				'header' => __('Created At'),
				'index' => 'created_at',
				'type'      => 'datetime',
                'width'     => 200
			]
		);
					
					


		

		
		   $this->addExportType($this->getUrl('exportorder/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('exportorder/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('order_id');
        //$this->getMassactionBlock()->setTemplate('Zevioo_ExportOrder::export/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('export');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('exportorder/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('exportorder/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('exportorder/*/index', ['_current' => true]);
    }

    /**
     * @param \Zevioo\ExportOrder\Model\export|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		return '#';
    }

	
		static public function getOptionArray1()
		{
            $data_array=array(); 
			$data_array[0]='Order Success';
			$data_array[1]='Order Cancel';
            return($data_array);
		}
		static public function getValueArray1()
		{
            $data_array=array();
			foreach(\Zevioo\ExportOrder\Block\Adminhtml\Export\Grid::getOptionArray1() as $k=>$v){
               $data_array[]=array('value'=>$k,'label'=>$v);		
			}
            return($data_array);

		}
		

}