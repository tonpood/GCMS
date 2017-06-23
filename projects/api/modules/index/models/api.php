<?php
/**
 * @filesource index/models/api.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Api;

use \Kotchasan\Http\Request;

/**
 * API Model
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model
{

  /**
   * ฟังก์ชั่นแปลง id เป็นเวลา
   *
   * @param Request $request
   * @return string
   */
  public static function getTime(Request $request)
  {
    return \Kotchasan\Date::format($request->get('id')->toInt());
  }
}