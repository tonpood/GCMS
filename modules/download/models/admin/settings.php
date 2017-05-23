<?php
/**
 * @filesource download/models/admin/settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Download\Admin\Settings;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Language;
use \Gcms\Gcms;

/**
 *  บันทึกการตั้งค่า
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * ค่าติดตั้งเริ่มต้น
   *
   * @return array
   */
  public static function defaultSettings()
  {
    return array(
      'file_typies' => array('doc', 'ppt', 'pptx', 'docx', 'rar', 'zip', 'jpg', 'pdf'),
      'upload_size' => 2097152,
      'list_per_page' => 20,
      'sort' => 1,
      'can_download' => array(-1, 0, 1),
      'moderator' => array(1),
      'can_upload' => array(1),
      'can_config' => array(1)
    );
  }

  /**
   * บันทึกข้อมูล config ของโมดูล
   *
   * @param Request $request
   */
  public function save(Request $request)
  {
    $ret = array();
    // referer, session, member
    if ($request->initSession() && $request->isReferer() && $login = Login::isMember()) {
      if ($login['email'] == 'demo' || !empty($login['fb'])) {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ POST
        $typies = array();
        foreach (explode(',', strtolower($request->post('file_typies')->filter('a-zA-Z0-9,'))) as $typ) {
          if ($typ != '') {
            $typies[$typ] = $typ;
          }
        }
        $save = array(
          'file_typies' => array_keys($typies),
          'upload_size' => $request->post('upload_size')->toInt(),
          'list_per_page' => $request->post('list_per_page')->toInt(),
          'sort' => $request->post('sort')->toInt(),
          'can_download' => $request->post('can_download', array())->toInt(),
          'can_upload' => $request->post('can_upload', array())->toInt(),
          'moderator' => $request->post('moderator', array())->toInt(),
          'can_config' => $request->post('can_config', array())->toInt(),
        );
        // โมดูลที่เรียก
        $index = \Index\Adminmodule\Model::get('download', $request->post('id')->toInt());
        // สามารถตั้งค่าได้
        if ($index && Gcms::canConfig($login, $index, 'can_config')) {
          if (empty($save['file_typies'])) {
            // คืนค่า input ที่ error
            $ret['ret_file_typies'] = 'this';
          } else {
            $save['can_upload'][] = 1;
            $save['moderator'][] = 1;
            $save['can_config'][] = 1;
            $this->db()->createQuery()->update('modules')->set(array('config' => serialize($save)))->where($index->module_id)->execute();
            // คืนค่า
            $ret['alert'] = Language::get('Saved successfully');
            $ret['location'] = $request->getUri()->postBack('index.php', array('module' => 'download-settings', 'mid' => $index->module_id));
          }
        } else {
          $ret['alert'] = Language::get('Unable to complete the transaction');
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}