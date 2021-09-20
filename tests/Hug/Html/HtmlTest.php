<?php

# For PHP7
// declare(strict_types=1);

// namespace Hug\Tests\Text;

use PHPUnit\Framework\TestCase;

use Hug\Html\Html as Html;

/**
 *
 */
final class HtmlTest extends TestCase
{
	public $html;
    public $html_spa;

    public $html_links;
    public $url_links;

	function setUp(): void
	{
		$data = realpath(__DIR__ . '/../../../data');
		$this->html = file_get_contents($data . '/free.fr.html');
        $this->html_spa = file_get_contents($data . '/spa.html');

        $this->html_links = file_get_contents($data . '/hugo.maugey.fr.html');
        $this->url_links = 'https://hugo.maugey.fr/developeur-web/Linux?_escaped_fragment_';

        $this->html_iframes = file_get_contents($data . '/www.customdom.com.html');
	}

    /* ************************************************* */
    /* ************* Html::is_external_link ************ */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsExternalLink()
    {
		$domain = 'hugo.maugey.fr';
		$link = 'http://maugey.fr/coucou.html';
    	$test = Html::is_external_link($domain, $link);
        $this->assertTrue($test);
    }

    /**
     *
     */
    public function testCannotIsExternalLink()
    {
		$domain = 'maugey.fr';
		$link = 'http://maugey.fr/coucou.html';
    	$test = Html::is_external_link($domain, $link);
        $this->assertFalse($test);
    }

    /* ************************************************* */
    /* ************** Html::is_domain_link ************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsDomainLink()
    {
		$domain = 'maugey.fr';
		$link = 'http://maugey.fr/coucou.html';
    	$test = Html::is_domain_link($domain, $link);
        $this->assertTrue($test);
    }

    /**
     *
     */
    public function testCannotIsDomainLink()
    {
		$domain = 'tata.maugey.fr';
		$link = 'http://maugey.fr/coucou.html';
    	$test = Html::is_domain_link($domain, $link);
        $this->assertFalse($test);
    }

    /* ************************************************* */
    /* ********* Html::replace_rel_to_abs_paths ******** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanReplaceRelToAbsPaths()
    {
		$page_url = 'http://portail.free.fr/m/';
    	$test = Html::replace_rel_to_abs_paths($this->html, $page_url);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* **************** Html::rel_to_abs *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanReplaceRelToAbs()
    {
		$rel = '/js/script.js';
		$base = 'http://portail.free.fr/m/';
    	$test = Html::rel_to_abs($rel, $base);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ************** Html::find_backlinks ************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanFindBacklinks()
    {
		$domain = 'www.free.fr';
    	$test = Html::find_backlinks($this->html, $domain);
        $this->assertIsArray($test);
    }

    /* ************************************************* */
    /* ************** Html::get_canonicals ************* */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetCanonicals()
    {
		$canonical_content = 'index, follow, noindex, noarchive';
    	$test = Html::get_canonicals($canonical_content);
        $this->assertIsArray($test);
    }

    /* ************************************************* */
    /* ************** Html::set_href_ssl *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanSetHrefSsl()
    {
    	$test = Html::set_href_ssl($this->html);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ************ Html::set_charset_utf_8 ************ */
    /* ************************************************* */

    /**
     *
     */
    public function testCanSetCharsetUtf8()
    {
    	$test = Html::set_charset_utf_8($this->html);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ********** Html::remove_external_links ********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRemoveExternalLinks()
    {
		$domain = 'free.fr';
    	$test = Html::remove_external_links($this->html, $domain);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* *********** Html::remove_script_style *********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanRemoveScriptStyle()
    {
    	$test = Html::remove_script_style($this->html);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* *********** Html::extract_body_content ********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanExtractBodyContent()
    {
    	$test = Html::extract_body_content($this->html);
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ****************** Html::is_spa ***************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanIsSpa()
    {
        $test = Html::is_spa($this->html);
        $this->assertIsBool($test);
        $this->assertFalse($test);

        $test = Html::is_spa($this->html_spa);
        $this->assertIsBool($test);
        $this->assertTrue($test);

    }

    /* ************************************************* */
    /* *********** Html::add_escaped_fragment ********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanAddEscapedFragment()
    {
        $test = Html::add_escaped_fragment('https://hugo.maugey.fr/developeur-web');
        $this->assertIsString($test);
    }

    /* ************************************************* */
    /* ***************** Html::get_links *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetLinks()
    {
        $test = Html::get_links($this->html_links);
        $this->assertIsArray($test);
        $this->assertTrue(count($test)===59);
    }

    /* ************************************************* */
    /* **************** Html::get_iframes ************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetIframes()
    {
        $test = Html::get_iframes($this->html_iframes);
        $this->assertIsArray($test);
        $this->assertTrue(count($test)===2);
    }


    /* ************************************************* */
    /* ************ Html::get_external_links *********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetExternalLinks()
    {
        $test = Html::get_external_links($this->html_links, $this->url_links);
        $this->assertIsArray($test);
        $this->assertTrue(count($test)===3);
    }

    /* ************************************************* */
    /* ************ Html::get_internal_links *********** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetInternalLinks()
    {
        $test = Html::get_internal_links($this->html_links, $this->url_links);
        $this->assertIsArray($test);
        $this->assertTrue(count($test)===52);
    }

    /* ************************************************* */
    /* **************** Html::get_images *************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanGetImages()
    {
        $test = Html::get_images($this->html_links);
        $this->assertIsArray($test);
        $this->assertTrue(count($test)===29);
    }

    /* ************************************************* */
    /* **************** Html::DJNikMail **************** */
    /* ************************************************* */

    /**
     *
     */
    public function testCanDJNikMail()
    {
		$str = 'tatayoyo@free.fr';
    	$test = Html::DJNikMail($str);
        $this->assertIsString($test);
    }

}
