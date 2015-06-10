<?php

/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */

use Yii;

$I = new AcceptanceTester($scenario);
$I->wantTo('ensure the standard MATA CMS header is present in all files');



echo Yii::getAlias("@vendor");
exit;
$pathToModuleRoot = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..";
$dir =  $pathToModuleRoot;


iterateThroughDir($dir, $this);

 exit;


function iterateThroughDir($dir, $context) {

	foreach(glob($dir . DIRECTORY_SEPARATOR . "*") as $file) {

		if (is_dir($file) && isIgnoredDir($file) == false) {
			iterateThroughDir($file, $context);  
		} else if (stripos($file, ".php") !== false) {

			if (ensureHeaderPresent($file)) {
				codecept_debug($file . " OK");  
			} else {
				codecept_debug(sprintf("Incorrect header found in %s", $file)); 
				// $context->fail(sprintf("Incorrect header found in %s", $file));
			}
			
		} else {
			codecept_debug($file . " Skipped");  
		}

	
	}
}


function isIgnoredDir($dir) {
	$ignoredFolders = ["matacms-content-block"];

	codecept_debug("TESTING " . $dir); 

	foreach ($ignoredFolders as $ignoredFolder) {
		if (stripos($dir, $ignoredFolder) !== false) {
			codecept_debug("Skipping whole folder " . $dir);  
			return true;
		}
	} 

	return false;

}

// function checkIfStringEndsWith($string, $ending) {
// 	return strrpos($string, $ending) == strlen($string) - strlen($ending);
// }

function ensureHeaderPresent($file) {

	$header = <<<EOT
<?php
/**
 * @link http://www.matacms.com/
 * @copyright Copyright (c) 2015 Qi Interactive Limited
 * @license http://www.matacms.com/license/
 */
EOT;

	return strpos(file_get_contents($file), $header) === 0;
}



