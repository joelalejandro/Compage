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

/* Handles plugin notifications hooks. */
class BaseNotification {

  protected $message;
  protected $type;

  public function __construct($message, $type = "info") {
    $this->setMessage($message)->setType($type);
    if ($message != "" && $message != null) {
      add_action("admin_notices", array($this, "show"));
    }
  }

  public function show() {
    echo "<div class='" . $this->getTypeClass() . "'>";
    echo $this->message;
    echo "</div>";
  }

  public function getMessage() {
    return $this->message;
  }

  public function setMessage($message) {
    $this->message = $message;
    return $this;
  }

  public function getType() {
    return $this->type;
  }

  protected function getTypeClass() {
    $type_class = "";
    switch ($this->getType()) {
      case "info":
        $type_class = "updated";
        break;
      case "error":
        $type_class = "error";
        break;
    }
    return $type_class;
  }

  public function setType($type) {
    $this->type = $type;
    return $this;
  }


}
