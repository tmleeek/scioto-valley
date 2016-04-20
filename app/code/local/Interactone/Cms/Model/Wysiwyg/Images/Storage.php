<?php

class Interactone_Cms_Model_Wysiwyg_Images_Storage extends Mage_Cms_Model_Wysiwyg_Images_Storage
{
    /**
     * Thumbnail URL getter
     *
     * Override as by default Magento is failing to find the thumbnail and instead re-generating each time
     * @param  string $filePath original file path
     * @param  boolean $checkFile OPTIONAL is it necessary to check file availability
     * @return string | false
     */

    //const THUMBS_DIRECTORY_NAME = 'thumbs';

    public function getThumbnailUrl($filePath, $checkFile = false)
    {
        $mediaRootDir = Mage::getConfig()->getOptions()->getMediaDir() . DS;

        if (strpos($filePath, $mediaRootDir) === 0) {
            $thumbSuffix = self::THUMBS_DIRECTORY_NAME . DS . substr($filePath, strlen($mediaRootDir));
            if (!$checkFile || is_readable($this->getHelper()->getStorageRoot() . $thumbSuffix)) {
                $randomIndex = '?rand=' . time();
                return str_replace('\\', '/', $this->getHelper()->getBaseUrl() . $thumbSuffix) . $randomIndex;
            }
        }
        return false;
    }

    /**
     * Thumbnail root directory getter
     *
     * @return string
     */
//    public function getThumbnailRoot()
//    {
//        return $this->getHelper()->getStorageRoot() . self::THUMBS_DIRECTORY_NAME;
//    }
}