<?php
/**
 * @filesource index/models/updateprofile.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Updateprofile;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\File;

/**
 * บันทึกข้อมูลสมาชิก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * บันทึก ข้อมูลสมาชิก
   */
  public function save(Request $request)
  {
    $ret = array();
    // session, token, member
    if ($request->initSession() && $request->isSafe() && $login = Login::isMember()) {
      if ($login['email'] == 'demo') {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // รับค่าจากการ POST
        $save = array();
        foreach ($request->getParsedBody() as $key => $value) {
          $k = str_replace('register_', '', $key);
          switch ($k) {
            case 'phone1':
            case 'phone2':
            case 'provinceID':
            case 'zipcode':
              $save[$k] = $request->post($key)->number();
              break;
            case 'sex':
              $save['sex'] = $request->post('register_sex')->topic();
              $save['subscrib'] = $request->post('register_subscrib')->toBoolean();
              break;
            case 'displayname':
            case 'fname':
            case 'lname':
            case 'address1':
            case 'address2':
            case 'province':
            case 'country':
              $save[$k] = $request->post($key)->topic();
              break;
            case 'website':
              $save[$k] = str_replace(array('http://', 'https://', 'ftp://'), array('', '', ''), $request->post($key)->url());
              break;
            case 'subscrib':
              $save[$k] = $request->post($key)->toBoolean();
              break;
            case 'birthday':
              $save[$k] = $request->post($key)->date();
              break;
            case 'password':
            case 'repassword':
              $$k = $request->post($key)->text();
              break;
          }
        }
        // ชื่อตาราง user
        $user_table = $this->getTableName('user');
        // database connection
        $db = $this->db();
        // ตรวจสอบค่าที่ส่งมา
        $user = $db->first($user_table, $request->post('register_id')->toInt());
        if (!$user) {
          // ไม่พบสมาชิกที่แก้ไข
          $ret['alert'] = Language::get('not a registered user');
        } else {
          // ชื่อเล่น
          if (isset($save['displayname'])) {
            if (mb_strlen($save['displayname']) < 2) {
              $ret['ret_register_displayname'] = Language::get('Name for the show on the site at least 2 characters');
            } elseif (in_array($save['displayname'], self::$cfg->member_reserv)) {
              $ret['ret_register_displayname'] = Language::get('Invalid name');
            } else {
              // ตรวจสอบ displayname ซ้ำ
              $search = $db->first($user_table, array('displayname', $save['displayname']));
              if ($search !== false && $user->id != $search->id) {
                $ret['ret_register_displayname'] = Language::replace('This :name already exist', array(':name' => Language::get('Name')));
              }
            }
          }
          // ชื่อ นามสกุล
          if (!empty($save['fname']) || !empty($save['lname'])) {
            $search = $db->first($user_table, array(array('fname', $save['fname']), array('lname', $save['lname'])));
            if ($search !== false && $user->id != $search->id) {
              $ret['ret_register_fname'] = Language::replace('This :name already exist', array(':name' => Language::get('Name').' '.Language::get('Surname')));
            }
          }
          // โทรศัพท์
          if (!empty($save['phone1'])) {
            if (!preg_match('/[0-9]{9,10}/', $save['phone1'])) {
              $ret['ret_register_phone1'] = str_replace(':name', Language::get('phone number'), Language::get('Invalid :name'));
            } else {
              // ตรวจสอบโทรศัพท์
              $search = $db->first($user_table, array('phone1', $save['phone1']));
              if ($search !== false && $user->id != $search->id) {
                $ret['ret_register_phone1'] = Language::replace('This :name already exist', array(':name' => Language::get('phone number')));
              }
            }
          }
          // แก้ไขรหัสผ่าน
          if ($user->fb == 0 && (!empty($password) || !empty($repassword))) {
            if (mb_strlen($password) < 4) {
              // รหัสผ่านต้องไม่น้อยกว่า 4 ตัวอักษร
              $ret['ret_register_password'] = Language::get('Passwords must be at least four characters');
            } elseif ($repassword != $password) {
              // ถ้าต้องการเปลี่ยนรหัสผ่าน กรุณากรอกรหัสผ่านสองช่องให้ตรงกัน
              $ret['ret_register_repassword'] = Language::get('To change your password, enter your password to match the two inputs');
            } else {
              // password ใหม่ถูกต้อง
              $save['password'] = md5($password.$user->email);
            }
          }
          // อัปโหลดไฟล์
          foreach ($request->getUploadedFiles() as $item => $file) {
            if ($file->hasUploadFile()) {
              $item = str_replace('register_', '', $item);
              if (!File::makeDirectory(ROOT_PATH.self::$cfg->usericon_folder)) {
                // ไดเรคทอรี่ไม่สามารถสร้างได้
                $ret['ret_register_'.$item] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), self::$cfg->usericon_folder);
              } else {
                if (!empty($user->icon)) {
                  // ลบไฟล์เดิม
                  @unlink(ROOT_PATH.self::$cfg->usericon_folder.$user->icon);
                }
                try {
                  // อัปโหลด thumbnail
                  $save['icon'] = $user->id.'.jpg';
                  $file->cropImage(self::$cfg->user_icon_typies, ROOT_PATH.self::$cfg->usericon_folder.$save['icon'], self::$cfg->user_icon_w, self::$cfg->user_icon_h);
                } catch (\Exception $exc) {
                  // ไม่สามารถอัปโหลดได้
                  $ret['ret_register_'.$item] = Language::get($exc->getMessage());
                }
              }
            }
          }
          if (!empty($save) && empty($ret)) {
            // save
            $db->update($user_table, $user->id, $save);
            // เปลี่ยน password ที่ login ใหม่
            if (!empty($save['password'])) {
              $_SESSION['login']['password'] = $password;
            }
            // คืนค่า
            $ret['alert'] = Language::get('Saved successfully');
            $ret['location'] = 'index.php?module=editprofile&tab='.$request->post('tab')->toString();
            // clear
            $request->removeToken();
          }
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่าเป็น JSON
    if (!empty($ret)) {
      echo json_encode($ret);
    }
  }
}