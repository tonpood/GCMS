<?php
/**
 * @filesource modules/index/models/fblogin.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Fblogin;

use \Kotchasan\Http\Request;
use \Kotchasan\Language;

/**
 * Facebook Login
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  public function chklogin(Request $request)
  {
    // session, token
    if ($request->initSession() && $request->isSafe()) {
      // สุ่มรหัสผ่านใหม่
      $password = uniqid();
      // ข้อมูลที่ส่งมา
      $save = array(
        'fname' => $request->post('first_name')->topic(),
        'lname' => $request->post('last_name')->topic(),
        'email' => $request->post('email')->url(),
        'website' => str_replace(array('http://', 'https://', 'www.'), '', $request->post('link')->url()),
      );
      $fb_id = $request->post('id')->number();
      // ไม่มีอีเมล์ ใช้ id ของ Facebook
      if (empty($save['email'])) {
        $save['email'] = $fb_id;
      }
      $save['displayname'] = $save['fname'];
      // db
      $db = $this->db();
      // table
      $user_table = $this->getTableName('user');
      // ตรวจสอบสมาชิกกับ db
      $search = $db->createQuery()
        ->from('user')
        ->where(array('email', $save['email']), array('displayname', $save['displayname']), 'OR')
        ->toArray()
        ->first();
      if ($search === false) {
        // ยังไม่เคยลงทะเบียน, ลงทะเบียนใหม่
        $save['id'] = $db->getNextId($this->getTableName('user'));
        $save['fb'] = 1;
        $save['subscrib'] = 1;
        $save['visited'] = 0;
        $save['status'] = 0;
        $save['ip'] = $request->getClientIp();
        $save['password'] = md5($password.$save['email']);
        $save['lastvisited'] = time();
        $save['create_date'] = $save['lastvisited'];
        $save['icon'] = $save['id'].'.jpg';
        $save['country'] = 'TH';
        $db->insert($user_table, $save);
      } elseif ($search['fb'] == 1) {
        // facebook เคยเยี่ยมชมแล้ว อัปเดทการเยี่ยมชม
        $save = $search;
        $save['visited'] ++;
        $save['lastvisited'] = time();
        $save['ip'] = $request->getClientIp();
        $save['password'] = md5($password.$search['email']);
        $db->update($user_table, $search['id'], $save);
      } else {
        // ไม่สามารถ login ได้ เนื่องจากมี email อยู่ก่อนแล้ว
        $save = false;
        $ret['alert'] = Language::replace('This :name already exist', array(':name' => Language::get('User')));
        $ret['isMember'] = 0;
      }
      if (is_array($save)) {
        // อัปเดท icon สมาชิก
        $data = @file_get_contents('https://graph.facebook.com/'.$fb_id.'/picture');
        if ($data) {
          $f = @fopen(ROOT_PATH.self::$cfg->usericon_folder.$save['icon'], 'wb');
          if ($f) {
            fwrite($f, $data);
            fclose($f);
          }
        }
        // clear
        $request->removeToken();
        // login
        $save['password'] = $password;
        $_SESSION['login'] = $save;
        // คืนค่า
        $ret['isMember'] = 1;
        $name = trim($save['fname'].' '.$save['lname']);
        $ret['alert'] = Language::replace('Welcome %s, login complete', array('%s' => empty($name) ? $save['email'] : $name));
      }
      // คืนค่าเป็น json
      echo json_encode($ret);
    }
  }
}