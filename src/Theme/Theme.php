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
use Compage\Theme\Controllers\InitializeController;

class Theme extends Pluggable {

  protected $stylesheets;
  protected $scripts;
  protected $features;
  protected $menus;
  protected $sidebars;
  protected $widgets;

  public function __construct($theme_base_file) {
    parent::__construct($theme_base_file);
    $this->stylesheets = array();
    $this->scripts = array();
    $this->features = array();
    $this->menus = array();
    $this->sidebars = array();
    $this->widgets = array();
  }

  public function addStylesheet($css, $media) {
    $this->stylesheets[md5(time())] = array(
      "source" => $css,
      "media" => $media
    );
  }

  public function addScript($js, $footer = false) {
    $this->scripts[md5(time())] = array(
      "source" => $js,
      "footer" => $footer
    );
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
  }

  public function addMenu($id, $caption) {
    $this->menus[$id] = $caption;
  }

  public function addSidebar($options = array()) {
    $this->sidebars[$options["id"]] => $options;
  }

  public function addWidget($widget_class) {
    $this->widgets[] = $widget_class;
  }

  public function initialize() {
    new InitializeController($this);
  }

}