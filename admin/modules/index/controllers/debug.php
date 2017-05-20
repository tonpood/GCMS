<?php
/**
 * @filesource index/controllers/debug.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Debug;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Html;

/**
 * module=debug
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * Debug
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // แอดมิน
    if (Login::isAdmin()) {
      // ข้อความ title bar
      $this->title = '{LNG_Debug tool}';
      // เลือกเมนู
      $this->menu = 'tools';
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-tools">{LNG_Tools}</span></li>');
      $ul->appendChild('<li><span>'.$this->title.'</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h1 class="icon-world">'.$this->title.'</h1>'
      ));
      $div = $section->add('div', array(
        'class' => 'setup_frm'
      ));
      $div = $div->add('div', array(
        'class' => 'item'
      ));
      $div->appendChild('<div id="debug_layer"></div>');
      $div->appendChild('<div class="submit right"><a id="debug_clear" class="button large red">{LNG_Clear}</a></div>');
      $section->script('showDebug();');
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}