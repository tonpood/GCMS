<?php
/**
 * @filesource index/controllers/skin.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Skin;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Html;
use \Kotchasan\Config;

/**
 * module=skin
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * ตั้งค่า template
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // แอดมิน
    if (Login::isAdmin()) {
      // ข้อความ title bar
      $this->title = '{LNG_Customize the layout of the site}';
      // เลือกเมนู
      $this->menu = 'settings';
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-settings">{LNG_Site settings}</span></li>');
      $ul->appendChild('<li><span>{LNG_Template settings}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h1 class="icon-index">'.$this->title.'</h1>'
      ));
      // โหลด config
      $config = Config::load(ROOT_PATH.'settings/config.php');
      // แสดงฟอร์ม
      $section->appendChild(createClass('Index\Skin\View')->render($config));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}