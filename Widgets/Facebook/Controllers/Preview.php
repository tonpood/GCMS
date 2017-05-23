<?php
/**
 * @filesource Widgets/Facebook/Controllers/Index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Facebook\Controllers;

use \Kotchasan\Http\Request;

/**
 * Controller หลัก สำหรับแสดงผล Widget
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Preview extends \Kotchasan\Controller
{

  /**
   * หน้าเว็บ Facebook Page
   * @param Request $request
   */
  public function index(Request $request)
  {
    if (empty(self::$cfg->facebook_page)) {
      self::$cfg->facebook_page = \Widgets\Facebook\Models\Settings::defaultSettings();
    }
    $query_string = array();
    foreach (self::$cfg->facebook_page as $key => $value) {
      $query_string[$key] = $request->get($key, $value)->toString();
    }
    if (!empty($query_string)) {
      // หน้าเว็บ Facebook
      echo \Widgets\Facebook\Views\Preview::render($query_string);
    }
  }
}