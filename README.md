[![Latest Stable Version](https://poser.pugx.org/femtopixel/crop/v/stable)](https://packagist.org/packages/femtopixel/crop) 
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Build Status](https://scrutinizer-ci.com/g/femtopixel/crop/badges/build.png?b=master)](https://scrutinizer-ci.com/g/femtopixel/crop/build-status/master)
[![Dependency Status](https://www.versioneye.com/user/projects/575fe512433d18002c19d66d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/575fe512433d18002c19d66d)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/femtopixel/crop/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/femtopixel/crop/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/femtopixel/crop/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/femtopixel/crop/?branch=master)
[![License](https://poser.pugx.org/femtopixel/crop/license)](https://packagist.org/packages/femtopixel/crop)
[![Bitcoin donation](https://github.com/jaymoulin/jaymoulin.github.io/raw/master/btc.png "Bitcoin donation")](https://m.freewallet.org/id/374ad82e/btc)
[![Litecoin donation](https://github.com/jaymoulin/jaymoulin.github.io/raw/master/ltc.png "Litecoin donation")](https://m.freewallet.org/id/374ad82e/ltc)
[![PayPal donation](https://github.com/jaymoulin/jaymoulin.github.io/raw/master/ppl.png "PayPal donation")](https://www.paypal.me/jaymoulin)


Crop
====

This component will resize images following configuration rules.
allows only defined format

## Installation

```
composer require femtopixel/crop
```

## Usage

```php
<?php
$formats = array(
    'mini' => array(
        'width' => 100,
        'height' => 100,
        'full' => 'cropped',
    ),
);
$image = new \FemtoPixel\Crop\Crop('/path/to/file.png', 'mini');
$image->setFormatsFromArray($formats)->render();
```

You can define as many format that you want!

- First parameter is path to your file to display
- Second (optional) is the format you want to display in (default : 'original' (no modification))
- Third (optional) is path to default image displayed if path in first parameter doesn't exist. This file will be displayed in requested format. (default : 'src/missing.png')
 
## Configuration

Each format given in method `setFormatsFromArray` must define `width`, `height` and optional `full` index.
 
`full` can be configured to 4 values depending on attended rendering :

 - `none` : No cropping, no resize. Will render image with original file dimensions.
 - `cropped` : Crop the rendered image to be exactly dimensions defined in configuration.
 - `height` : Resize the image without distortion to have height to value defined in configuration.
 - `width` : Resize the image without distortion to have width to value defined in configuration.
