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

namespace Compage\Theme;

use Compage\Essentials\Context;
use Compage\Essentials\Pluggable;
use Compage\Essentials\PluggableType;
use Compage\Component\ComponentType;
use Compage\Theme\Controllers\InitializeController;

abstract class Theme extends Pluggable {

  protected $stylesheets;
  protected $scripts;
  protected $features;
  protected $menus;
  protected $sidebars;
  protected $widgets;
  protected $events;
  protected $images;

  public function __construct($theme_base_file) {
    parent::__construct($theme_base_file);
    $this->stylesheets = array();
    $this->scripts = array();
    $this->features = array();
    $this->menus = array();
    $this->sidebars = array();
    $this->widgets = array();
    $this->events = array();
    $this->images = array();

    $this->type = PluggableType::Theme;
  }

  public function get($collection, $key = "") {
    if (stripos($collection, "controller") !== false) {
      return $this->getComponent(ComponentType::Controller, $collection);
    } else if ($collection == "controller") {
      return $this->getComponent($collection, $key);
    } else {
      $array = $this->{$collection};
      if (isset($key) && $key != "") {
        if ($collection == "images") {
          return $array[$key]["source"];  
        } else {
          return $array[$key];
        }        
      } else {
        return $array;
      }
    }
  }

  public function addStylesheet($css, $media) {
    $this->stylesheets[md5(time().$css)] = array(
      "source" => $this->getDirectoryAsUri(ComponentType::Asset) . "/" . $css,
      "media" => $media
    );
    return $this;
  }

  public function addImage($img) {
    $this->images[$img] = array(
      "source" => $this->getDirectoryAsUri(ComponentType::Asset) . "/" . $img
    );
    return $this;
  }

  public function addScript($js, $footer = true) {
    $this->scripts[md5(time().$js)] = array(
      "source" => $this->getDirectoryAsUri(ComponentType::Asset) . "/" . $js,
      "footer" => $footer
    );
    return $this;
  }

  public function addFeature($feature, array $options = array()) {
    if (!in_array($feature, array(
      "post-formats",
      "post-thumbnails",
      "custom-background",
      "custom-header",
      "automatic-feed-links",
      "shortcode-in-widgets"
    ))) {
      $this->features[$feature] = $options;
    }
    return $this;
  }

  public function addMenu($id, $caption, $callback = null) {
    $this->menus[$id] = array(
      "caption" => $caption,
      "callback" => $callback,
      "id" => $id
    );
    return $this;
  }

  public function addSidebar($options = array()) {
    $this->sidebars[$options["id"]] = $options;
    return $this;
  }

  public function addWidget($widget_class) {
    $this->widgets[] = $widget_class;
    return $this;
  }

  public function onInitialize($callback) {
    $this->events["initialize"][] = $callback;
    return $this;
  }

  public function initialize() {
    new InitializeController($this);
    
    if (isset($this->events["initialize"])) {
      foreach ($this->events["initialize"] as $callback) {
        if (is_callable($callback)) { $callback($this); }
      }
    }
  }
  
  static public function activate($instance, $file) {
    $class = self::exists($instance);
    if ($class !== false) {
      Context::registerPluggable(new $class($file));
    }
  }
}