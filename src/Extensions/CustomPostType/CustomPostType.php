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

namespace Compage\Extensions\CustomPostType;

use Compage\Component\Component;
use Compage\Component\ComponentType;

class CustomPostType extends Component {

  protected $labels;
  protected $isPublic;
  protected $menuPosition;
  protected $supports;
  protected $menuIcon;
  protected $hasArchive;
  protected $taxonomies;
  protected $metaFields;

  public function __construct($pluggable, $name) {
    parent::__construct($pluggable);
    $this->setComponentName($name);
    $this->setComponentType(ComponentType::CustomPostType);
    $this->supports = array();
    $this->taxonomies = array();
    $this->labels = array();
    $this->metaFields = array();
  }

  public function instantiate() {
    $component = $this->getPluggable()->getFullyQualifiedName()
               . "\\" . $this->getPluggable()->getDirectory(ComponentType::CustomPostType)
               . "\\" . $this->getComponentName();
    $class = new \ReflectionClass($component);
    return new $component($this->getPluggable(), $this->getComponentName());
  }

  public function getLabels() {
    return $this->labels;
  }

  public function setLabels($labels) {
    $this->labels = $labels;
    return $this;
  }

  public function isPublic($value = null) {
    if (!isset($value)) {
      return $this->isPublic;
    } else {
      $this->isPublic = $value;
      return $this;
    }
  }

  public function setMenuPosition($pos) {
    $this->menuPosition = $pos;
    return $this;
  }

  public function getMenuPosition() {
    return $this->menuPosition;
  }

  public function supports($feature = null) {
    if (!isset($feature)) {
      return $this->supports;
    } else {
      $this->supports[] = $feature;
      return $this;
    }
  }

  public function setMenuIcon($icon) {
    $this->menuIcon = $icon;
  }

  public function getMenuIcon() {
    return $this->menuIcon;
  }

  public function hasArchive($value = null) {
    if (!isset($value)) {
      return $this->hasArchive;
    } else {
      $this->hasArchive = $value;
      return $this;
    }
  }

  public function usesTaxonomy($tax) {
    $this->taxonomies[] = $tax;
    return $this;
  }

  public function getPostTypeID() {
    return strtolower(str_ireplace("CustomPostType", "", $this->getComponentName()));
  }

  public function register() {
    
    $post_type = $this->getPostTypeID();
    $posts_type = $post_type . "s";

    register_post_type($post_type, array(
      'labels' => $this->labels,
      'public' => $this->isPublic,
      'menu_position' => $this->menuPosition,
      'supports' => $this->supports,
      'menu_icon' => $this->menuIcon,
      'has_archive' => $this->hasArchive,
      'capability_type' => array($post_type, $posts_type)
    ));

    $capabilities = array(
      "publish_" . $posts_type => true,
      "edit_" . $posts_type => true,
      "edit_others_" . $posts_type => true,
      "delete_" . $posts_type => true,
      "delete_others_" . $posts_type => true,
      "read_private_" . $posts_type => true,
      "edit_" . $post_type => true,
      "delete_" . $post_type => true,
      "read_" . $post_type => true
    );

    add_role("manage_" . $post_type, "Admin " . $this->labels["name"], $capabilities);

    $role = get_role("administrator");
    foreach ($capabilities as $cap => $status) {
      $role->add_cap($cap);
    }

    foreach ($this->taxonomies as $tax) {
      $taxonomy = $this->getPluggable()->get($tax . "CustomTaxonomy");
      register_taxonomy_for_object_type($taxonomy->getTermID(), $post_type);
    }

    if (count($this->metaFields) > 0) {
      $metaFields = $this->metaFields;
      $name = $this->labels["singular_name"];
      add_action("save_post", function($post_id) use ($metaFields, $post_type) {
        foreach ($metaFields as $metaFieldID => $field) {
          update_post_meta(
            $post_id, 
            $post_type . "_meta_field_" . $metaFieldID, 
            $_REQUEST[$post_type . "_meta_field"][$metaFieldID]
          );
        }
      });
      add_action("add_meta_boxes", function() use ($metaFields, $name, $post_type) {
        \add_meta_box($post_type . "_metabox", $name, function($post) use ($metaFields, $post_type) {

          $template = <<<HTML
          <tr class='form-field'>
          <th scope='row' align="right" valign='top' width="15%">
            <label for="{field_id}">{name}</label>  
          </th>  
          <td>  
            <input type="{type}" name="{post_type}_meta_field[{field_id}]" id="{post_type}_meta_field_{field_id}"
             style="width:95%;" value="{value}"><br />  
            <span class="description">{description}</span>  
          </td>  
          </tr>
HTML;

          $template_textarea = <<<HTML
          <tr class='form-field'>
          <th scope='row' align="right" valign='top' width="20%">
            <label for="{field_id}">{name}</label>  
          </th>  
          <td>  
            <textarea rows="4" name="{post_type}_meta_field[{field_id}]" id="{post_type}_meta_field_{field_id}"
             style="width:95%;">{value}</textarea><br />  
            <span class="description">{description}</span>  
          </td>  
          </tr>
HTML;

          echo "<table width=100%>";
          foreach ($metaFields as $metaFieldID => $metaField) {
            $metaValue = get_post_meta($post->ID, $post_type . "_meta_field_" . $metaFieldID, true);
            $html = $metaField["type"] == "textarea" ? $template_textarea : $template;
            $html = str_replace("{post_type}", $post_type, $html);
            $html = str_replace("{field_id}", $metaFieldID, $html);
            $html = str_replace("{value}", $metaValue, $html);
            $html = str_replace("{name}", $metaField["name"], $html);
            $html = str_replace("{type}", $metaField["type"], $html);
            $html = str_replace("{description}", $metaField["description"], $html);
            echo $html;
          }
          echo "</table>";

        }, $post_type, "normal", "");      
      });
    }
  }

  public function addMetaField($key, $settings) {
    $this->metaFields[$key] = array(
      "id" => $key,
      "type" => $settings["type"],
      "name" => $settings["name"],
      "description" => $settings["description"]
    );
  }

}