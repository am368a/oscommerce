<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  require('../includes/classes/image.php');

  class osC_Image_Admin extends osC_Image {

// Private variables

    var $_title, $_header, $_data = array();

    var $_has_parameters = false;

// Class constructor

    function osC_Image_Admin() {
      parent::osC_Image();
    }

// Public methods

    function &getGroups() {
      return $this->_groups;
    }

    function resize($image, $group_id) {
      if (!file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $this->_groups[$group_id]['code'])) {
        mkdir(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $this->_groups[$group_id]['code']);
        @chmod(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $this->_groups[$group_id]['code'], 0777);
      }

      exec(escapeshellarg(CFG_APP_IMAGEMAGICK_CONVERT) . ' -resize ' . (int)$this->_groups[$group_id]['size_width'] . 'x' . (int)$this->_groups[$group_id]['size_height'] . (($this->_groups[$group_id]['force_size']) == '1' ? '!' : '') . ' ' . escapeshellarg(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $this->_groups[1]['code'] . '/' . $image) . ' ' . escapeshellarg(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $this->_groups[$group_id]['code'] . '/' . $image));
      @chmod(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $this->_groups[$group_id]['code'] . '/' . $image, 0777);
    }

    function getModuleCode() {
      return $this->_code;
    }

    function &getTitle() {
      return $this->_title;
    }

    function &getHeader() {
      return $this->_header;
    }

    function &getData() {
      return $this->_data;
    }

    function activate() {
      $this->_setHeader();
      $this->_setData();
    }

    function hasParameters() {
      return $this->_has_parameters;
    }

    function existsInGroup($id, $group_id) {
      global $osC_Database;

      $Qimage = $osC_Database->query('select image from :table_products_images where id = :id');
      $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qimage->bindInt(':id', $id);
      $Qimage->execute();

      return file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $this->_groups[$group_id]['code'] . '/' . $Qimage->value('image'));
    }

    function delete($id) {
      global $osC_Database;

      $Qimage = $osC_Database->query('select image from :table_products_images where id = :id');
      $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qimage->bindInt(':id', $id);
      $Qimage->execute();

      foreach ($this->_groups as $group) {
        @unlink(DIR_FS_CATALOG . DIR_WS_IMAGES . 'products/' . $group['code'] . '/' . $Qimage->value('image'));
      }

      $Qdel = $osC_Database->query('delete from :table_products_images where id = :id');
      $Qdel->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qdel->bindInt(':id', $id);
      $Qdel->execute();

      return ($Qdel->affectedRows() === 1);
    }

    function setAsDefault($id) {
      global $osC_Database;

      $Qimage = $osC_Database->query('select products_id from :table_products_images where id = :id');
      $Qimage->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
      $Qimage->bindInt(':id', $id);
      $Qimage->execute();

      if ($Qimage->numberOfRows() === 1) {
        $Qupdate = $osC_Database->query('update :table_products_images set default_flag = :default_flag where products_id = :products_id and default_flag = :default_flag');
        $Qupdate->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qupdate->bindInt(':default_flag', 0);
        $Qupdate->bindInt(':products_id', $Qimage->valueInt('products_id'));
        $Qupdate->bindInt(':default_flag', 1);
        $Qupdate->execute();

        $Qupdate = $osC_Database->query('update :table_products_images set default_flag = :default_flag where id = :id');
        $Qupdate->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qupdate->bindInt(':default_flag', 1);
        $Qupdate->bindInt(':id', $id);
        $Qupdate->execute();

        return ($Qupdate->affectedRows() === 1);
      }
    }

    function reorderImages($images_array) {
      global $osC_Database;

      $counter = 0;

      foreach ($images_array as $id) {
        $counter++;

        $Qupdate = $osC_Database->query('update :table_products_images set sort_order = :sort_order where id = :id');
        $Qupdate->bindTable(':table_products_images', TABLE_PRODUCTS_IMAGES);
        $Qupdate->bindInt(':sort_order', $counter);
        $Qupdate->bindInt(':id', $id);
        $Qupdate->execute();
      }

      return ($counter > 0);
    }
  }
?>