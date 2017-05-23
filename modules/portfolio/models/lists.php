<?php
/**
 * @filesource portfolio/models/lists.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Portfolio\Lists;

use \Kotchasan\Http\Request;

/**
 * โมเดลสำหรับแสดงรายการอัลบัม
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * ลิสต์รายการ
   *
   * @param Request $request
   * @param object $index
   * @return object
   */
  public static function get(Request $request, $index)
  {
    $where = array(
      array('module_id', $index->module_id),
      array('published', '1')
    );
    // แสดงตาม tag
    $index->tag = $request->request('tag')->topic();
    if (mb_strlen($index->tag) > 1) {
      $where[] = array('keywords', 'LIKE', '%'.$index->tag.'%');
    }
    $model = new static;
    // query
    $query = $model->db()->createQuery()
      ->from('portfolio')
      ->where($where);
    // จำนวน
    $index->total = $query->cacheOn()->count();
    // ข้อมูลแบ่งหน้า
    $list_per_page = $index->rows * $index->cols;
    $index->page = $request->request('page')->toInt();
    $index->totalpage = ceil($index->total / $list_per_page);
    $index->page = max(1, ($index->page > $index->totalpage ? $index->totalpage : $index->page));
    $index->start = $list_per_page * ($index->page - 1);
    // query
    $query->select()
      ->order('create_date DESC')
      ->limit($list_per_page, $index->start);
    $index->items = $query->cacheOn()->execute();
    // คืนค่า
    return $index;
  }
}