<?php
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
$setup->updateAttribute(Mage_Catalog_Model_Category::ENTITY, 'meta_description', 'is_wysiwyg_enabled', 1);
$setup->updateAttribute(Mage_Catalog_Model_Category::ENTITY, 'meta_description', 'is_html_allowed_on_front', 1);