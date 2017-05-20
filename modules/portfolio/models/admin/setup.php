<?php
/**
 * @filesource portfolio/models/admin/setup.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Portfolio\Admin\Setup;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 * โมเดลสำหรับแสดงรายการบทความ (setup.php)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Orm\Field
{
  /**
   * ชื่อตาราง
   *
   * @var string
   */
  protected $table = 'portfolio P';

  /**
   * รับค่าจาก action ของ table
   */
  public static function action()
  {
    $ret = array();
    // referer, session, admin
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isMember()) {
      if ($login['email'] == 'demo' || !empty($login['fb'])) {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ POST
        $id = self::$request->post('id')->toString();
        $action = self::$request->post('action')->toString();
        $index = \Index\Adminmodule\Model::get('portfolio', self::$request->post('mid')->toInt());
        if ($index && Gcms::canConfig($login, $index, 'can_write')) {
          // Model
          $model = new \Kotchasan\Model;
          if ($action === 'published') {
            // สถานะการเผยแพร่
            $table_index = $model->getTableName('index');
            $search = $model->db()->first($table_index, array(array('id', (int)$id), array('module_id', (int)$index->module_id)));
            if ($search) {
              $published = $search->published == 1 ? 0 : 1;
              $model->db()->update($table_index, $search->id, array('published' => $published));
              // คืนค่า
              $ret['elem'] = 'published_'.$search->id;
              $lng = Language::get('PUBLISHEDS');
              $ret['title'] = $lng[$published];
              $ret['class'] = 'icon-published'.$published;
            }
          } elseif ($action === 'delete' && preg_match('/^[0-9,]+$/', $id)) {
            // ลบรายการที่เลือก
            $query = $model->db()->createQuery()->select('id', 'picture')->from('index')->where(array(array('id', explode(',', $id)), array('module_id', (int)$index->module_id)))->toArray();
            $id = array();
            foreach ($query->execute() as $item) {
              // ลบรูปภาพ
              if (!empty($item['picture'])) {
                @unlink(ROOT_PATH.DATA_FOLDER.'portfolio/'.$item['picture']);
              }
              $id[] = $item['id'];
            }
            if (!empty($id)) {
              // ลบฐานข้อมูล
              $model->db()->createQuery()->delete('index', array(array('id', $id), array('module_id', (int)$index->module_id)))->execute();
              $model->db()->createQuery()->delete('index_detail', array(array('id', $id), array('module_id', (int)$index->module_id)))->execute();
              $model->db()->createQuery()->delete('comment', array(array('index_id', $id), array('module_id', (int)$index->module_id)))->execute();
              // อัปเดทจำนวนเรื่อง และ ความคิดเห็น ในหมวด
              \Portfolio\Admin\Write\Model::updateCategories((int)$index->module_id);
              // คืนค่า
              $ret['location'] = 'reload';
            }
          } elseif ($action === 'can_reply') {
            // การแสดงความคิดเห็น
            $table_index = $model->getTableName('index');
            $search = $model->db()->first($table_index, array(array('id', (int)$id), array('module_id', (int)$index->module_id)));
            if ($search) {
              $can_reply = $search->can_reply == 1 ? 0 : 1;
              $model->db()->update($table_index, $search->id, array('can_reply' => $can_reply));
              // คืนค่า
              $ret['elem'] = 'can_reply_'.$search->id;
              $lng = Language::get('REPLIES');
              $ret['title'] = $lng[$can_reply];
              $ret['class'] = 'icon-reply reply'.$can_reply;
            }
          }
        } else {
          $ret['alert'] = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    if (!empty($ret)) {
      // คืนค่าเป็น JSON
      echo json_encode($ret);
    }
  }
}