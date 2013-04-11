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

use Compage\Component\Controller;
use Compage\Component\ComponentType;
use Compage\Essentials\Pluggable;

class TaxonomyController extends Controller {

  protected $term;
  protected $posts;

  public function __construct($pluggable, $settings) {
    parent::__construct($pluggable);

    $term = get_queried_object();
    if (isset($term->term_id)) {
      $this->term = $term;
      $get_terms_settings = array();
      $get_terms_settings["hide_empty"] = !$settings["include_empty_taxonomy"];
      $this->terms = get_terms($settings["taxonomy"], $get_terms_settings);
      if (!in_array($settings["taxonomy"], array('category', 'tag'))) {
        foreach ($this->terms as $i => $the_term) {
          $this->terms[$i]->meta = $this->getPluggable()
            ->get($settings["taxonomy_class"])
            ->getMetaFieldValues($the_term->term_id);
        }
      }
      $this->posts = get_posts(array(
        "post_type" => $settings["post_type"],
        "tax_query" => array(
          "taxonomy" => $settings["taxonomy"],
          "field" => "id",
          "terms" => $this->term->term_id
        )
      ));
      if ($settings["load_post_meta"] == true) {
        foreach ($this->posts as $key => $post) {
          $this->posts[$key]->meta = get_post_meta($post->ID);
          foreach ($this->posts[$key]->meta as $meta_id => $meta_value) {
            $this->posts[$key]->meta[$meta_id] = $meta_value[count($meta_value) - 1];
          }
        }
      }
    }
  }

  public function getTerm() {
    return $this->term;
  }

  public function getTaxonomyPosts() {
    return $this->posts;
  }

  public function getTerms() {
    return $this->terms;
  }

}