<?php
/**
 * FME Jobs extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   FME 
 * @package    Jobs
 * @author     Kaleem Ullah Khan <kaleem.ullah@unitedsol.net>
 * @copyright  Copyright 2011 � free-magentoextensions.com All right reserved
 */
?>
<?php
class FME_Jobs_Block_Adminhtml_Renderer_Cvrenderer extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    public function render(Varien_Object $row)
    {
        
        $cvfile = $row['cvfile'];
        
        $path = Mage::getBaseUrl('media') ."jobs". DS. "cv". DS;
        $linkstr = '<a href="'.$path.''.$row["cvfile"].'" >'.$row["cvfile"].'</a>';
        
        
        return $linkstr;
    }


}