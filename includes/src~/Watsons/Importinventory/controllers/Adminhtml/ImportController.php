<?php
class Watsons_Importinventory_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    public function upload_FormAction()
    {
        $type = Zend_Filter::filterStatic(
            $this->getRequest()->getParam('type')
            , 'Alnum'
        );

        Mage::register('sync_import_data', array(
            'type'          => $type
            , 'type_label'  => $this->_toLabel($type)
        ));

        $this->loadLayout();
        $this->_setActiveMenu('sync/import');
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent(
            $this->getLayout()->createBlock(
                'sync/adminhtml_import'
            )
        );

        $this->renderLayout();
    }

    public function saveAction()
    {
        set_time_limit(-1);

        $uploadedFile   = $_FILES['file'];
        $ioFile         = new Varien_Io_File();

        if (is_uploaded_file($uploadedFile['tmp_name'])) {
            $filePath   = $uploadedFile['tmp_name'];

             ini_set('auto_detect_line_endings', true);

            $csvData= array();
            $fh     = fopen($filePath, 'r');
            while ($rowData = fgetcsv($fh)) { $csvData[] = $rowData; }
            fclose($fh);

            $ioFile->rm($filePath);

            $type = Zend_Filter::get(
                $this->getRequest()->getParam('type')
                , 'Alnum'
            );

            try {
                // handles the file upload and processing
                $syncModel = Mage::getModel('sync/sync');
                switch ($type) {
                    case 'prices':
                        $syncModel->updateProductPrices($csvData);
                        break;
                    case 'inventory':
                        $syncModel->updateProductInventory($csvData);
                        break;
                    case 'ordernumbers':
                        $syncModel->updateRetailOrderNumbers($csvData);
                        break;
                    default:
                        throw Mage::exception('Watsons_Sync', 'Invalid Type!');
                        break;
                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('sync')->__(
                        'File was successfully processed!'
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addException(
                    $e, $e->getMessage()
                );
            }
        } else {

        }

        $this->_redirect('*/*/upload_form/type/' . $type);
    }

    protected function _toLabel($key)
    {
        switch ($key) {
            case 'prices':
                $label = Mage::helper('sync')->__('Product Prices');
                break;
            case 'inventory':
                $label = Mage::helper('sync')->__('Product Inventory');
                break;
            case 'ordernumbers':
                $label = Mage::helper('sync')->__('Retail Order Numbers');
                break;
            default:
                $label = $key;
                break;
        }
        return $label;
    }
}