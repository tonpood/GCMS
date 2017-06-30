<?php
/**
 * @filesource modules/index/models/languageadd.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Languageadd;

use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Config;

/**
 * บันทึกภาษาหลัก
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Model extends \Kotchasan\KBase
{

  /**
   * form submit
   */
  public function save()
  {
    $ret = array();
    // referer, session, member
    if (self::$request->initSession() && self::$request->isReferer() && $login = Login::isAdmin()) {
      if ($login['email'] == 'demo' || !empty($login['fb'])) {
        $ret['alert'] = Language::get('Unable to complete the transaction');
      } else {
        // โหลด config
        $config = Config::load(ROOT_PATH.'settings/config.php');
        // รับค่าจากการ POST
        $post = array(
          'language_name' => self::$request->post('language_name')->text(),
          'copy' => self::$request->post('lang_copy')->text(),
          'language' => self::$request->post('language')->text()
        );
        // ตรวจสอบค่าที่ส่งมา
        if (!preg_match('/^[a-z]{2,2}$/', $post['language_name'])) {
          $ret['ret_language_name'] = 'this';
        }
        // Model
        $model = new \Kotchasan\Model;
        // ชื่อตาราง
        $language_table = $model->getTableName('language');
        if (empty($ret)) {
          if (empty($post['language'])) {
            // สร้างภาษาใหม่
            if (!@copy(ROOT_PATH.'language/'.$post['copy'].'.php', ROOT_PATH.'language/'.$post['language_name'].'.php')) {
              // error copy file
              $ret['alert'] = sprintf(Language::get('Directory %s cannot be created or is read-only.'), 'language/');
            } else {
              @copy(ROOT_PATH.'language/'.$post['copy'].'.js', ROOT_PATH.'language/'.$post['language_name'].'.js');
              @copy(ROOT_PATH.'language/'.$post['copy'].'.gif', ROOT_PATH.'language/'.$post['language_name'].'.gif');
              $config->languages[] = $post['language_name'];
              // เพิ่อมคอลัมน์ภาษา
              $model->db()->query("ALTER TABLE `$language_table` ADD `$post[language_name]` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci AFTER `$post[copy]`");
              // สำเนาภาษา
              $model->db()->query("UPDATE `$language_table` SET `$post[language_name]`=`$post[copy]`");
            }
          } elseif ($post['language_name'] != $post['language']) {
            // เปลี่ยนชื่อภาษา
            rename(ROOT_PATH.'language/'.$post['language'].'.php', ROOT_PATH.'language/'.$post['language_name'].'.php');
            rename(ROOT_PATH.'language/'.$post['language'].'.js', ROOT_PATH.'language/'.$post['language_name'].'.js');
            rename(ROOT_PATH.'language/'.$post['language'].'.gif', ROOT_PATH.'language/'.$post['language_name'].'.gif');
            foreach ($config->languages as $i => $item) {
              if ($item == $post['language']) {
                $config->languages[$i] = $post['language_name'];
              }
            }
            // อัปเดทฐานข้อมูล
            $model->db()->query("ALTER TABLE `$language_table` CHANGE `$post[language]` `$post[language_name]` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL");
          }
          // ไอคอนอัปโหลด
          foreach (self::$request->getUploadedFiles() as $item => $file) {
            if ($file->hasUploadFile()) {
              // ตรวจสอบไฟล์อัปโหลด
              if (!$file->validFileExt(array('gif'))) {
                $ret['alert'] = Language::get('The type of file is invalid');
              } else {
                try {
                  $file->moveTo(ROOT_PATH.'language/'.$post['language_name'].'.gif');
                } catch (\Exception $exc) {
                  // ไม่สามารถอัปโหลดได้
                  $ret['ret_'.$item] = Language::get($exc->getMessage());
                }
              }
            }
          }
          if (empty($ret)) {
            // save config
            if (Config::save($config, ROOT_PATH.'settings/config.php')) {
              $ret['alert'] = Language::get('Saved successfully');
              $ret['location'] = self::$request->getUri()->postBack('index.php', array('module' => 'languages'));
            } else {
              $ret['alert'] = sprintf(Language::get('File %s cannot be created or is read-only.'), 'settings/config.php');
            }
          }
        }
      }
    } else {
      $ret['alert'] = Language::get('Unable to complete the transaction');
    }
    // คืนค่า json
    echo json_encode($ret);
  }
}