<?php
/**
 * @filesource index/controllers/pages.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Pages;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Html;

/**
 * module=pages
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * รายการหน้าเว็บไซต์
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // แอดมิน
    if (Login::isAdmin()) {
      // ข้อความ title bar
      $this->title = '{LNG_List of all available main pages}';
      // เลือกเมนู
      $this->menu = 'index';
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-modules">{LNG_Menus} &amp; {LNG_Web pages}</span></li>');
      $ul->appendChild('<li><span>{LNG_Web pages}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h1 class="icon-index">'.$this->title.'</h1>'
      ));
      // แสดงตาราง
      $section->appendChild(createClass('Index\Pages\View')->render());
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}