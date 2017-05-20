<?php
/**
 * @filesource video/models/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Video\Index;

use \Kotchasan\Http\Request;

/**
 * อ่านข้อมูลโมดูล
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลโมดูล
   *
   * @param Request $request
   * @param Object $index
   * @return Object
   */
  public static function getItems(Request $request, $index)
  {
    // Model
    $model = new static;
    $query = $model->db()->createQuery()
      ->from('video')
      ->where(array('module_id', (int)$index->module_id));
    // จำนวน
    $index->total = $query->cacheOn()->count();
    // ข้อมูลแบ่งหน้า
    if (empty($index->list_per_page)) {
      $index->list_per_page = 20;
    }
    $index->page = $request->request('page')->toInt();
    $index->totalpage = ceil($index->total / $index->list_per_page);
    $index->page = max(1, ($index->page > $index->totalpage ? $index->totalpage : $index->page));
    $index->start = $index->list_per_page * ($index->page - 1);
    // query
    $query->select('id', 'youtube', 'topic', 'description', 'views', 'last_update')
      ->order('last_update DESC')
      ->limit($index->list_per_page, $index->start);
    $index->items = $query->cacheOn()->execute();
    // คืนค่า
    return $index;
  }
}