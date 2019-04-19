# php-html

This librairy provides utilities function to ease HTML manipulation

[![Build Status](https://travis-ci.org/hugsbrugs/php-html.svg?branch=master)](https://travis-ci.org/hugsbrugs/php-html)
[![Coverage Status](https://coveralls.io/repos/github/hugsbrugs/php-html/badge.svg?branch=master)](https://coveralls.io/github/hugsbrugs/php-html?branch=master)

## Install

Install package with composer
```
composer require hugsbrugs/php-html
```

In your PHP code, load librairy
```php
require_once __DIR__ . '/vendor/autoload.php';
use Hug\Html\Html as Html;
```

## Usage

Test if a link is external from given domain
```php
$bool = Html::is_external_link($domain = 'maugey.fr', $link = 'http://maugey.fr/coucou.html');
```

Test if a link is from given domain
```php
$bool = Html::is_domain_link($domain = 'tata.maugey.fr', $link = 'http://maugey.fr/coucou.html');
```

Replace all relatives path in HTML (script, style, img) with absolute ones with given page URL 
```php
$html = Html::replace_rel_to_abs_paths($html, $page_url = 'http://portail.free.fr/m/');
```

Build an absolute link from relative link and page URL 
```php
$html = Html::rel_to_abs($rel = '/js/script.js', $base = 'http://portail.free.fr/m/');
```

Return array of backlinks from given HTML page and domain
```php
$html = Html::find_backlinks($html, $domain = 'www.free.fr');
```

Returns array of canonicals from string
```php
$array = Html::get_canonicals($canonical_content = 'index, follow, noindex, noarchive');
```

Replaces all links in HTML by https
```php
$html = Html::set_href_ssl($html);
```

Set UTF-8 Charset in HTML page with correct syntax depending on Doctype
```php
$html = Html::set_charset_utf_8($html);
```

Remove all external link from HTML with given domain
```php
$html = Html::remove_external_links($html, $domain = 'free.fr');
```

Remove all <sccript> and <style> tags from HTML
```php
$html = Html::remove_script_style($html);
```

Extract <body> content from HTML page
```php
$body = Html::extract_body_content($html);
```

Check for <meta name="fragment" content="!"> tag
```php
$is_spa = Html::is_spa($html);
```


Get all links
```php
$links = Html::get_links($html);
```

Get external links
```php
$external_links = Html::get_external_links($html);
```

Get internal links
```php
$internal_links = Html::get_internal_links($html);

```
Get images
```php
$images = Html::get_images($html);
```


Obfuscate email to be incorporated in HTML
```php
$email = Html::DJNikMail($str = 'tatayoyo@free.fr');
```

## Unit Tests

```
phpunit --bootstrap vendor/autoload.php tests
```

## Author

Hugo Maugey [visit my website ;)](https://hugo.maugey.fr)