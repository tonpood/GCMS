<?php
/**
 * @filesource modules/edocument/models/admin/setup.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Edocument\Admin\Setup;

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
  protected $table = 'edocument A';

  /**
   * query หน้าเพจ เรียงลำดับตาม module,language
   *
   * @return array
   */
  public function getConfig()
  {
    return array(
      'select' => array(
        'A.id',
        'A.document_no',
        'A.topic',
        'A.ext',
        'A.detail',
        array($this->db()->createQuery()->select('email')->from('user U')->where(array('U.id', 'A.sender_id')), 'sender'),
        'A.size',
        'A.last_update',
        'A.downloads',
        'A.file',
        'A.module_id',
        'A.sender_id'
      ),
    );
  }

  /**
   * รับค่าจาก action ของ table
   */
  public static function action()
  {
    $ret = array();
    // session, referer, admin
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isMember()) {
      if ($login['email'] == 'demo' || !empty($login['fb'])) {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ POST
        $id = self::$request->post('id')->toString();
        $action = self::$request->post('action')->toString();
        $index = \Index\Adminmodule\Model::get('edocument', self::$request->post('mid')->toInt());
        if ($index && Gcms::canConfig($login, $index, 'can_upload') && preg_match('/^[0-9,]+$/', $id)) {
          $module_id = (int)$index->module_id;
          // Model
          $model = new \Kotchasan\Model;
          if ($action === 'delete') {
            // ลบ
            $id = explode(',', $id);
            $query = $model->db()->createQuery()->select('file')->from('edocument')->where(array(array('id', $id), array('module_id', $module_id)))->toArray();
            foreach ($query->execute() as $item) {
              // ลบไฟล์
              @unlink(ROOT_PATH.$item['file']);
            }
            // ลบข้อมูล
            $model->db()->createQuery()->delete('edocument', array(array('id', $id), array('module_id', $module_id)))->execute();
            // reload
            $ret['location'] = 'reload';
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
