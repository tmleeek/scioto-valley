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
 * @package    AW_Pquestion2
 * @version    2.0.0
 * @copyright  Copyright (c) 2010-2012 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE.txt
 */


class AW_Pquestion2_Test_Model_Resource_Summary_Question_Collection extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     * @loadFixture
     * @dataProvider dataProvider
     */
    public function addFilterByQuestionId($questionId, $expectation)
    {
        $collection = Mage::getModel('aw_pq2/summary_question')->getCollection();
        $collection->addFilterByQuestionId($questionId);
        $this->assertEquals(
            count($collection->getItems()),
            $expectation['collection_item_count'],
            'Collection item count check'
        );
    }

}