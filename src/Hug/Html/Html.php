<?php

namespace Hug\Html;

use Hug\Http\Http;
use Hug\Xpath\Xpath;
use Hug\HString\HString as HString;

/**
 *
 */
class Html
{
    /**
     * Test if a link is external or not
     *
     * @param string $domain Web Page Domain (to detect internal links)
     * @param string $link link to test 
     *
     * @return bool is_external_link
     *
     * @todo tester avec IDN
     */
    public static function is_external_link($domain, $link)
    {
        $is_external_link = false;
        # If domain name is in link -> internal link
        if(strpos($link, $domain)===false)
        {   
            if(strpos($link, 'http')>=0)
            {
                $is_external_link = true;
            }
        }
        return $is_external_link;
    }

    /**
     * Test if a link is pointing to domain or not
     *
     * @param string $domain domain Name (to test)
     * @param string $link link to test for domain
     *
     * @return bool IsDomainLink
     *
     * @todo tester avec IDN
     *
     */
    public static function is_domain_link($domain, $link)
    {
        $is_domain_link = false;
        if(strpos($link, $domain))
        {
            # 
            $UrlArray = parse_url($link); 
            //if(isset($UrlArray['host']) && $UrlArray['host']===$domain)
            if(isset($UrlArray['host']) && ($UrlArray['host']===$domain || $UrlArray['host']==='www.'.$domain))
            {
                $is_domain_link = true;
            }
        }
        return $is_domain_link;
    }

    /**
     * In order to embed a webpage into an iframe, this function replaces relative paths of 
     * scripts, styles, images and links into absolute paths
     * 
     * @param string $html webpage HTML
     * @param string $url webpage URL
     * @return string $html new HTML with replaced paths
     *
     */
    public static function replace_rel_to_abs_paths($html, $page_url)
    {
        $myDom = new \DOMDocument;
        @$myDom->loadHTML($html);
        $xpath = new \DOMXPath($myDom);

        # IMAGES //img/@src
        $QueryResults = $xpath->query('//img');
        foreach ($QueryResults as $QueryResult)
        {
            $src = $QueryResult->getAttribute('src');
            $QueryResult->setAttribute('src', Html::rel_to_abs($src, $page_url));
        }

        # LINKS //a/@href
        $QueryResults = $xpath->query('//a');
        foreach ($QueryResults as $QueryResult)
        {
            $href = $QueryResult->getAttribute('href');
            $QueryResult->setAttribute('href', Html::rel_to_abs($href, $page_url));
        }

        # SCRIPTS //script/@src
        $QueryResults = $xpath->query('//script');
        foreach ($QueryResults as $QueryResult)
        {
            $src = $QueryResult->getAttribute('src');
            $QueryResult->setAttribute('src', Html::rel_to_abs($src, $page_url));
        }

        # STYLES //link/@href
        $QueryResults = $xpath->query('//link');
        foreach ($QueryResults as $QueryResult)
        {
            $href = $QueryResult->getAttribute('href');
            $QueryResult->setAttribute('href', Html::rel_to_abs($href, $page_url));
        }


        $new_html = $myDom->saveHTML();
        if($new_html!==FALSE)
        {
            $html = $new_html;
        }
        
        return $html;
    }

    /**
     * 
     * 
     * @param string $rel
     * @param string $base
     *
     * @return string 
     * @link http://stackoverflow.com/questions/4444475/transfrom-relative-path-into-absolute-url-using-php
     */
    public static function rel_to_abs($rel, $base)
    {
        if(empty($rel))
        {
            return '';
        }

        # return if already absolute URL
        if(parse_url($rel, PHP_URL_SCHEME) != '')
        {
            return $rel;
        }

        # queries and anchors
        if($rel[0]=='#' || $rel[0]=='?')
        {
            return $base.$rel;
        }

        # parse base URL and convert to local variables: $scheme, $host, $path
        $r = parse_url($base);
        $scheme = $r['scheme'];
        $host = $r['host'];
        $path = isset($r['path']) ? $r['path'] : '';

        # remove non-directory element from path
        $path = preg_replace('#/[^/]*$#', '', $path);

        # destroy path if relative url points to root
        if ($rel[0] == '/') $path = '';

        # dirty absolute URL
        $abs = "$host$path/$rel";

        # replace '//' or '/./' or '/foo/../' with '/'
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }

    /**
     * This function parses HTML to find and qualify backlinks against given Domain Name.
     *
     * Each baklink have following properties :
     * - url : where is the link pointing to ?
     * - anchor : text extracted from anchor tag
     * - rel : link relation (follow|nofollow)
     * - img_alt : image alt in in case anchor tag wraps an image tag
     *
     * Here is the logic :
     * - search for meta robot tags (follow|nofollow) to set default links type
     * - for all anchor tags, look for searched domain
     * - for all searched domain anchor tags, look for text and possible images
     *
     * @param string $html HTML to parse for backlink search
     * @param string $domain Domain Name to search against
     * @return array $backlinks Backlinks found.
     * 
     * @todo Checks for IDN Domain Names
     */
    public static function find_backlinks($html, $domain)
    {
        $backlinks = [];

        $doc = new \H0gar\Xpath\Doc($html, 'html');

        /* 
            <meta name="robots" content="nofollow" /> 
            <meta name="robots" content="index,follow" />
        */
        $meta_robots_rel = 'follow';
        $meta_robots = $doc->item('//meta[@name="robots"]/@content');
        $meta_robots_text = $meta_robots->text();
        //echo $meta_robots_text."<br>";

        if(stripos($meta_robots_text, 'nofollow') > -1)
        {
            $meta_robots_rel = 'nofollow';
        }
        elseif(stripos($meta_robots_text, 'follow') > -1)
        {
            $meta_robots_rel = 'follow';
        }

        // elseif(stripos($meta_robots_text, 'index') > -1)
        //     $meta_robots_rel = 'index';
        // else
        //     $meta_robots_rel = 'R_M_def';
        //echo "META ROBOTS REL : " . $meta_robots_rel . "<br>";
        
        # EXTRACT ALL LINKS WHICH CONTAINS DOMAIN IN URL 
        foreach($doc->items('//a[contains(@href,"'.$domain.'")]') as $link)
        {
            $href = trim($link->attr('href'));
            // echo "HREF : ".$href."<br>";
            $tata = Http::extract_domain_from_url($href);
            // error_log('tata : ' . $tata);

            # CHECK DOMAIN
            if($tata===$domain)
            {
                $anchor = trim($link->text());
                //echo "ANCHOR : ".$anchor."<br>";

                $rel = trim($link->attr('rel'));
                if(stripos($rel, 'nofollow')>-1)
                    $rel = 'nofollow';
                elseif(stripos($rel, 'follow')>-1)
                    $rel = 'follow';
                else
                    $rel = $meta_robots_rel;
                //echo "REL : ".$rel."<br>";
                
                $img_alt = null;
                foreach ($link->getNode()->getELementsByTagName('img') as $image)
                {
                    $img_alt = $image->getAttribute('alt');
                }
                //echo "IMG ALT : ".$img_alt."<br>";

                # ADD NEW BACKLINK
                $backlinks[] = [
                    'url' => $href,
                    'anchor' => $anchor,
                    'rel' => $rel,
                    'img_alt' => $img_alt
                ];
            }
        }
        return $backlinks;
    }

    /**
     * List canonical properties (index|noindex, follow|nofollow, all|none)
     *
     * @param string canonical_content
     * @return array canonicals
     *
     * @todo : what the fuck when index & noindex in same (and follow & nofollow ...)
     *
     */
    public static function get_canonicals($canonical_content)
    {
        # Short way
        //return array_map('strtolower', array_map('trim', explode(',', $canonical_content) ) );
        
        // $canonicals = [];
        // $canonicals = array_map('strtolower', array_map('trim', explode(',', $canonical_content) ) );
        // $canonicals = array_map(function($a){
        //     if(stripos($a, " ")>-1)
        //         return "";
        //     return $a;
        // }, $canonicals);
        // return $canonicals;

        # Long way (handles missing comma)
        $canonicals = [];

        if(stripos($canonical_content, 'nofollow')>-1)
        {
            $canonicals[] = 'nofollow';
        }
        if(stripos($canonical_content, 'follow')>-1)
        {
            $canonicals[] = 'follow';
        }
        if(stripos($canonical_content, 'index')>-1)
        {
            $canonicals[] = 'index';
        }
        if(stripos($canonical_content, 'noindex')>-1)
        {
            $canonicals[] = 'noindex';
        }

        if(stripos($canonical_content, 'all')>-1)
        {
            $canonicals[] = 'index';
            $canonicals[] = 'follow';
        }
        if(stripos($canonical_content, 'none')>-1)
        {
            $canonicals[] = 'noindex';
            $canonicals[] = 'nofollow';
        }
        

        return $canonicals;
    }
    /**
     * @param string $html
     * @return array canonicals Array containing possible combination of :nofollow, follow, index, noindex
     */
    /*function get_canonicals($html)
    {
        $canonicals = [];
        
        $doc = new \H0gar\Xpath\Doc($html, 'html');
        $meta_robots = $doc->item('//meta[@name="robots"]/@content');
        $meta_robots_text = strtolower($meta_robots->text());
        //echo $meta_robots_text."<br>";

        if(stripos($meta_robots_text, 'nofollow')>-1)
            $canonicals[] = 'nofollow';
        if(stripos($meta_robots_text, 'follow')>-1)
            $canonicals[] = 'follow';
        if(stripos($meta_robots_text, 'index')>-1)
            $canonicals[] = 'index';
        if(stripos($meta_robots_text, 'noindex')>-1)
            $canonicals[] = 'noindex';
        
        return $canonicals;
    }*/

    /**
     * Replaces http:// by https://
     * 
     * @param string $html
     * @return string $html
     */
    public static function set_href_ssl($html)
    {
        // if(strpos($domain, 'http://')===0)
        // {
        //     $domain_ssl = str_replace_first('http://', 'https://', $domain);

        //     $html = str_replace($domain, $domain_ssl, $html);
        // }

        return str_replace('http://', 'https://', $html);
    }

    /**
     * Takes an HTML pages, search and replace charset by UTF-8
     *
     * @param string $html
     * @return string $html
     */
    public static function set_charset_utf_8($html)
    {
        $myDom = new \DOMDocument;
        @$myDom->loadHTML($html);
        $xpath = new \DOMXPath($myDom);

        # XHTML
        $QueryResults = $xpath->query('//meta[@http-equiv="Content-Type"]');// /@content
        foreach ($QueryResults as $QueryResult)
        {
            $content = $QueryResult->getAttribute('content');
            //error_log('CHARSET XHTML : ' . $content);
            $QueryResult->setAttribute('content', "text/html; charset=UTF-8");
        }
        # HTML5
        $QueryResults = $xpath->query('//meta');// /@charset
        foreach ($QueryResults as $QueryResult)
        {
            $charset = $QueryResult->getAttribute('charset');
            //error_log('CHARSET HTML5 : ' . $charset);
            if(isset($charset) && !empty($charset))
            {
                $QueryResult->setAttribute('charset', "UTF-8");
            }
        }

        $new_html = $myDom->saveHTML();
        if($new_html!==FALSE)
        {
            $html = $new_html;
        }
        
        return $html;
    }


    /**
     * Remove external links from html 
     *
     * @param string $html HTML page to parse
     * @param string $domain_name Domain Name (url to keep)
     *
     * @return string $Html : cleaned HTML with external links removed but content inside link (text, image) kept
     *
     */
    public static function remove_external_links($html, $domain_name)
    {
        $new_html = "";
        try
        {
            $dom = new \DOMDocument();
            @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
            $xPath = new \DOMXPath($dom);
            
            foreach ($xPath->query("//a[not(contains(@href,'".$domain_name."')) and not(contains(@href,'#'))]") as $link)
            {
                while($link->hasChildNodes())
                {
                    $child = $link->removeChild($link->firstChild);
                    $link->parentNode->insertBefore($child, $link);
                }
                $link->parentNode->removeChild($link);
            }

            $html = $dom->saveHTML();
        }
        catch(Exception $e)
        {
            error_log("remove_external_links : ".$e);
        }
        return $html;
    }

    /**
     * Remove <script> and <style> tags in HTML
     *
     * We need to use a regular expression and not Xpath because invalid HTML crashed on Xpath requests
     *
     * @param string $html
     *
     * @return string html$
     * @link http://stackoverflow.com/questions/7130867/remove-script-tag-from-html-content
     */
    public static function remove_script_style($html)
    {
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html) ? : $html;
        $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html) ? : $html;

        return $html;
    }

    /**
     * Extract body content
     *
     * @param string $html
     *
     * @return string $extracted_text
     *
     */
    public static function extract_body_content($html)
    {
        $extracted_text = '';
        
        $xpath_query = '//body//*[not(self::script) and not(self::pre) and not(self::code) and not(self::style)]/text()';
        $items = Xpath::extract_all($html, $xpath_query);
        
        # REMOVE EMPTY LINE
        $items = array_filter($items);
        $items = array_map('trim', $items);

        # IMPLODE
        $extracted_text = implode("\n", $items);

        return $extracted_text;
    }

    /**
     * A SPA website sometimes contains meta
     * <meta name="fragment" content="!">
     * to tell search engine to request another URL to get 
     * populated HTML webpage instead of ajax populated page
     *
     * @param string $html
     * @return bool $is_spa
     */
    public static function is_spa($html)
    {
        $is_spa = false;

        $myDom = new \DOMDocument;
        @$myDom->loadHTML($html);
        $xpath = new \DOMXPath($myDom);

        # HTML5
        $QueryResults = $xpath->query('//meta[@name="fragment"]');// /@content
        foreach ($QueryResults as $QueryResult)
        {
            $content = $QueryResult->getAttribute('content');
            //error_log('CHARSET HTML5 : ' . $charset);
            if(isset($content) && $content==='!')
            {
                //error_log('is SPA HTML 5');
                $is_spa = true;
            }
        }
        
        return $is_spa;
    }

    /**
     * Get All links from HTML page
     *
     * @param string $html
     * @return array $links
     */
    public static function get_links($html)
    {
        $links = [];

        $myDom = new \DOMDocument;
        @$myDom->loadHTML($html);
        $xpath = new \DOMXPath($myDom);

        # HTML5
        $QueryResults = $xpath->query('//a');
        foreach ($QueryResults as $QueryResult)
        {
            $links[] = [
                'href' => $QueryResult->getAttribute('href'),
                'title' => $QueryResult->getAttribute('title'),
                'rel' => $QueryResult->getAttribute('rel'),
                'target' => $QueryResult->getAttribute('target'),
            ];
        }
        
        return $links;
    }

    /**
     * Get All External Links of given webpage
     *
     * @param string $html
     * @param string $url Webpage URL or Webpage domain
     * @return array $external_links
     */
    public static function get_external_links($html, $url)
    {
        $external_links = [];

        $domain = Http::extract_domain_from_url($url);

        $links = Html::get_links($html);
        foreach ($links as $key => $link)
        {
            $href = $link['href'];
            if($href!=='' && substr($href, 0, 1)!=='#' && (HString::starts_with($href, 'http://') || HString::starts_with($href, 'https://') || HString::starts_with($href, '//')))
            {
                $domain_link = Http::extract_domain_from_url($href);
                // error_log($domain_link.' ? '.$domain);
                if($domain_link!==$domain)
                {
                    $external_links[] = $link;        
                }
            }
        }

        return $external_links;
    }

    /**
     * Get All Internal Links of given webpage
     *
     * @param string $html
     * @param string $url Webpage URL or Webpage domain
     * @return array $internal_links
     */
    public static function get_internal_links($html, $url)
    {
        $internal_links = [];

        $domain = Http::extract_domain_from_url($url);

        $links = Html::get_links($html);
        foreach ($links as $key => $link)
        {
            $href = $link['href'];
            $domain_link = Http::extract_domain_from_url($href);
            if(substr($href, 0, 1)==='#' || substr($href, 0, 1)==='/' || $domain_link===$domain)
            {
                $internal_links[] = $link;        
            }
        }

        return $internal_links;
    }

    /**
     * Get All images from HTML page
     *
     * @param string $html
     * @return array $images
     */
    public static function get_images($html)
    {
        $images = [];

        $myDom = new \DOMDocument;
        @$myDom->loadHTML($html);
        $xpath = new \DOMXPath($myDom);

        # HTML5
        $QueryResults = $xpath->query('//img');
        foreach ($QueryResults as $QueryResult)
        {
            $images[] = [
                'src' => $QueryResult->getAttribute('src'),
                'alt' => $QueryResult->getAttribute('alt'),
            ];
        }
        
        return $images;
    }


    /* ****************************************************** */
    /* ****************************************************** */
    /*                           EMAILS                       */
    /* ****************************************************** */
    /* ****************************************************** */

    /**
     * @param string $ct
     * @return string $sort
     */
    private static function DJKeySort($ct)
    {
        $sort='';
        $chaine = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        srand((double)microtime()*1000000); 
        for($i=0; $i<$ct; $i++)
        { 
            $sort .= $chaine[rand()%strlen($chaine)];
        }
        return $sort;
    }

    /**
     * Cette fonction permet d'encoder un mail dans une page web et de pouvoir cliquer dessus sans qu'il soit récupérable par les robots spammeurs.
     * 
     * @param string $str
     * @return string
     */
    public static function DJNikMail($str)
    {
        $Js2 = '';
        if($str!==null && $str!=='')
        {
            $Str_a = Html::DJKeySort(rand(10,35));
            $Str_b = Html::DJKeySort(rand(10,35));
            $Str_e = Html::DJKeySort(rand(10,35));
            $Str_f = Html::DJKeySort(rand(10,35));
            $Str_h = Html::DJKeySort(rand(10,35));
            $Str_i = Html::DJKeySort(rand(10,35));
            $Str_x = Html::DJKeySort(rand(10,35));
            $Str_c = Html::DJKeySort(rand(10,35));
            $str = str_rot13($str); 
            $Js1 = str_rot13('<a href="mailto:'.$Str_h.'" rel=\"'.$Str_h.'\" class="email">'.$Str_h.'</a>');
            $Js2 = '<span style="width:auto !important;" id="'.$Str_f.'"></span>'."\r\n".'<script type="text/javascript">'."\r\n".''.$Str_a.'=new RegExp("('.rawurlencode(str_rot13(''.$Str_h.'')).')","g");'."\r\n".''.$Str_b.'=decodeURIComponent("'.(rawurlencode($Js1)).'".replace('.$Str_a.',"'.rawurlencode(str_replace(".",''.$Str_x.'',$str)).'"));'."\r\n".''.$Str_e.'='.$Str_b.'.replace(/[a-zA-Z]/g, function('.$Str_c.'){return String.fromCharCode(('.$Str_c.'<="Z"?90:122)>=('.$Str_c.'='.$Str_c.'.charCodeAt(0)+13)?'.$Str_c.':'.$Str_c.'-26);});'. "\r\n".''.$Str_i.'='.$Str_e.'.replace(/'.str_rot13($Str_x).'/g,".");'."\r\n".'document.getElementById("'.$Str_f.'").innerHTML='.$Str_i.';'."\r\n".'</script>';
        }
        return $Js2;
    }
}
