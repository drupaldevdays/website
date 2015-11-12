<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module {

  /**
   * Fill a CK Editor field.
   *
   * @param $value
   *   The value to enter into CK Editor.
   * @param string $selector
   *   A CSS (only) selector to identify the <textarea> element.
   */
  public function fillCkEditor($value, $selector) {
    $id = $this->getModule("WebDriver")
      ->executeJs("return document.querySelector('$selector').id");
    $this->getModule("WebDriver")
      ->executeJs("CKEDITOR.instances['$id'].setData(" . json_encode($value) . ")");
  }
}
