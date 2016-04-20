<?php

class Watsons_Sync_Model_Fs_Collection extends Varien_Data_Collection_Filesystem
{
    const EXTENSION = 'csv';

    /**
     * Folder, where all exports are stored
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Set collection specific parameters and make sure export
     * folder will exist
     */
    public function __construct()
    {
        parent::__construct();

        $this->_baseDir = Mage::getBaseDir('var')
            . DS . 'watsons_sync' . DS . 'orders';

        // check for valid base dir
        $ioProxy = new Varien_Io_File();
        $ioProxy->mkdir($this->_baseDir);
        if (!is_file($this->_baseDir . DS . '.htaccess')) {
            $ioProxy->open(array('path' => $this->_baseDir));
            $ioProxy->write('.htaccess', 'deny from all', 0644);
        }

        // set collection specific params
        $this
            ->setOrder('time', self::SORT_ORDER_DESC)
            ->addTargetDir($this->_baseDir)
            ->setFilesFilter(
                '/^[a-z0-9\-\_]+\.'
                . preg_quote(self::EXTENSION, '/')
                . '$/'
            )
            ->setCollectRecursively(false)
        ;
    }

    protected function _generateRow($filename)
    {
        $row = parent::_generateRow($filename);
        foreach (Mage::getSingleton('sync/order')->load($row['basename'], $this->_baseDir)
            ->getData() as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = filesize($filename);
        return $row;
    }
}