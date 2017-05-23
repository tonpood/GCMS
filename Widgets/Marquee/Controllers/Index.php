<?php
/**
 * @filesource Widgets/Marquee/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Marquee\Controllers;

/**
 * Controller หลัก สำหรับแสดงผล Widget
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Index extends \Kotchasan\Controller
{

  /**
   * แสดงผล Widget
   *
   * @param array $query_string ข้อมูลที่ส่งมาจากการเรียก Widget
   * @return string
   */
  public function get($query_string)
  {
    foreach (self::$cfg->marquee as $key => $value) {
      if (!isset($query_string[$key])) {
        $query_string[$key] = $value;
      }
    }
    return \Widgets\Marquee\Views\Index::render($query_string);
  }
}