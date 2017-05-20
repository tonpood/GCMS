<?php
/**
 * @filesource download/controllers/admin/init.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Download\Admin\Init;

use \Gcms\Gcms;

/**
 * จัดการการตั้งค่าเริ่มต้น
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * ฟังก์ชั่นเรียกโดย admin
   *
   * @param array $modules
   */
  public static function init($modules)
  {
    if (!empty($modules)) {
      // เมนู
      foreach ($modules as $item) {
        Gcms::$menu->menus['modules'][$item->module]['write'] = '<a href="index.php?module=download-write&amp;mid='.$item->id.'"><span>{LNG_Add New} {LNG_Download file}</span></a>';
        Gcms::$menu->menus['modules'][$item->module]['setup'] = '<a href="index.php?module=download-setup&amp;mid='.$item->id.'"><span>{LNG_List of} {LNG_Download file}</span></a>';
      }
    }
  }

  /**
   * คำอธิบายเกี่ยวกับโมดูล ถ้าไม่มีฟังก์ชั่นนี้ โมดูลนี้จะไม่สามารถใช้ซ้ำได้
   */
  public static function description()
  {
    return '{LNG_Module} {LNG_Download file}';
  }
}