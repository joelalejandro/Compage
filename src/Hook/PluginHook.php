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

namespace Compage;

class PluginHook extends PluginComponent {

  private $callback;

  public function __construct(Plugin $plugin, $callback) {
    parent::__construct($plugin);

    if (is_callable($callback))
      $this->callback = $callback;
  }

  public function toAction($action, $priority = 10, $accepted_args = 1) {
    add_action($action, $this->callback, $priority, $accepted_args);
    return $this;
  }

  public function toFilter($filter, $priority = 10, $accepted_args = 1) {
    add_filter($filter, $this->callback, $priority, $accepted_args);
  }

  public function toShortcode($shortcode) {
    add_shortcode($shortcode, $this->callback);
  }

  public function toPluginActivation() {
    register_activation_hook($this->getPlugin()->getRootFile(), $this->callback);
  }

  public function toPluginUninstall() {
    register_uninstall_hook($this->getPlugin()->getRootFile(), $this->callback);
  }

  public function toOptionsPage($page_title, $menu_title, $cap, $menu_slug) {
    add_options_page($this->callback, $page_title, $menu_title, $cap, $menu_slug);
  }

  public function toMetaBox($id, $title, $type, $size, $priority) {
    add_meta_box($id, $title, $this->callback, $type, $size, $priority);
  }
}
