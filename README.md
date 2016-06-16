[![Latest Stable Version](https://poser.pugx.org/femtopixel/crop/v/stable)](https://packagist.org/packages/femtopixel/crop) 
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%205.3-8892BF.svg?style=flat-square)](https://php.net/)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%207.0-8892BF.svg?style=flat-square)](https://php.net/)
[![Build status](https://travis-ci.org/femtopixel/crop.svg)](https://travis-ci.org/femtopixel/crop)
[![Dependency Status](https://www.versioneye.com/user/projects/575fe512433d18002c19d66d/badge.svg?style=flat)](https://www.versioneye.com/user/projects/575fe512433d18002c19d66d)
[![Code Climate](https://codeclimate.com/github/femtopixel/crop/badges/gpa.svg)](https://codeclimate.com/github/femtopixel/crop)
[![Test Coverage](https://codeclimate.com/github/femtopixel/crop/badges/coverage.svg)](https://codeclimate.com/github/femtopixel/crop/coverage)
[![Issue Count](https://codeclimate.com/github/femtopixel/crop/badges/issue_count.svg)](https://codeclimate.com/github/femtopixel/crop)
[![License](https://poser.pugx.org/femtopixel/crop/license)](https://packagist.org/packages/femtopixel/crop)

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