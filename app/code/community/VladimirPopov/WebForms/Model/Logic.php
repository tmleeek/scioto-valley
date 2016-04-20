<?php
class VladimirPopov_WebForms_Model_Logic
    extends VladimirPopov_WebForms_Model_Abstract
{
    const VISIBILITY_HIDDEN = 'hidden';
    const VISIBILITY_VISIBLE = 'visible';

    public function _construct()
    {
        parent::_construct();
        $this->_init('webforms/logic');
    }

    public function ruleCheck($data)
    {
        $flag = false;
        $input = "";
        if (!empty($data[$this->getFieldId()]))
            $input = $data[$this->getFieldId()];
        if (!is_array($input)) $input = array($input);
        if (
            $this->getAggregation() == VladimirPopov_WebForms_Model_Logic_Aggregation::AGGREGATION_ANY ||
            (
                $this->getAggregation() == VladimirPopov_WebForms_Model_Logic_Aggregation::AGGREGATION_ALL &&
                $this->getLogicCondition() == VladimirPopov_WebForms_Model_Logic_Condition::CONDITION_NOTEQUAL
            )
        ) {
            if ($this->getLogicCondition() == VladimirPopov_WebForms_Model_Logic_Condition::CONDITION_EQUAL) {
                foreach ($input as $input_value) {
                    if (in_array($input_value, $this->getFrontendValue($input_value)))
                        $flag = true;
                }
            } else {
                $flag = true;
                foreach ($input as $input_value) {
                    if (in_array($input_value, $this->getFrontendValue($input_value))) $flag = false;
                }
                if (!count($input)) $flag = false;
            }
        } else {
            $flag = true;
            foreach ($this->getFrontendValue($input_value) as $trigger_value) {
                if (!in_array($trigger_value, $input)) {
                    $flag = false;
                }
            }
        }

        return $flag;
    }

    public function getTargetVisibility($target, $logic_rules, $data)
    {
        $flag = false;
        foreach ($logic_rules as $logic) {
            foreach ($logic->getTarget() as $t) {
                if ($target["id"] == $t) {
                    if ($logic->ruleCheck($data)) {
                        $action = $logic->getAction();
                        $flag = true;
                        break;
                    }
                }
            }
        }
        $visibility = false;
        if ($target["logic_visibility"] == self::VISIBILITY_VISIBLE)
            $visibility = true;
        if ($flag) {
            $visibility = true;
            if ($action == VladimirPopov_WebForms_Model_Logic_Action::ACTION_HIDE) {
                $visibility = false;
            }
        }
        return $visibility;
    }

    public function getFrontendValue($input_value = false)
    {
        if (Mage::app()->getStore()->isAdmin())
            return $this->getValue();
        $field = Mage::getModel('webforms/fields')->setStoreId($this->getStoreId())->load($this->getFieldId());
        if ($field->getType() == 'select/contact') {
            if($input_value && !is_numeric($input_value)) return $this->getValue();
            $return = array();
            $options = $field->getOptionsArray();
            foreach ($options as $i => $option) {
                foreach ($this->getValue() as $trigger) {
                    $contact = $field->getContactArray($option['value']);
                    $trigger_contact = $field->getContactArray($trigger);
                    if ($contact == $trigger_contact) {
                        $value = $option["value"];
                        if ($option['null']) {
                            $value = '';
                        }
                        if ($contact['email']) $return[] = $i;
                        else $return[] = $value;
                    }
                }
            }
            return $return;
        }
        return $this->getValue();
    }
}