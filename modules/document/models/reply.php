<?php
/**
 * @filesource document/models/reply.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Document\Reply;

use \Kotchasan\Http\Request;
use \Kotchasan\ArrayTool;
use \Gcms\Login;
use \Gcms\Gcms;
use \Kotchasan\Language;
use \Kotchasan\Validator;

/**
 *  Model สำหรับบันทึกความคิดเห็น
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * บันทึกความคิดเห็น
   *
   * @param Request $request
   */
  public function save(Request $request)
  {
    $ret = array();
    // session, token
    if ($request->initSession() && $request->isSafe()) {
      // login
      $login = Login::isMember();
      if ($login && $login['email'] == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // ค่าที่ส่งมา
        $email = $request->post('reply_email')->topic();
        $password = $request->post('reply_password')->topic();
        $post = array(
          'detail' => $request->post('reply_detail')->textarea()
        );
        $index_id = $request->post('index_id')->toInt();
        $id = $request->post('reply_id')->toInt();
        // ตรวจสอบค่าที่ส่งมา
        $index = $this->get($id, $request->post('module_id')->toInt(), $index_id);
        if ($index && $index->canReply) {
          // ผู้ดูแล
          $moderator = Gcms::canConfig($login, $index, 'moderator');
          // login ใช้ email และ password ของคน login
          if ($login) {
            $email = $login['email'];
            $password = $login['password'];
          }
          // true = guest โพสต์ได้
          $guest = in_array(-1, $index->can_reply);
          if ($post['detail'] == '') {
            // ไม่ได้กรอกรายละเอียด
            $ret['ret_reply_detail'] = Language::get('Please fill in').' '.Language::get('Detail');
          } elseif ($id == 0) {
            // ใหม่
            if ($email == '') {
              // ไม่ได้กรอกอีเมล์
              $ret['ret_reply_email'] = Language::get('Please fill in').' '.Language::get('Email');
            } elseif ($password == '' && !$guest) {
              // สมาชิกเท่านั้น และ ไม่ได้กรอกรหัสผ่าน
              $ret['ret_reply_password'] = Language::get('Please fill in').' '.Language::get('Password');
            } elseif ($email != '' && $password != '') {
              $user = Login::checkMember($email, $password);
              if (is_string($user)) {
                if (Login::$login_input == 'password') {
                  $ret['ret_reply_password'] = $user;
                } else {
                  $ret['ret_reply_email'] = $user;
                }
              } elseif (!in_array($user['status'], $index->can_reply)) {
                // ไม่สามารถแสดงความคิดเห็นได้
                $ret['alert'] = Language::get('Sorry, you do not have permission to comment');
              } else {
                // สมาชิก สามารถแสดงความคิดเห็นได้
                $post['member_id'] = $user['id'];
                $post['email'] = $user['email'];
                $post['sender'] = empty($user['displayname']) ? $user['email'] : $user['displayname'];
              }
            } elseif ($guest) {
              // ตรวจสอบอีเมล์ซ้ำกับสมาชิก สำหรับบุคคลทั่วไป
              $search = $this->db()->createQuery()
                ->from('user')
                ->where(array('email', $email))
                ->first('id');
              if ($search) {
                // พบอีเมล์ ต้องการ password
                $ret['ret_reply_password'] = Language::get('Please fill in').' '.Language::get('Password');
              } elseif (!Validator::email($email)) {
                // อีเมล์ไม่ถูกต้อง
                $ret['ret_reply_email'] = str_replace(':name', Language::get('Email'), Language::get('Invalid :name'));
              } else {
                // guest
                $post['member_id'] = 0;
                $post['email'] = $email;
              }
            } else {
              // สมาชิกเท่านั้น
              $ret['alert'] = Language::get('Members Only');
            }
          } elseif (!($index->member_id == $login['id'] || $moderator)) {
            // แก้ไข ไม่ใช่เจ้าของ และ ไม่ใช่ผู้ดูแล
            $ret['alert'] = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
          }
          if ($id == 0 && empty($ret) && $post['detail'] != '') {
            // ตรวจสอบโพสต์ซ้ำภายใน 1 วัน
            $search = $this->db()->createQuery()
              ->from('comment')
              ->where(array(
                array('detail', $post['detail']),
                array('email', $post['email']),
                array('module_id', $index->module_id),
                array('last_update', '>', time() - 86400),
              ))
              ->first('id');
            if ($search) {
              $ret['alert'] = Language::get('Your post is already exists. You do not need to post this.');
            }
          }
          // เวลาปัจจุบัน
          $mktime = time();
          if (empty($ret)) {
            $post['last_update'] = $mktime;
            if ($id > 0) {
              // แก้ไข
              $this->db()->update($this->getTableName('comment'), $id, $post);
              // คืนค่า
              $ret['alert'] = Language::get('Edit comment successfully');
            } else {
              // ใหม่
              $post['ip'] = $request->getClientIp();
              $post['index_id'] = $index->id;
              $post['module_id'] = $index->module_id;
              $id = $this->db()->insert($this->getTableName('comment'), $post);
              // อัปเดทคำถาม
              $q['commentator'] = empty($post['sender']) ? $post['email'] : $post['sender'];
              $q['commentator_id'] = $post['member_id'];
              $q['comments'] = $index->comments + 1;
              $q['comment_id'] = $id;
              // อัปเดทสมาชิก
              if ($post['member_id'] > 0) {
                $this->db()->createQuery()->update('user')->set('`reply`=`reply`+1')->where($post['member_id'])->execute();
              }
              if ($index->category_id > 0) {
                // อัปเดทจำนวนเรื่อง และ ความคิดเห็น ในหมวด
                \Document\Admin\Write\Model::updateCategories((int)$index->module_id);
              }
              // คืนค่า
              $ret['alert'] = Language::get('Thank you for your comment');
            }
            // เคลียร์
            $request->removeToken();
            // reload
            $location = WEB_URL.'index.php?module='.$index->module.'&id='.$index_id.'&visited='.$mktime;
            $location .= self::$cfg->use_ajax == 1 ? "&to=R_$id" : "#R_$id";
            $ret['location'] = $location;
          }
        } else {
          // ไม่พบรายการที่ต้องการ หรือไม่สามารถโพสต์ได้
          $ret['alert'] = Language::get('Can not be performed this request. Because they do not find the information you need or you are not allowed');
        }
      }
    }
    if ($ret) {
      // คืนค่าเป็น JSON
      echo json_encode($ret);
    }
  }

  /**
   * อ่านข้อมูล ความคิดเห็น
   *
   * @param int $id ID ของความคิดเห็น, 0 ถ้าเป็นความคิดเห็นใหม่
   * @param int $module_id ID ของโมดูล
   * @param int $index_id ID ของคำถาม
   * @return object|bool คืนค่าผลลัพท์ที่พบ (Object) ไม่พบข้อมูลคืนค่า false
   */
  private function get($id, $module_id, $index_id)
  {
    if ($id > 0) {
      // แก้ไข
      $index = $this->db()->createQuery()
        ->from('comment R')
        ->join('index Q', 'INNER', array(array('Q.id', 'R.index_id'), array('Q.index', 0)))
        ->join('modules M', 'INNER', array('M.id', 'Q.module_id'))
        ->join('category C', 'LEFT', array(array('C.module_id', 'Q.module_id'), array('C.category_id', 'Q.category_id')))
        ->where(array(array('R.id', $id), array('R.index_id', $index_id), array('R.module_id', $module_id)))
        ->toArray()
        ->cacheOn()
        ->first('R.member_id', 'Q.id', 'Q.comments', 'Q.module_id', 'Q.can_reply canReply', 'Q.alias', 'M.module', 'M.config mconfig', 'C.config', 'C.category_id');
    } else {
      // ใหม่
      $index = $this->db()->createQuery()
        ->from('index Q')
        ->join('modules M', 'INNER', array('M.id', 'Q.module_id'))
        ->join('category C', 'LEFT', array(array('C.module_id', 'Q.module_id'), array('C.category_id', 'Q.category_id')))
        ->where(array(array('Q.id', $index_id), array('Q.module_id', $module_id)))
        ->toArray()
        ->cacheOn()
        ->first('Q.id', 'Q.comments', 'Q.module_id', 'Q.can_reply canReply', 'Q.alias', 'M.module', 'M.config mconfig', 'C.config', 'C.category_id');
    }
    if ($index) {
      // config จากโมดูล
      $index = ArrayTool::unserialize($index['mconfig'], $index);
      // config จากหมวด แทนที่ config จากโมดูล
      if (!empty($index['config'])) {
        $datas = @unserialize($index['config']);
        if (is_array($datas)) {
          foreach ($datas as $key => $value) {
            if ($key == 'can_reply') {
              // ไม่แสดงความคิดเห็นถ้าหมวดไม่สมารถแสดงได้
              if ($value == 0) {
                $index['can_reply'] = array();
              }
            } else {
              $index[$key] = $value;
            }
          }
        }
      }
      unset($index['mconfig']);
      unset($index['config']);
      return (object)$index;
    }
    return false;
  }
}