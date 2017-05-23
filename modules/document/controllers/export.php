<?php
/**
 * @filesource document/controllers/export.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Export;

use \Kotchasan\Http\Request;

/**
 * ส่งออกบทความ
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Kotchasan\Controller
{

  /**
   * ส่งออกบทความ
   *
   * @param Request $request
   * @param object $index ข้อมูลโมดูล
   * @return array
   */
  public static function init(Request $request, $index)
  {
    // พิมพ์บทความ
    return createClass('Document\Export\View')->printer($request, $index);
  }
}