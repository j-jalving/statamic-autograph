<?php

namespace JJalving\Autograph\Helpers;

class Minify
{
  /**
   * Get a minified version of the given HTML string
   *
   * @param string $html
   * @return string
   */
  public static function minifyHtml(string $html): string
  {
    if (trim($html) === "") return $html;
    // Remove extra white-space(s) between HTML attribute(s)
    $html = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function ($matches) {
      return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
    }, str_replace("\r", "", $html));

    // Minify inline CSS declaration(s)
    if (strpos($html, ' style=') !== false) {
      $html = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function ($matches) {
        return '<' . $matches[1] . ' style=' . $matches[2] . Minify::minifyCss($matches[3]) . $matches[2];
      }, $html);
    }
    if (strpos($html, '</style>') !== false) {
      $html = preg_replace_callback('#<style(.*?)>(.*?)</style>#is', function ($matches) {
        return '<style' . $matches[1] . '>' . Minify::minifyCss($matches[2]) . '</style>';
      }, $html);
    }

    return preg_replace(
      array(
        // t = text
        // o = tag open
        // c = tag close
        // Keep important white-space(s) after self-closing HTML tag(s)
        '#<(img|html)(>| .*?>)#s',
        // Remove a line break and two or more white-space(s) between tag(s)
        '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
        '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
        '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
        '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
        '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
        '#<(img|html)(>| .*?>)<\/\1>#s', // reset previous fix
        '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
        '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
        // Remove HTML comment(s) except IE and noindex comment(s)
        '#\s*<!--(?!(\[if\s)|(noindex)|(\/noindex)).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s', //'#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s' - noindex fix
        '/\s+/', // Remove double spaces
      ),
      array(
        '<$1$2</$1>',
        '$1$2$3',
        '$1$2$3',
        '$1$2$3$4$5',
        '$1$2$3$4$5$6$7',
        '$1$2$3',
        '<$1$2',
        '$1 ',
        '$1',
        '',
        ' '
      ),
      $html
    );
  }

  /**
   * Get a minified version of the given CSS string
   *
   * @param string $css
   * @return string
   */
  public static function minifyCss(string $css): string
  {
    if (trim($css) === "") return $css;
    $replaced = preg_replace(
      array(
        // Remove comment(s)
        '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
        // Remove unused white-space(s)
        '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~]|\s(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
        // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
        '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
        // Replace `:0 0 0 0` with `:0`
        '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
        // Replace `background-position:0` with `background-position:0 0`
        '#(background-position):0(?=[;\}])#si',
        // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
        '#(?<=[\s:,\-])0+\.(\d+)#s',
        // Minify string value
        '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
        '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
        // Minify HEX color code
        '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
        // Replace `(border|outline):none` with `(border|outline):0`
        '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
        // Remove empty selector(s)
        '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
      ),
      array(
        '$1',
        '$1$2$3$4$5$6$7',
        '$1',
        ':0',
        '$1:0 0',
        '.$1',
        '$1$3',
        '$1$2$4$5',
        '$1$2$3',
        '$1:0',
        '$1$2'
      ),
      $css
    );
    return str_replace('fill=none', '', $replaced); // fix for url("data:image/svg+xml,%3Csvg ...  fill='none'
  }
}
