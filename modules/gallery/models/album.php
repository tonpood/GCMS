<?php
/**
 * @filesource gallery/models/album.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gallery\Album;

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
   * ลิสต์รายการอัลบัม
   *
   * @param Request $request
   * @param object $index
   * @return object
   */
  public static function get(Request $request, $index)
  {
    $model = new static;
    // query
    $query = $model->db()->createQuery()
      ->from('gallery_album A')
      ->where(array(
      array('A.module_id', $index->module_id)
    ));
    // จำนวน
    $index->total = $query->cacheOn()->count();
    // ข้อมูลแบ่งหน้า
    $list_per_page = $index->rows * $index->cols;
    $index->page = $request->request('page')->toInt();
    $index->totalpage = ceil($index->total / $list_per_page);
    $index->page = max(1, ($index->page > $index->totalpage ? $index->totalpage : $index->page));
    $index->start = $list_per_page * ($index->page - 1);
    // รายการที่แสดง
    $q1 = $model->db()->createQuery()
      ->select('G.image')
      ->from('gallery G')
      ->where(array(array('G.album_id', 'A.id'), array('G.module_id', 'A.module_id')))
      ->order('count')
      ->limit(1);
    // เรียงลำดับ
    $sorts = array('id DESC', 'last_update DESC', 'RAND()');
    $query->select('A.*', array($q1, 'image'))
      ->order($sorts[$index->sort])
      ->limit($list_per_page, $index->start);
    $index->items = $query->cacheOn()->execute();
    return $index;
  }
}