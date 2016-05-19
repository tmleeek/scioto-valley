<?php

class MageWorx_Downloads_Model_Import extends Mage_Core_Model_Abstract
{
    public function importFilesRelations($csvPath, $uploadedFiles)
    {
        $csv = new Varien_File_Csv();
        $data = $csv->getData($csvPath);

        foreach ($data as $line) {
            if (!is_array($line) || count($line) != 2) {
                continue;
            }

            $files = explode(';', $line[1]);
            $fileIds = array();
            foreach ($files as $path) {
                $fileIds[] = array_search(Mage::getBaseDir('media') . '/downloads_import/' . $path, $uploadedFiles);
            }

            $skus = explode(';', $line[0]);
            $this->_assignFiles($fileIds, $skus);
        }
    }

    protected function _assignFiles($files, $skus)
    {
        foreach ($files as $fileId){
            foreach ($skus as $sku){
                $prod = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                if (!$prod || !$prod->getId()) {
                    continue;
                }

                $file = Mage::getModel('downloads/files')->load($fileId);
                if (!$file || !$file->getId()) {
                    continue;
                }

                $relation = Mage::getModel('downloads/relation');
                $relation->setData(array(
                    'file_id' => $fileId,
                    'product_id' => $prod->getId()
                ));
                $relation->save();
            }
        }

        return $this;
    }
}