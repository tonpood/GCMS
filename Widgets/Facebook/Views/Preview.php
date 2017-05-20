<?php
/**
 * @filesource Widgets/Facebook/Views/Settings.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Widgets\Facebook\Views;

/**
 * Facebook Page
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Preview extends \Kotchasan\View
{

  /**
   * Facebook Page
   *
   * @param array $query_string
   * @return string
   */
  public static function render($query_string)
  {
    // หน้าเว็บ Facebook
    $content = '<!DOCTYPE html>';
    $content .= '<html>';
    $content .= '<head>';
    $content .= '<title>Facebook</title>';
    $content .= '<meta charset=utf-8>';
    $content .= '<style>';
    $content .= '#fb-root{display: none}';
    $content .= '.fb_iframe_widget, .fb_iframe_widget span, .fb_iframe_widget span iframe[style] {width: 100% !important;}';
    $content .= '</style>';
    $content .= '</head>';
    $content .= '<body>';
    $content .= \Widgets\Facebook\Views\Index::render($query_string);
    $content .= '</body>';
    $content .= '</html>';
    return $content;
  }
}