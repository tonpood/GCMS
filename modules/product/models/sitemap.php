<?php
/**
 * @filesource product/models/sitemap.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Product\Sitemap;

/**
 * บทความทั้งหมด
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * บทความทั้งหมด
   *
   * @param array $ids แอเรย์ของ module_id
   * @param string $date วันที่วันนี้
   * @return array
   */
  public static function getStories($ids, $date)
  {
    $model = new static;
    return $model->db()->createQuery()
        ->select('id', 'module_id', 'alias', 'last_update')
        ->from('product')
        ->where(array(array('module_id', $ids), array('published', 1)))
        ->cacheOn()
        ->execute();
  }
}