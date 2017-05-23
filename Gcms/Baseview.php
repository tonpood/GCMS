<?php
/**
 * @filesource Gcms/Baseview.php
 * @link http://www.kotchasan.com/
 * @copyright 2016 Goragod.com
 * @license http://www.kotchasan.com/license/
 */

namespace Gcms;

/**
 * View base class สำหรับ GCMS.
 *
 * @author Goragod Wiriya <admin@goragod.com>
 *
 * @since 1.0
 */
class Baseview extends \Kotchasan\View
{
  /**
   * ลิสต์รายการ JSON-LD
   *
   * @var array
   */
  protected $jsonld = array();
  /**
   * ลิสต์รายการ breadcrumb สำหรับ JSON-LD
   *
   * @var array
   */
  protected $breadcrumbs_jsonld = array();

  /**
   * กำหนดค่า JSON-LD
   *
   * @param array $datas
   */
  public function setJsonLd($datas)
  {
    $this->jsonld[] = $datas;
  }

  /**
   * สร้างข้อมูล JSON-LD สำหรับ breadcrumb (BreadcrumbList)
   *
   * @return array
   */
  public function getBreadcrumbJsonld()
  {
    // BreadcrumbList
    if (sizeof($this->breadcrumbs_jsonld) > 1) {
      $elements = array();
      foreach ($this->breadcrumbs_jsonld as $i => $items) {
        $elements[] = array(
          '@type' => 'ListItem',
          'position' => $i + 1,
          'item' => $items
        );
      }
      return array(
        '@context' => 'http://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $elements
      );
    }
    return array();
  }
}