<?php

class Watsons_Sync_Model_Order extends Varien_Object
{
    /* internal constants */
    const BACKUP_EXTENSION  = 'gz';
    const COMPRESS_RATE     = 9;

    /**
     * Gz file pointer
     *
     * @var resource
     */
    protected $_handler = null;

    public function __construct() {
        parent::__construct();

        $filePath = Mage::getBaseDir('var')
            . DS . 'watsons_sync' . DS . 'orders';

        $this->setPath($filePath);
        $this->setFilePath($filePath);
    }

    /**
     * Load sync file info
     *
     * @param string fileName
     * @param string filePath
     * @return Watsons_Sync_Model_Order
     */
    public function load($fileName, $filePath=null)
    {
        if ($filePath == null) {
            $fullFilePath = $filePath . DS . $fileName;
        } else {
            $fullFilePath = $filePath . DS . $fileName;
        }
        $time = filectime($fullFilePath);
        $type = pathinfo($fullFilePath, PATHINFO_EXTENSION);

        $this->addData(array(
            'id'    => $filePath . DS . $fileName,
            'time'  => (int)$time,
            'path'  => $filePath,
            'name'  => $fileName,
            'date_object' => new Zend_Date((int)$time)
        ));
        $this->setType($type);
        return $this;
    }

    /**
     * Checks sync file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return is_file($this->getPath() . DS . $this->getFileName());
    }

    /**
     * Return file name of sync file
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->getName();
    }

    /**
     * Set the sync file content
     *
     * @param string $content
     * @return Watsons_Sync_Model_Order
     * @throws Watsons_Sync_Exception
     */
    public function setFile(&$content)
    {
        if (!$this->hasData('time') || !$this->hasData('type') || !$this->hasData('path')) {
            Mage::throwException(Mage::helper('sync')->__('Wrong order of creation for new sync'));
        }

        $ioProxy = new Varien_Io_File();
        $ioProxy->setAllowCreateFolders(true);
        $ioProxy->open(array('path'=>$this->getPath()));

        $compress = 0;
        if (extension_loaded("zlib")) {
            $compress = 1;
        }

        $rawContent = '';
        if ( $compress ) {
            $rawContent = gzcompress( $content, self::COMPRESS_RATE );
        } else {
            $rawContent = $content;
        }

        $fileHeaders = pack("ll", $compress, strlen($rawContent));
        $ioProxy->write($this->getFileName(), $fileHeaders . $rawContent);
        return $this;
    }

    /**
     * Return content of sync file
     *
     * @todo rewrite to Varien_IO, but there no possibility read part of files.
     * @return string
     * @throws Watsons_Sync_Exception
     */
    public function &getFile()
    {

        if (!$this->exists()) {
            Mage::throwException(Mage::helper('sync')->__(
                "Order file doesn't exist"
            ));
        }

        $content = file_get_contents($this->getPath() . DS . $this->getFileName());

        return $content;
    }

    /**
     * Delete sync file
     *
     * @throws Watsons_Sync_Exception
     */
    public function deleteFile()
    {
        if (!$this->exists()) {
            Mage::throwException(Mage::helper('sync')->__("Order file doesn't exist"));
        }

        $ioProxy = new Varien_Io_File();
        $ioProxy->open(array('path'=>$this->getPath()));
        $ioProxy->rm($this->getFileName());
        return $this;
    }

    /**
     * Open sync file (write or read mode)
     *
     * @param bool $write
     * @return Watsons_Sync_Model_Order
     */
    public function open($write = false)
    {
        if (is_null($this->getPath())) {
            Mage::exception('Watsons_Sync', Mage::helper('sync')->__('Order file path don\'t specify'));
        }

        $ioAdapter = new Varien_Io_File();
        try {
            $path = $ioAdapter->getCleanPath($this->getPath());
            $ioAdapter->checkAndCreateFolder($path);
            $filePath = $path . DS . $this->getFileName();
        }
        catch (Exception $e) {
            Mage::exception('Watsons_Sync', $e->getMessage());
        }

        if ($write && $ioAdapter->fileExists($filePath)) {
            $ioAdapter->rm($filePath);
        }
        if (!$write && !$ioAdapter->fileExists($filePath)) {
            Mage::exception('Watsons_Sync', Mage::helper('sync')->__('Order file "%s" doesn\'t exist', $this->getFileName()));
        }

        $mode = $write ? 'wb' . self::COMPRESS_RATE : 'rb';

        try {
            $this->_handler = fopen($filePath, $mode);
        }
        catch (Exception $e) {
            Mage::exception('Watsons_Sync', Mage::helper('sync')->__('Order file "%s" can\'t read or write', $this->getFileName()));
        }

        return $this;
    }

    /**
     * Read sync uncomressed data
     *
     * @param int $length
     * @return string
     */
    public function read($length)
    {
        if (is_null($this->_handler)) {
            Mage::exception('Watsons_Sync', Mage::helper('sync')->__('Order file handler don\'t specify'));
        }

        return fgets($this->_handler, $length);
    }

    public function eof()
    {
        if (is_null($this->_handler)) {
            Mage::exception('Watsons_Sync', Mage::helper('sync')->__('Order file handler don\'t specify'));
        }

        return feof($this->_handler);
    }

    /**
     * Write to sync file
     *
     * @param string $string
     * @return Watsons_Sync_Model_Order
     */
    public function write($string)
    {
        if (is_null($this->_handler)) {
            Mage::exception('Watsons_Sync', Mage::helper('sync')->__('Order file handler don\'t specify'));
        }

        try {
            fwrite($this->_handler, $string);
        }
        catch (Exception $e) {
            Mage::exception('Watsons_Sync', Mage::helper('sync')->__('Error write to Order file "%s"', $this->getFileName()));
        }

        return $this;
    }

    /**
     * Close open sync file
     *
     * @return Watsons_Sync_Model_Order
     */
    public function close()
    {
        @fclose($this->_handler);
        $this->_handler = null;

        return $this;
    }

    /**
     * Print output
     *
     */
    public function output()
    {
        if (!$this->exists()) {
            return ;
        }

        $ioAdapter = new Varien_Io_File();
        $ioAdapter->open(array('path' => $this->getPath()));

        $ioAdapter->streamOpen($this->getFileName(), 'r');
        while ($buffer = $ioAdapter->streamRead()) {
            echo $buffer;
        }
        $ioAdapter->streamClose();
    }

    public function getSize()
    {
        if (!is_null($this->getData('size'))) {
            return $this->getData('size');
        }

        if ($this->exists()) {
            $this->setData('size', filesize($this->getPath() . DS . $this->getFileName()));
            return $this->getData('size');
        }

        return 0;
    }
}