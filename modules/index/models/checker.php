<?php
/**
 * @filesource index/models/checker.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Checker;

use \Kotchasan\Language;
use \Kotchasan\Validator;
use \Gcms\Gcms;
use \Kotchasan\Antispam;

/**
 * ตรวจสอบข้อมูลสมาชิกด้วย Ajax
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\Model
{

  /**
   * ฟังก์ชั่นตรวจสอบความถูกต้องของอีเมล์ และตรวจสอบอีเมล์ซ้ำ
   */
  public function email()
  {
    // referer
    if (self::$request->isReferer()) {
      $id = self::$request->post('id')->toInt();
      $value = self::$request->post('value')->toString();
      if (!Validator::email($value)) {
        echo Language::replace('Invalid :name', array(':name' => Language::get('Email')));
      } else {
        // ตรวจสอบอีเมล์ซ้ำ
        $search = $this->db()->first($this->getTableName('user'), array('email', $value));
        if ($search && ($id == 0 || $id != $search->id)) {
          echo Language::replace('This :name already exist', array(':name' => Language::get('Email')));
        }
      }
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบความถูกต้องของหมายเลขโทรศัพท์ และตรวจสอบหมายเลขโทรศัพท์ซ้ำ
   */
  public function phone()
  {
    // referer
    if (self::$request->isReferer()) {
      $id = self::$request->post('id')->toInt();
      $value = self::$request->post('value')->toString();
      if (!preg_match('/[0-9]{9,10}/', $value)) {
        echo Language::replace('Invalid :name', array(':name' => Language::get('phone number')));
      } else {
        // ตรวจสอบโทรศัพท์
        $model = new static;
        $search = $model->db()->first($model->getTableName('user'), array('phone1', $value));
        if ($search && ($id == 0 || $id != $search->id)) {
          echo Language::replace('This :name already exist', array(':name' => Language::get('phone number')));
        }
      }
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบความถูกต้องของเลขประชาชน และตรวจสอบเลขประชาชนซ้ำ
   */
  public function idcard()
  {
    // referer
    if (self::$request->isReferer()) {
      $id = self::$request->post('id')->toInt();
      $value = self::$request->post('value')->toString();
      if (!preg_match('/[0-9]{13,13}/', $value)) {
        echo Language::replace('Invalid :name', array(':name' => Language::get('Identification number')));
      } else {
        // ตรวจสอบ idcard
        $model = new static;
        $search = $model->db()->first($model->getTableName('user'), array('idcard', $value));
        if ($search && ($id == 0 || $id != $search->id)) {
          echo Language::replace('This :name already exist', array(':name' => Language::get('idcard')));
        }
      }
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบความถูกต้องของชื่อเรียก และตรวจสอบชื่อเรียกซ้ำ
   */
  public function displayname()
  {
    // referer
    if (self::$request->isReferer()) {
      $id = self::$request->post('id')->toInt();
      $value = self::$request->post('value')->text();
      if (!empty($value)) {
        // ตรวจสอบ ชื่อเรียก
        $model = new static;
        $search = $model->db()->first($model->getTableName('user'), array('displayname', $value));
        if ($search && ($id == 0 || $id != $search->id)) {
          echo Language::replace('This :name already exist', array(':name' => Language::get('Name')));
        }
      }
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบความถูกต้องของ Anti Spam
   */
  public function antispam()
  {
    // referer, session
    if (self::$request->initSession() && self::$request->isReferer()) {
      $antispam = new Antispam(self::$request->post('id')->toString());
      if (!$antispam->valid(self::$request->post('value')->toString())) {
        echo Language::replace('Incorrect :name', array(':name' => Language::get('Antispam')));
      }
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบชื่อโมดูลซ้ำ
   */
  public function module()
  {
    // referer
    if (self::$request->isReferer()) {
      $id = self::$request->post('id')->toInt();
      $value = self::$request->post('value')->toString();
      $lng = self::$request->post('lng')->filter('a-z');
      $owner = self::$request->post('owner')->toString();
      if (!preg_match('/^[a-z0-9]{1,}$/', $value)) {
        echo Language::get('English lowercase and number only');
      } elseif (in_array($value, Gcms::$MODULE_RESERVE) || (($value != $owner && is_dir(ROOT_PATH.'modules/'.$value)) || is_dir(ROOT_PATH.'Widgets/'.$value) || is_dir(ROOT_PATH.$value) || is_file(ROOT_PATH.$value.'.php'))) {
        // เป็นชื่อโฟลเดอร์หรือชื่อไฟล์
        echo Language::get('Invalid name');
      } else {
        $model = new static;
        // ค้นหาชื่อโมดูลซ้ำ
        $where = array(
          array('module_id', 'IN', $model->db()->createQuery()->select('id')->from('modules')->where(array('module', $value))),
          array('index', 1)
        );
        if ($id > 0) {
          $where[] = array('id', '!=', $id);
        }
        $query = $model->db()->createQuery()
          ->select('language')
          ->from('index')
          ->where($where)
          ->order('language')
          ->toArray();
        $error = false;
        foreach ($query->execute() as $item) {
          if (
            // ซ้ำกับโมดูลอื่น
            $id > 0 ||
            // มีภาษาที่เลือกอยู่แล้ว
            $item['language'] == $lng ||
            // ทุกภาษาอยู่แล้ว
            $item['language'] == '' ||
            // เลือกทุกภาษาแต่มีภาษาอื่นอยู่แล้ว
            $lng == ''
          ) {
            $error = true;
            break;
          }
        }
        if ($error) {
          echo Language::replace('This :name already exist', array(':name' => Language::get('Module')));
        }
      }
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบ alias ซ้ำ
   */
  public static function alias()
  {
    // referer
    if (self::$request->isReferer()) {
      $id = self::$request->post('id')->toInt();
      $value = Gcms::aliasName(self::$request->post('val')->toString());
      // Model
      $model = new static;
      // ค้นหาชื่อเรื่องซ้ำ
      $search = $model->db()->first($model->getTableName('index'), array('alias', $value));
      if ($search && ($id == 0 || $id != $search->id)) {
        echo Language::replace('This :name already exist', array(':name' => Language::get('Alias')));
      }
    }
  }

  /**
   * ฟังก์ชั่นตรวจสอบชื่อเรื่องซ้ำ
   */
  public static function topic()
  {
    // referer
    if (self::$request->isReferer()) {
      $id = self::$request->post('id')->toInt();
      $lng = self::$request->post('lng')->toString();
      // Model
      $model = new static;
      // ค้นหาชื่อไตเติลซ้ำ
      $where = array(
        array('topic', self::$request->post('value')->text())
      );
      if ($id > 0) {
        $where[] = array('id', '!=', $id);
      }
      $query = $model->db()->createQuery()
        ->select('language')
        ->from('index_detail')
        ->where($where);
      $error = false;
      foreach ($query->toArray()->execute() as $item) {
        if ($lng == '') {
          $error = true;
        } elseif ($item['language'] == '') {
          $error = true;
        } elseif ($item['language'] == $lng) {
          $error = true;
        }
      }
      if ($error) {
        echo Language::replace('This :name already exist', array(':name' => Language::get('Topic')));
      }
    }
  }
}
