<?php
$type = 'formatimage';//le format d'image a afficher

$typeList = Zend_Registry::getInstance()->config['imageresizer']['formats']; //renvoie un array contenant les tailles indexés par les format
/**
exemple : 
[smallarticle][width]
[smallarticle][height]
[smallarticle][full]      //width pour couper la sur la longueur. definir a height pour couper sur la hauteur
[smallarticle][cropped] //booleen pour couper l'image
*/
if (isset($typeList['original'])) {
	unset($typeList['original']);
}

if (!array_key_exists($type, $typeList)) {
	$type = 'original';
}
try {
	$thumb = PhpThumbFactory::create($file);
} catch (Exception $e) {
	$file = $typeList[$type]['default'];
	$thumb = PhpThumbFactory::create($file);
}
if ($type != 'original') {
	$dimensions = $thumb->getCurrentDimensions();
	$ratioOriginal = $dimensions['width'] / $dimensions['height']; 
	$ratioDest = $typeList[$type]['width'] / $typeList[$type]['height'];
	if ($typeList[$type]['full'] != 'width' && $typeList[$type]['full'] != 'height') {
		if ($ratioDest < $ratioOriginal) {
			$height = $typeList[$type]['height'];
			$width = $typeList[$type]['height'] * $ratioOriginal;
			if ($width > $typeList[$type]['width'] && !$typeList[$type]['cropped']){
				$width = $typeList[$type]['width'];
				$height = $typeList[$type]['width'] * (1 / $ratioOriginal);
			}
		} elseif ($ratioDest >= $ratioOriginal) {
			$width = $typeList[$type]['width'];
			$height = $typeList[$type]['width'] * (1 / $ratioOriginal);
			if ($height > $typeList[$type]['height'] && !$typeList[$type]['cropped']){
				$height = $typeList[$type]['height'];
				$width = $typeList[$type]['height'] * $ratioOriginal;
			}
		}
	}
	if ($typeList[$type]['full'] == 'auto') {
		$typeList[$type]['full'] = 'width';
	}
	if ($typeList[$type]['full'] == 'width') {
		$width = $typeList[$type]['width'];
		$height = $typeList[$type]['width'] * (1 / $ratioOriginal);
		$thumb->setOptions(array(
			'resizeUp' => true,
		));
	} else if ($typeList[$type]['full'] == 'height') {
		$height = $typeList[$type]['height'];
		$width = $typeList[$type]['height'] * $ratioOriginal;
		$thumb->setOptions(array(
			'resizeUp' => true,
		));
	}
	$thumb->resize($width, $height);
	if ($typeList[$type]['cropped']) {
		$thumb->cropFromCenter($typeList[$type]['width'], $typeList[$type]['height']);
	}
}
$thumb->show();
