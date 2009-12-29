<?php
/**
 *
 * @file helperlib.php
 * @license GPL3 
 *
 *  Copyright 2008  Meitar Moscovitz  (email : meitarm@gmail.com)
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 */

/**
 * Like strip_tags() but only strips the tags you specify.
 *
 * Copied from: http://www.php.net/manual/en/function.strip-tags.php#93567
 */
function strip_only($str, $tags) {

    if (!is_array($tags)) {
        $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
        if (end($tags) == '') { array_pop($tags); }
    }

    foreach ($tags as $tag) {
        $str = preg_replace('#</?'.$tag.'[^>]*>#is', '', $str);
    }

    return $str;
}
?>
