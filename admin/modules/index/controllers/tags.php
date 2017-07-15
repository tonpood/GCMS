<?php
/**
 * @filesource modules/index/controllers/tags.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Tags;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Html;

/**
 * module=tags
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * รายการ Tags
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // แอดมิน
    if (Login::isAdmin()) {
      // ข้อความ title bar
      $this->title = '{LNG_List of} {LNG_Tags}';
      // เมนู
      $this->menu = 'widget';
      // แสดงผล
      $section = Html::create('section');
      // breadcrumbs
      $breadcrumbs = $section->add('div', array(
        'class' => 'breadcrumbs'
      ));
      $ul = $breadcrumbs->add('ul');
      $ul->appendChild('<li><span class="icon-modules">{LNG_Module}</span></li>');
      $ul->appendChild('<li><span>{LNG_Tags}</span></li>');
      $section->add('header', array(
        'innerHTML' => '<h1 class="icon-tags">'.$this->title().'</h1>'
      ));
      // แสดงตาราง
      $section->appendChild(createClass('Index\Tags\View')->render($request));
      return $section->render();
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}
