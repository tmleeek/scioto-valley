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

class AW_Advancedsearch_Block_Result_Cms_Pages extends AW_Advancedsearch_Block_Result_Abstract
{
    const PAGER_ID = 'cms_pages_posts_pager';

    /**
     * Returns cropped page content (~100 words by default)
     *
     * @param $page
     * @return string
     */
    public function getPageContent($page)
    {
        $processor = Mage::helper('cms')->getPageTemplateProcessor();
        $content = strip_tags($processor->filter($page->getContent()));
        $content = mbereg_replace("\r\n", ' ', $content);
        $content = mbereg_replace("\n", ' ', $content);
        if (preg_match('|(^(.*?\s){100})|u', $content, $_m)) {
            if ($_m && is_array($_m) && isset($_m[0])) {
                $content = $_m[0];
                $page->setData('is_content_cropped', true);
            }
        }
        return $content;
    }

    public function getPager()
    {
        $pager = $this->getChild(self::PAGER_ID);
        if (!$pager->getCollection()) {
            $pager->setCollection($this->getResults());
        }
        return $pager->toHtml();
    }
}