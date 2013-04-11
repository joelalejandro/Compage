<?php
/*
Copyright (c) 2013 Joel A. Villarreal Bertoldi

Permission is hereby granted, free of charge, to any
person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice
shall be included in all copies or substantial portions of
the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

namespace Compage\Extensions\CustomTaxonomy;

use Compage\Component\Component;
use Compage\Component\ComponentType;

class CustomTaxonomy extends Component {

  protected $labels;
  protected $hierarchical;
  protected $showsUI;
  protected $showsAdminColumn;
  protected $hasQueryVar;
  protected $rewrite;
  protected $taxObjectType;
  protected $metaFields;

  public function __construct($pluggable, $name) {
    parent::__construct($pluggable);
    $this->setComponentName($name);
    $this->setComponentType(ComponentType::CustomTaxonomy);
  }

  public function addMetaField($key, $settings) {
    $this->metaFields[$key] = array(
      "id" => $key,
      "type" => $settings["type"],
      "name" => $settings["name"],
      "description" => $settings["description"]
    );
  }

  public function getMetaFieldValues($termID) {
    $values = array();
    $tax = $this->getTermID();
    foreach ($this->metaFields as $metaField) {
      $values[$metaField["id"]] = get_option($tax . "_term_meta_" . $termID . "_" . $metaField["id"]);
    }
    return $values;
  }

  public function getLabels() {
    return $this->labels;
  }

  public function setLabels($labels) {
    $this->labels = $labels;
    return $this;
  }

  public function getTaxObjectType() {
    return $this->taxObjectType;
  }

  public function setTaxObjectType($tot) {
    $this->taxObjectType = $tot;
  }

  public function hierarchical($value = null) {
    if (!isset($value)) {
      return $this->hierarchical;
    } else {
      $this->hierarchical = $value;
      return $this;
    }
  }

  public function showsUI($value = null) {
    if (!isset($value)) {
      return $this->showsUI;
    } else {
      $this->showsUI = $value;
      return $this;
    }
  }

  public function showsAdminColumn($value = null) {
    if (!isset($value)) {
      return $this->showsAdminColumn;
    } else {
      $this->showsAdminColumn = $value;
      return $this;
    }
  }

  public function hasQueryVar($value = null) {
    if (!isset($value)) {
      return $this->hasQueryVar;
    } else {
      $this->hasQueryVar = $value;
      return $this;
    }
  }

  public function getRewrite() {
    return $this->rewrite;
  }

  public function setRewrite($rewrite) {
    $this->rewrite = $rewrite;
  }

  public function getTermID() {
    return strtolower(str_ireplace("CustomTaxonomy", "", $this->getComponentName()));
  }

  public function instantiate() {
    $component = $this->getPluggable()->getFullyQualifiedName()
               . "\\" . $this->getPluggable()->getDirectory(ComponentType::CustomTaxonomy)
               . "\\" . $this->getComponentName();
    $class = new \ReflectionClass($component);
    return new $component($this->getPluggable(), $this->getComponentName());
  }

  public function register() {
    $tax = $this->getTermID();
    $taxs = $tax . "s";

    $capabilities = array(
      "manage_" . $taxs => true,
      "edit_" . $taxs => true,
      "delete_" . $taxs => true,
      "assign_" . $taxs => true
    );

    $tax_caps = array(
      "manage_terms" => "manage_" . $taxs,
      "edit_terms" => "edit_" . $taxs,
      "delete_terms" => "delete_" . $taxs,
      "assign_terms" => "assign_" . $taxs
    );

    add_role("manage_" . $tax, "Admin " . $this->labels["name"], $capabilities);

    $role = get_role("administrator");
    foreach ($capabilities as $cap => $status) {
      $role->add_cap($cap);
    }    

    register_taxonomy($tax, $this->taxObjectType, array(
      'hierarchical' => $this->hierarchical,
      'labels' => $this->labels,
      'show_ui' => $this->showsUI,
      'show_admin_column' => $this->showsAdminColumn,
      'query_var' => $this->hasQueryVar,
      'rewrite' => $this->rewrite,
      'capability_type' => $tax,
      'capabilities' => $tax_caps
    ));

    $metaFields = $this->metaFields;

    if (count($metaFields) > 0) {
      add_action($tax . "_edit_form_fields", function($tag) use ($tax, $metaFields) {
        $termID = $tag->term_id;

        $template = <<<HTML
        <tr class='form-field'>
        <th scope='row' valign='top'
          <label for="{field_id}">{name}</label>  
        </th>  
        <td>  
          <input type="{type}" name="{tax}_term_meta_{term_id}[{field_id}]" id="{tax}_term_meta_{field_id}" size="25" style="width:60%;" value="{value}"><br />  
          <span class="description">{description}</span>  
        </td>  
        </tr>
HTML;

        foreach ($metaFields as $metaFieldID => $metaField) {
          $termValue = get_option($tax . "_term_meta_" . $termID . "_" . $metaFieldID);
          $html = $template;
          $html = str_replace("{tax}", $tax, $html);
          $html = str_replace("{term_id}", $termID, $html);
          $html = str_replace("{field_id}", $metaFieldID, $html);
          $html = str_replace("{value}", $termValue, $html);
          $html = str_replace("{name}", $metaField["name"], $html);
          $html = str_replace("{type}", $metaField["type"], $html);
          $html = str_replace("{description}", $metaField["description"], $html);
          echo $html;
        }
      }, 10, 2);

      add_action("edited_" . $tax, function($termID) use ($tax, $metaFields) {
        $collection = $tax . "_term_meta_" . $termID;
        if (isset($_POST[$collection])) {
          foreach ($_POST[$collection] as $key => $value) {
            update_option($collection . "_" . $key, $value);
          }
        }
      });
    }
  }

}