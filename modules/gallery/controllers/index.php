<?php
/**
 * @filesource gallery/controllers/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gallery\Index;

use \Kotchasan\Http\Request;

/**
 * Controller หลัก สำหรับแสดง frontend ของ GCMS
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * Controller หลักของโมดูล ใช้เพื่อตรวจสอบว่าจะเรียกหน้าไหนมาแสดงผล
   *
   * @param Request $request
   * @param object $index ข้อมูลโมดูล
   * @return object
   */
  public function init(Request $request, $index)
  {
    if (MAIN_INIT === 'indexhtml') {
      // ตรวจสอบโมดูลและอ่านข้อมูลโมดูล
      $index = \Index\Module\Model::getDetails($index);
      if ($index) {
        if ($request->request('id')->exists()) {
          // ดูอัลบัม
          $page = createClass('Gallery\View\View')->index($request, $index);
        } else {
          // หน้าแสดงรายการอัลบัม
          $page = createClass('Gallery\Album\View')->index($request, $index);
        }
        if ($page) {
          return $page;
        }
      }
    }
    // 404
    return createClass('Index\PageNotFound\Controller')->init('gallery');
  }

  /**
   * ฟังก์ชั่นสร้าง URL
   *
   * @param string $module ชื่อโมดูล
   * @param int $id ID
   * @return string
   */
  public static function url($module, $id)
  {
    return \Gcms\Gcms::createUrl($module, '', 0, 0, 'id='.$id);
  }
}