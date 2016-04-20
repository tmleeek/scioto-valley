<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This software is designed to work with Magento community edition and
 * its use on an edition other than specified is prohibited. aheadWorks does not
 * provide extension support in case of incorrect edition use.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Advancedsearch
 * @version    1.4.8
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Advancedsearch_Model_Indexer_Catalog extends AW_Advancedsearch_Model_Indexer_Abstract
{
    const PRIMARY = 'entity_id';
    const RESULT_KEY = '_product_id';

    protected $_indexTableModel = null;

    /**
     * @return null|string
     */
    public function getResultKey()
    {
        return self::RESULT_KEY;
    }

    /**
     * @param null $store
     *
     * @return array
     */
    public function getAdvancedFilter($store = null)
    {
        $storeId = Mage::app()->getStore($store)->getId();
        return array(
            'key' => '_store_id',
            'value' => array($storeId)
        );
    }

    protected function _getIndexName()
    {
        return 'catalog_';
    }

    protected function _extendSqlAttributes(&$attributes)
    {
        foreach ($attributes as $k => $v) {
            if (!isset($v['extended'])) {
                $attributes[$k]['extended'] = 0;
            }
        }
        $attributes[] = array(
            'attribute' => self::RESULT_KEY,
            'type' => 'int',
            'unsigned' => true,
            'is_null' => false,
            'default' => 0,
            'extra' => ''
        );
        $attributes[] = array(
            'attribute' => '_store_id',
            'type' => 'int',
            'unsigned' => true,
            'is_null' => false,
            'default' => 0,
            'extra' => ''
        );
        $attributes[] = array(
            'attribute' => '_updated',
            'type' => 'tinyint',
            'unsigned' => true,
            'is_null' => false,
            'default' => 0,
            'extra' => ''
        );
        return $attributes;
    }

    protected function _getTextColumnDefinition()
    {
        return array(
            'unsigned' => null,
            'default' => null,
            'extra' => null,
            'type' => 'text',
            'is_null' => true
        );
    }

    protected function _createTable()
    {
        $pa = Mage::getResourceSingleton('catalog/product')->loadAllAttributes()->getAttributesByCode();
        $attributes = $this->getIndex()->getData('attributes');
        if ($attributes) {
            $attributes = array_merge(array(array('attribute' => self::PRIMARY)), $attributes);
        }
        $queryString = '';
        $this->_extendSqlAttributes($attributes);
        foreach ($attributes as $attr) {
            $attrName = $attr['attribute'];
            if (isset($pa[$attrName])) {
                $flatColumn = $pa[$attrName]->getFlatColumns();
                if ($pa[$attrName]->getData('frontend_input')
                    && in_array($pa[$attrName]->getData('frontend_input'), array('select', 'multiselect'))
                ) {
                    $flatColumn[$attrName] = $this->_getTextColumnDefinition();
                }
                if (isset($flatColumn[$attrName])) {
                    $flatColumn = $flatColumn[$attrName];
                    $queryString .= "`{$attrName}` {$flatColumn['type']}";
                    if ($flatColumn['unsigned']) {
                        $queryString .= " unsigned";
                    }
                    $queryString .= " " . ($flatColumn['is_null'] ? 'NULL' : 'NOT NULL');
                    if ($flatColumn['default']) {
                        $queryString .= " DEFAULT " . $this->_getConnection()->quoteInto('?', $flatColumn['default']);
                    }
                    if ($flatColumn['extra']) {
                        $queryString .= ' ' . $flatColumn['extra'];
                    }
                    if ($attr != $attributes[count($attributes) - 1]) {
                        $queryString .= ",\n";
                    }
                }
            } else {
                if (isset($attr['type'])) {
                    $queryString .= "`{$attrName}` {$attr['type']}";
                    if ($attr['unsigned']) {
                        $queryString .= " unsigned";
                    }
                    $queryString .= " " . ($attr['is_null'] ? 'NULL' : 'NOT NULL');
                    if ($attr['default']) {
                        $queryString .= " DEFAULT " . $this->_getConnection()->quoteInto('?', $attr['default']);
                    }
                    if ($attr['extra']) {
                        $queryString .= ' ' . $attr['extra'];
                    }
                    if ($attr != $attributes[count($attributes) - 1]) {
                        $queryString .= ",\n";
                    }
                } else {
                    return Mage::helper('awadvancedsearch')->__('Can\'t use attribute "%s"', $attrName);
                }
            }
        }
        return parent::_createTable($queryString);
    }

    /**
     * @return AW_Advancedsearch_Model_Product_Collection
     */
    protected function _prepareCollection()
    {
        /** @var AW_Advancedsearch_Model_Product_Collection $collection */
        $collection = Mage::getModel('awadvancedsearch/product_collection');
        return $collection->addAttributeToSelect($this->_getAttributes());
    }

    protected function _fillDataFromCollection($collection, $checkRecordsExist = false)
    {
        $table = $this->getIndexTableModel();
        $addAttributes = $this->_getAttributes();
        $attCount = count($addAttributes);
        $stores = $this->getIndex()->getStore();
        $systemStoreIds = array_values($stores);
        if (in_array(0, $stores)) {
            $systemStoreIds = array_keys(Mage::app()->getStores());
        }
        $maxStoreId = max($systemStoreIds);
        foreach ($collection as $item) {
            $storeIdsForIndexing = array_intersect($item->getStoreIds(), $stores);
            if (in_array(0, $stores) || $storeIdsForIndexing) {
                foreach ($systemStoreIds as $storeId) {
                    //make unique id range = ((i + (i - 1) * n) ... i + i*n)
                    //where i = product_id, n = range length (max from storeIds)
                    $productId = $item->getData(self::PRIMARY);
                    $primaryKey = ($productId + ($productId - 1) * $maxStoreId) + $storeId;
                    $data = array(self::PRIMARY => $primaryKey);
                    $data['_product_id'] = $productId;
                    $data['_store_id'] = $storeId;
                    for ($j = 0; $j < $attCount; $j++) {
                        $attrText = $item->getResource()->getAttributeRawValue(
                            $item->getId(), $addAttributes[$j], $storeId
                        );
                        if (in_array($item->getResource()->getAttribute($addAttributes[$j])->getFrontendInput(), array('multiselect', 'select'))) {
                            $options = $item->getResource()->getAttribute($addAttributes[$j])->getSource()->getAllOptions();
                            foreach ($options as $option) {
                                if ($option['value'] == $attrText) {
                                    $attrText = $option['label'];
                                    break;
                                }
                            }
                        }
                        if ($attrText) {
                            if (is_array($attrText)) {
                                $attrText = implode(' ', $attrText);
                            }
                            $data[$addAttributes[$j]] = strip_tags($attrText);
                        } else {
                            $data[$addAttributes[$j]] = strip_tags($item->getData($addAttributes[$j]));
                        }
                    }
                    if ($checkRecordsExist) {
                        $tableItem = $table->fetchRow(
                            $table->select()->where(self::PRIMARY . ' = ?', $item[self::PRIMARY])
                        );
                        if ($tableItem) {
                            $tableItem->setFromArray($data);
                            $tableItem->_updated = 1;
                            $tableItem->save();
                            continue;
                        } else {
                            $data['_updated'] = 1;
                        }
                    }
                    $table->insert($data);
                }
            }
        }
    }

    protected function _getPrimary()
    {
        return self::PRIMARY;
    }

    protected function _getPrimaryTable()
    {
        return '';
    }
}
