<?php
/**
 * @filesource modules/document/models/admin/categorywrite.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Admin\Categorywrite;

use \Kotchasan\Http\Request;
use \Kotchasan\ArrayTool;
use \Kotchasan\Login;
use \Kotchasan\Language;
use \Gcms\Gcms;
use \Kotchasan\File;
use \Kotchasan\Database\Sql;

/**
 * อ่านข้อมูลหมวดหมู่ (Backend)
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * อ่านข้อมูลหมวดหมู่
   *
   * @param int $module_id
   * @param int $id
   * @return Object ถ้าไม่พบคืนค่า null
   */
  public static function get($module_id, $id)
  {
    if (is_int($module_id) && $module_id > 0) {
      $model = new static;
      if ($id == 0) {
        // ใหม่, ตรวจสอบโมดูลที่เรียก
        $select = array(
          '0 id',
          'M.id module_id',
          'M.module',
          'M.config mconfig',
          "'' topic",
          "'' detail",
          "'' icon",
          '1 published',
          Sql::NEXT('category_id', $model->getTableName('category'), array('module_id', 'M.id'), 'category_id'),
        );
        $index = $model->db()->createQuery()
          ->from('modules M')
          ->where(array(array('M.id', $module_id), array('M.owner', 'document')))
          ->toArray()
          ->first($select);
      } else {
        // แก้ไข ตรวจสอบโมดูลและหมวดที่เลือก
        $index = $model->db()->createQuery()
          ->from('category C')
          ->join('modules M', 'INNER', array(array('M.id', 'C.module_id'), array('M.owner', 'document')))
          ->where(array(array('C.id', $id), array('C.module_id', $module_id)))
          ->toArray()
          ->first('C.*', 'M.module', 'M.config mconfig');
      }
      if ($index) {
        // การเผยแพร่จากหมวด
        $published = $index['published'];
        // config จาก module
        $index = ArrayTool::unserialize($index['mconfig'], $index);
        unset($index['mconfig']);
        // config จากหมวด
        if (isset($index['config'])) {
          $index = ArrayTool::unserialize($index['config'], $index);
          unset($index['config']);
        }
        $index['published'] = $published;
        return (object)$index;
      }
    }
    return null;
  }

  /**
   * บันทึกหมวดหมู่
   *
   * @param Request $request
   */
  public function submit(Request $request)
  {
    $ret = array();
    // session, token, member
    if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
      if ($login['email'] == 'demo' || !empty($login['fb'])) {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ POST
        $save = array(
          'published' => $request->post('published')->toBoolean(),
          'config' => serialize(array(
            'can_reply' => $request->post('can_reply')->toBoolean()
          ))
        );
        $id = $request->post('id')->toInt();
        $module_id = $request->post('module_id')->toInt();
        $category_id = $request->post('category_id')->toInt();
        $q1 = $this->db()->createQuery()
          ->select('id')
          ->from('category')
          ->where(array(array('category_id', $category_id), array('module_id', 'M.id')));
        if ($id > 0) {
          $select = array(
            'C.id',
            'C.module_id',
            'C.icon',
            'C.config',
            'M.config mconfig',
            array($q1, 'cid')
          );
          $index = $this->db()->createQuery()
            ->from('category C')
            ->join('modules M', 'INNER', array('M.id', 'C.module_id'))
            ->where(array(array('C.id', $id), array('C.module_id', $module_id), array('M.owner', 'document')))
            ->toArray()
            ->first($select);
        } else {
          // ใหม่, ตรวจสอบโมดูลที่เรียก
          $select = array(
            'M.id module_id',
            '"" icon',
            'M.config mconfig',
            Sql::NEXT('id', $this->getTableName('category'), null, 'id'),
            array($q1, 'cid')
          );
          $index = $this->db()->createQuery()
            ->from('modules M')
            ->where(array(array('M.id', $module_id), array('M.owner', 'document')))
            ->toArray()
            ->first($select);
        }
        if ($index === false) {
          $ret['alert'] = Language::get('Sorry, Item not found It&#39;s may be deleted');
        } else {
          // config จาก module
          $index = ArrayTool::unserialize($index['mconfig'], $index);
          if (Gcms::canConfig($login, $index, 'can_config')) {
            unset($index['mconfig']);
            $topic = array();
            foreach ($request->post('topic')->topic() as $key => $value) {
              if ($value != '') {
                $topic[$key] = $value;
              }
            }
            $detail = array();
            foreach ($request->post('detail')->topic() as $key => $value) {
              if ($value != '') {
                $detail[$key] = $value;
              }
            }
            // ตรวจสอบค่าที่ส่งมา
            if ($category_id == 0) {
              $ret['ret_category_id'] = 'this';
            } elseif ($index['cid'] > 0 && $index['cid'] != $index['id']) {
              $ret['ret_category_id'] = Language::replace('This :name already exist', array(':name' => Language::get('ID')));
            } elseif (empty($topic)) {
              $ret['ret_topic_'.Language::name()] = 'Please fill in';
            } elseif (empty($detail)) {
              $ret['ret_detail_'.Language::name()] = 'Please fill in';
            } else {
              // อัปโหลดไฟล์
              $icon = ArrayTool::unserialize($index['icon']);
              foreach ($request->getUploadedFiles() as $item => $file) {
                /* @var $file \Kotchasan\Http\UploadedFile */
                if ($file->hasUploadFile()) {
                  if (!File::makeDirectory(ROOT_PATH.DATA_FOLDER.'document/')) {
                    // ไดเรคทอรี่ไม่สามารถสร้างได้
                    $ret['ret_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), DATA_FOLDER.'document/');
                  } elseif (!$file->validFileExt(array('jpg', 'gif', 'png'))) {
                    $ret['ret_'.$item] = Language::get('The type of file is invalid');
                  } else {
                    $old_icon = empty($icon[$item]) ? '' : $icon[$item];
                    $icon[$item] = "cat-$item-$index[id].".$file->getClientFileExt();
                    try {
                      $file->moveTo(ROOT_PATH.DATA_FOLDER.'document/'.$icon[$item]);
                      if ($old_icon != $icon[$item]) {
                        @unlink(ROOT_PATH.DATA_FOLDER.'document/'.$old_icon);
                      }
                    } catch (\Exception $exc) {
                      // ไม่สามารถอัปโหลดได้
                      $ret['ret_icon_'.$item] = Language::get($exc->getMessage());
                    }
                  }
                }
              }
              if (!empty($icon)) {
                $save['icon'] = Gcms::array2Ser($icon);
              }
            }
            if (empty($ret)) {
              $save['category_id'] = $category_id;
              $save['topic'] = Gcms::array2Ser($topic);
              $save['detail'] = Gcms::array2Ser($detail);
              if ($id == 0) {
                // ใหม่
                $save['module_id'] = $index['module_id'];
                $this->db()->insert($this->getTableName('category'), $save);
              } else {
                // แก้ไข
                $this->db()->update($this->getTableName('category'), $id, $save);
              }
              // อัปเดทจำนวนเรื่อง และ ความคิดเห็น ในหมวด
              \Document\Admin\Write\Model::updateCategories((int)$index['module_id']);
              // ส่งค่ากลับ
              $ret['alert'] = Language::get('Saved successfully');
              $ret['location'] = $request->getUri()->postBack('index.php', array('id' => $index['module_id'], 'module' => 'document-category'));
              // clear
              $request->removeToken();
            }
          } else {
            $ret['alert'] = Language::get('Unable to complete the transaction');
          }
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    echo json_encode($ret);
  }
}