<?php
/**
 * @filesource modules/index/models/download.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Download;

/**
 * โมเดลสำหรับใช้งานร่วมกับฐานข้อมูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * เมธอดอ่านข้อมูลที่ต้องการดาวน์โหลดจากฐานข้อมูล
   *
   * @param int $id
   * @return object|bool คืนค่า object ของข้อมูล ไม่พบคืนค่า false
   */
  public static function get($id)
  {
    // create Model
    $model = new static;
    // SELECT * FROM `download` WHERE `id`=$id LIMIT 1
    return $model->db()->first($model->getTableName('download'), (int)$id);
  }
}