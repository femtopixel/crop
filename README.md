[![Latest Stable Version](https://poser.pugx.org/femtopixel/image-resizer/v/stable)](https://packagist.org/packages/femtopixel/image-resizer) 
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.3-8892BF.svg?style=flat-square)](https://php.net/)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Build status](https://travis-ci.org/femtopixel/image-resizer.svg)](https://travis-ci.org/femtopixel/image-resizer)
[![Dependency Status](https://www.versioneye.com/user/projects/575fde04433d18005179251a/badge.svg?style=flat)](https://www.versioneye.com/user/projects/575fde04433d18005179251a)
[![License](https://poser.pugx.org/femtopixel/image-resizer/license)](https://packagist.org/packages/femtopixel/image-resizer)

Image Resizer
===

This component will resize images following configuration rules.
allows only defined format

## Installation


```
composer require femtopixel/image-resizer
```

## Usage

```php
<?php
$formats = array(
    'mini' => array(
        'width' => 100,
        'height' => 100,
        'full' => 'auto',
    ),
);
$image = new \FemtoPixel\ImageResizer\ImageResizer('/path/to/file.png', 'mini');
$image->setFormatsFromArray($formats)->render();
```

You can define as many format that you want!