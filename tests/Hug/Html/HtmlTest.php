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

	function __construct()
	{
		$data = realpath(__DIR__ . '/../../../data');
		$this->html = file_get_contents($data . '/free.fr.html');

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
        $this->assertInternalType('string', $test);
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
        $this->assertInternalType('string', $test);
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
        $this->assertInternalType('array', $test);
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
        $this->assertInternalType('array', $test);
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
        $this->assertInternalType('string', $test);
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
        $this->assertInternalType('string', $test);
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
        $this->assertInternalType('string', $test);
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
        $this->assertInternalType('string', $test);
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
        $this->assertInternalType('string', $test);
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
        $this->assertInternalType('string', $test);
    }

}
