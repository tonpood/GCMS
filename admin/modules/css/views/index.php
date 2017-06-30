<?php
/**
 * @filesource modules/css/views/index.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Css\Index;

/**
 * Generate CSS file
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class View extends \Kotchasan\KBase
{

  /**
   * สร้างไฟล์ CSS
   */
  public function index()
  {
    // โหลด css หลัก
    $data = preg_replace('/url\(([\'"])?fonts\//isu', "url(\\1".WEB_URL.'skin/fonts/', file_get_contents(ROOT_PATH.'skin/fonts.css'));
    $data .= file_get_contents(ROOT_PATH.'skin/gcss.css');
    $data .= file_get_contents(ROOT_PATH.'skin/gcms.css');
    // css ของ template
    $data2 = file_get_contents(TEMPLATE_ROOT.'skin/'.self::$cfg->skin.'/style.css');
    $data2 = preg_replace('/url\(([\'"])?(img|fonts)\//isu', "url(\\1".WEB_URL.'skin/\\2/', $data2);
    // โหลด css ของ module
    $dir = ROOT_PATH.'modules/';
    $f = opendir($dir);
    while (false !== ($text = readdir($f))) {
      if ($text != "." && $text != "..") {
        if (is_dir($dir.$text)) {
          if (is_file($dir.$text.'/admin.css')) {
            $data2 .= preg_replace('/url\(img\//isu', 'url('.WEB_URL.'modules/'.$text.'/img/', file_get_contents($dir.$text.'/admin.css'));
          }
        }
      }
    }
    closedir($f);
    // โหลด css ของ Widgets
    $dir = ROOT_PATH.'Widgets/';
    $f = opendir($dir);
    while (false !== ($text = readdir($f))) {
      if ($text != "." && $text != "..") {
        if (is_dir($dir.$text)) {
          if (is_file($dir.$text.'/admin.css')) {
            $data2 .= preg_replace('/url\(img\//isu', 'url('.WEB_URL.'Widgets/'.$text.'/img/', file_get_contents($dir.$text.'/admin.css'));
          }
        }
      }
    }
    closedir($f);
    foreach (self::$cfg->color_status as $key => $value) {
      $data2 .= '.status'.$key.'{color:'.$value.'}';
    }
    // compress css
    $data = preg_replace(array('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '/[\s]{0,}([:;,>\{\}])[\s]{0,}/'), array('', '\\1'), $data.$data2);
    // Response
    $response = new \Kotchasan\Http\Response;
    // cache 1 month
    $expire = 2592000;
    $response->withHeaders(array(
        'Content-type' => 'text/css; charset=utf-8',
        'Cache-Control' => 'max-age='.$expire.', must-revalidate, public',
        'Expires' => gmdate('D, d M Y H:i:s', time() + $expire).' GMT',
        'Last-Modified' => gmdate('D, d M Y H:i:s', time() - $expire).' GMT'
      ))
      ->withContent(preg_replace(array('/[\r\n\t]/s', '/[\s]{2,}/s', '/;}/'), array('', ' ', '}'), $data))
      ->send();
  }
}