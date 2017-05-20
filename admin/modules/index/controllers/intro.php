<?php
/**
 * @filesource index/controllers/intro.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Index\Intro;

use \Kotchasan\Http\Request;
use \Kotchasan\Login;
use \Kotchasan\Language;
use \Kotchasan\Html;

/**
 * module=intro
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Controller extends \Gcms\Controller
{

  /**
   * ฟอร์มตั้งค่าหน้า intro
   *
   * @param Request $request
   * @return string
   */
  public function render(Request $request)
  {
    // แอดมิน
    if (Login::isAdmin()) {
      // ข้อความ title bar
      $this->title = '{LNG_Enable/Disable Intro Page}';
      // เลือกเมนู
      $this->menu = 'settings';
      // ภาษาที่ต้องการ
      $language = self::$request->get('language', Language::name())->toString();
      if (preg_match('/^[a-z]{2,2}$/', $language)) {
        // intro detail
        $template = ROOT_PATH.DATA_FOLDER.'intro.'.$language.'.php';
        if (is_file($template)) {
          $template = trim(preg_replace('/<\?php exit([\(\);])?\?>/', '', file_get_contents($template)));
        } else {
          $template = '<p style="padding: 20px; text-align: center; font-weight: bold;"><a href="index.php">Welcome<br>ยินดีต้อนรับ</a></p>';
        }
        // แสดงผล
        $section = Html::create('section');
        // breadcrumbs
        $breadcrumbs = $section->add('div', array(
          'class' => 'breadcrumbs'
        ));
        $ul = $breadcrumbs->add('ul');
        $ul->appendChild('<li><span class="icon-settings">{LNG_Site settings}</span></li>');
        $ul->appendChild('<li><span>{LNG_Intro Page}</span></li>');
        $section->add('header', array(
          'innerHTML' => '<h1 class="icon-write">'.$this->title.'</h1>'
        ));
        // แสดงฟอร์ม
        $section->appendChild(createClass('Index\Intro\View')->render($language, $template));
        return $section->render();
      }
    }
    // 404.html
    return \Index\Error\Controller::page404();
  }
}