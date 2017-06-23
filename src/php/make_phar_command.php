#!/usr/bin/env php
<?php
/*
 * This file is part of the pdf2xml package.
 * https://github.com/sbignet/pdf2xml
 * (c) Stéphane Bignet <github@bigstef.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$phar = new Phar('pdf2xml.phar');
$phar['pdf2xml.xsd']     = file_get_contents('pdf2xml.xsd');
$phar['pdf2xml_lib.php'] = file_get_contents('pdf2xml_lib.php');
$phar['pdf2xml.php']     = file_get_contents('pdf2xml.php');

$stub = <<<"EOT"
#!/usr/bin/env php
<?php
Phar::mapPhar('pdf2xml.phar');
set_include_path('phar://pdf2xml.phar'.PATH_SEPARATOR.get_include_path());
require('pdf2xml.php');
__HALT_COMPILER();
EOT;

$phar->setStub($stub);
system('chmod +x pdf2xml.phar');