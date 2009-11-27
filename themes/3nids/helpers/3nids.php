<?php defined("SYSPATH") or die("No direct script access.");
/**
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2009 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

class 3nids_Core {
  public function fancylink($item, $view_type="album", $group_img = true,
                            $display_comment = true, $parent_title_class = "h2") {
    // view_type = album || dynamic || header
    $link = "";
    access::required("view", $item);

    $photo_size = module::get_var("3nids", "photo_size");
    if ($photo_size == "full"){
      $width = $item->width;
      $height = $item->height;
    }else{
      $width = $item->resize_width;
      $height = $item->resize_height;
    }

    $description_mode = module::get_var("3nids", "description");
    $description = "";
    $tags = tag::item_tags($item);
    if(count($tags) && $description_mode == "tags"){
      $description =  " || " . implode(", ", $tags);
    } else if ($description_mode == "item" && $item->description != ""){
      $description = " || " . str_replace("\"","&quot;",$item->description);
    } else if (($description_mode == "parent" ||
                $description_mode == "item") && $item->parent()->description != ""){
      $description = " || " . str_replace("\"", "&quot;", $item->parent()->description);
    }

    $title_mode = module::get_var("3nids", "title");
    if ($title_mode == "parent"){
      $title = html::clean($item->parent()->title);
    } else {
      $title = html::clean($item->title);
    }

    $rel = "";
    if ($group_img == true) {
      $rel = " rel=\"fancygroup\" ";
    }

    if ($item->is_photo() || ($item->is_movie()) && module::is_active("3nids")){
      $fancymodule = "";
      if (module::is_active("exif")) {
        $fancymodule .= "exif::" . url::site("exif/show/{$item->id}") . ";;";
      }
      if (module::is_active("comment") && module::is_active("3nids")) {
        $fancymodule .= "comment::" . url::site("comments_3nids?item_id={$item->id}") .
          ";;comment_count::" . comment_3nids::count($item) . ";;";
      }
      if ($item->is_photo()){
        $link .= "<a href=\"" . url::site("photo_3nids/show/{$item->id}") ."/?w=" . $width .
          "xewx&h=" . $height . "xehx\" " . $rel . " class=\"fancyclass iframe\" title=\"" .
          $title . $description ."\" name=\"" . $fancymodule  . " \">";
      } else {
        $link .= "<a href=\"" . url::site("movie_3nids/show/{$item->id}") . "/?w=" .
          strval(20 + $width) . "xewx&h=" . strval(50 + $height) . "xehx\" " . $rel .
          " class=\"fancyclass iframe\" title=\"" . $item->parent()->title . $description .
          "\" name=\"" . $fancymodule  . " \">";
      }
    } else if ($item->is_album() && $view_type != "header") {
      $link .= "<a href=\"" . $item->url() . "\">";
    }

    if ($view_type != "header") {
      $link .= $item->thumb_img(array("class" => "g-thumbnail")) . "</a>";
      if ($item->is_album()  && $view_type == "album") {
        $link .= "<a href=\"" . $item->url() . "?show=" . $item->id .
          "\"><$parent_title_class><span></span>" . html::clean($item->title) .
          "</$parent_title_class></a>";
      } else if (!($item->is_album()) &&  $view_type == "dynamic")  {
        $link .= "<a href=\"" . $item->parent()->url() . "?show=" . $item->id .
          "\" class=\"g-parent-album\"><$parent_title_class><span></span>" .
          html::clean($item->parent()->title) . "</$parent_title_class></a>";
      }

      if (($item->is_photo() || $item->is_movie()) && $display_comment &&
          module::is_active("comment")) {
        $link .= "<ul class=\"g-metadata\"><li><a href=\"" .
          url::site("comments_3nids?item_id={$item->id}") .
          "\" class=\"iframe fancyclass g-hidden\">" . comment_3nids::count($item) .
          " " . t("comments") . "</a></li></ul>";
      }
    } else {
      $link .= "</a>";
    }
    return $link;
  }
}
?>