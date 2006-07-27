<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  class osC_OrderTotal_total extends osC_OrderTotal {
    var $output;

    var $_title,
        $_code = 'total',
        $_status = false,
        $_sort_order;

    function osC_OrderTotal_total() {
      global $osC_Language;

      $this->output = array();

      $this->_title = $osC_Language->get('order_total_total_title');
      $this->_description = $osC_Language->get('order_total_total_description');
      $this->_status = (defined('MODULE_ORDER_TOTAL_TOTAL_STATUS') && (MODULE_ORDER_TOTAL_TOTAL_STATUS == 'true') ? true : false);
      $this->_sort_order = (defined('MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER') ? MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER : null);
    }

    function process() {
      global $osC_ShoppingCart, $osC_Currencies;

      $this->output[] = array('title' => $this->_title . ':',
                              'text' => '<b>' . $osC_Currencies->format($osC_ShoppingCart->getTotal()) . '</b>',
                              'value' => $osC_ShoppingCart->getTotal());
    }

    function check() {
      if (!isset($this->_check)) {
        $this->_check = defined('MODULE_ORDER_TOTAL_TOTAL_STATUS');
      }

      return $this->_check;
    }

    function install() {
      global $osC_Database;

      parent::install();

      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Total', 'MODULE_ORDER_TOTAL_TOTAL_STATUS', 'true', 'Do you want to display the total order value?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      $osC_Database->simpleQuery("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER', '4', 'Sort order of display.', '6', '2', now())");
    }

    function getKeys() {
      if (!isset($this->_keys)) {
        $this->_keys = array('MODULE_ORDER_TOTAL_TOTAL_STATUS',
                             'MODULE_ORDER_TOTAL_TOTAL_SORT_ORDER');
      }

      return $this->_keys;
    }
  }
?>