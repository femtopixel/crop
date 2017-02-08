<?php
namespace FemtoPixel\Crop\Tests;

use FemtoPixel\Crop\Format;
use FemtoPixel\Crop\ResizeEngine;

class CropTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $file = '/var/www/image.png';
        $crop = new \FemtoPixel\Crop\Crop($file);
        $this->assertSame($file, $crop->getFilePath());
        $this->assertSame(\FemtoPixel\Crop\Crop::FORMAT_ORIGINAL, $crop->getFormat());
        $this->assertStringEndsWith('missing.png', $crop->getDefaultImage());

        $defaultImage = '/new/path/default.png';
        $crop = new \FemtoPixel\Crop\Crop($file, 'testFormat', $defaultImage);
        $this->assertSame('testFormat', $crop->getFormat());
        $this->assertSame($defaultImage, $crop->getDefaultImage());
    }

    public function testSetGetAvailableFormat()
    {
        $file = '/var/www/image.png';
        $crop = new \FemtoPixel\Crop\Crop($file);
        $this->assertFalse($crop->isAvailableFormat('test'));
        $this->assertFalse($crop->isAvailableFormat('bob'));
        $mockFormat = $this->getMockBuilder('\FemtoPixel\Crop\Format')
            ->setMethods(null)
            ->setConstructorArgs(array(10, 10))
            ->getMock();
        /** @var $mockFormat \FemtoPixel\Crop\Format */
        $this->assertSame($crop, $crop->setAvailableFormat('test', $mockFormat));
        $this->assertTrue($crop->isAvailableFormat('test'));
        $this->assertFalse($crop->isAvailableFormat('bob'));
        $this->assertSame($crop, $crop->setFormatsFromArray(array('bob' => array('width' => 10, 'height' => 10))));
        $this->assertTrue($crop->isAvailableFormat('bob'));
        $this->assertFalse($crop->isAvailableFormat('test'));
    }

    public function testSetGet()
    {
        $file = '/var/www/image.png';
        $defaultImage = '/new/path/default.png';

        $crop = new \FemtoPixel\Crop\Crop($file);

        $this->assertSame($file, $crop->getFilePath());
        $this->assertSame($crop, $crop->setFilePath($defaultImage));
        $this->assertSame($defaultImage, $crop->getFilePath());

        $this->assertSame(\FemtoPixel\Crop\Crop::FORMAT_ORIGINAL, $crop->getFormat());
        $this->assertSame($crop, $crop->setFormat('test'));
        $this->assertSame('test', $crop->getFormat());

        $this->assertStringEndsWith('missing.png', $crop->getDefaultImage());
        $this->assertSame($crop, $crop->setDefaultImage($file));
        $this->assertSame($file, $crop->getDefaultImage());
        $this->assertSame($crop, $crop->setDefaultImage(null));
        $this->assertStringEndsWith('missing.png', $crop->getDefaultImage());
        $this->assertSame($crop, $crop->setDefaultImage($file));
        $this->assertSame($file, $crop->getDefaultImage());
        $this->assertSame($crop, $crop->setDefaultImage());
        $this->assertStringEndsWith('missing.png', $crop->getDefaultImage());
    }

    public function testGetAvailableFormatAll()
    {
        $crop = new \FemtoPixel\Crop\Crop('/tmp');
        $mockFormat = $this->getMockBuilder('\FemtoPixel\Crop\Format')
            ->setMethods(null)
            ->setConstructorArgs(array(10, 10))
            ->getMock();
        /** @var $mockFormat \FemtoPixel\Crop\Format */
        $this->assertSame(array(), $crop->getAvailableFormat());
        $this->assertSame(array(), $crop->getAvailableFormat(null));
        $this->assertSame($crop, $crop->setAvailableFormat('test', $mockFormat));
        $this->assertSame(array('test' => $mockFormat), $crop->getAvailableFormat(null));
    }

    public function testGetAvailableFormatExists()
    {
        $crop = new \FemtoPixel\Crop\Crop('/tmp');
        $mockFormat = $this->getMockBuilder('\FemtoPixel\Crop\Format')
            ->setMethods(null)
            ->setConstructorArgs(array(10, 10))
            ->getMock();
        /** @var $mockFormat \FemtoPixel\Crop\Format */
        $this->assertSame($crop, $crop->setAvailableFormat('test', $mockFormat));
        $this->assertSame($mockFormat, $crop->getAvailableFormat('test'));
    }

    public function testFailGetAvailableFormatWhenNonExistingFormat()
    {
        $crop = new \FemtoPixel\Crop\Crop('/tmp');
        $this->expectException(\FemtoPixel\Crop\Exception\FormatNotAvailable::class);
        $this->expectExceptionMessage("Requested 'bob' format is not available");
        $crop->getAvailableFormat('bob');
    }

    public function testGetComputedDefaultFilePath()
    {
        $reflectionMethod = new \ReflectionMethod(\FemtoPixel\Crop\Crop::class, 'getComputedDefaultFilePath');
        $reflectionMethod->setAccessible(true);

        $crop = $this->getMockBuilder(\FemtoPixel\Crop\Crop::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getFormat', 'getDefaultImage', 'getAvailableFormat'))
            ->getMock();
        $crop->expects($this->exactly(3))
            ->method('getFormat')
            ->willReturnOnConsecutiveCalls(
                \FemtoPixel\Crop\Crop::FORMAT_ORIGINAL,
                'test',
                'test'
            );
        $crop->expects($this->exactly(2))
            ->method('getDefaultImage')
            ->willReturnOnConsecutiveCalls("image1", "image2");
        $crop->expects($this->exactly(2))
            ->method('getAvailableFormat')
            ->withConsecutive(array($this->equalTo('test')), array($this->equalTo('test')))
            ->willReturnOnConsecutiveCalls(
                array(),
                array(\FemtoPixel\Crop\Format::DEFAULT_IMAGE => 'image3')
            );
        /** @var $crop \FemtoPixel\Crop\Crop */
        $this->assertSame('image1', $reflectionMethod->invoke($crop));
        $this->assertSame('image2', $reflectionMethod->invoke($crop));
        $this->assertSame('image3', $reflectionMethod->invoke($crop));
    }

    public function testResize()
    {
        $filePath = '/var/www/imagePath.png';
        $info = array(
            \FemtoPixel\Crop\ResizeEngine::INFO_WIDTH => 1000,
            \FemtoPixel\Crop\ResizeEngine::INFO_HEIGHT => 2000,
        );
        $expectedResults = array(
            array(100, 200, false),
            array(300, 600, false),
            array(300, 600, false),
            array(700, 800, true),
        );
        $reflectionMethod = new \ReflectionMethod(\FemtoPixel\Crop\Crop::class, 'resize');
        $reflectionMethod->setAccessible(true);

        $formats = array(
            'format1' => array(
                Format::WIDTH => 100,
                Format::HEIGHT => 200,
            ),
            'format2' => array(
                Format::WIDTH => 300,
                Format::HEIGHT => 400,
                Format::FULL => Format::FULL_WIDTH
            ),
            'format3' => array(
                Format::WIDTH => 500,
                Format::HEIGHT => 600,
                Format::FULL => Format::FULL_HEIGHT
            ),
            'format4' => array(
                Format::WIDTH => 700,
                Format::HEIGHT => 800,
                Format::FULL => Format::FULL_CROPPED
            ),
        );

        $resizeEngine = $this->getMockBuilder(\FemtoPixel\Crop\ResizeEngine::class)->setMethods(array('resize'))->getMock();
        $resizeEngine->expects($this->exactly(4))
            ->method('resize')
            ->withConsecutive(
                array(
                    $this->equalTo($filePath),
                    $this->equalTo($expectedResults[0][0]),
                    $this->equalTo($expectedResults[0][1]),
                    $this->equalTo($expectedResults[0][2])
                ),
                array(
                    $this->equalTo($filePath),
                    $this->equalTo($expectedResults[1][0]),
                    $this->equalTo($expectedResults[1][1]),
                    $this->equalTo($expectedResults[1][2])
                ),
                array(
                    $this->equalTo($filePath),
                    $this->equalTo($expectedResults[2][0]),
                    $this->equalTo($expectedResults[2][1]),
                    $this->equalTo($expectedResults[2][2])
                ),
                array(
                    $this->equalTo($filePath),
                    $this->equalTo($expectedResults[3][0]),
                    $this->equalTo($expectedResults[3][1]),
                    $this->equalTo($expectedResults[3][2])
                )
            );
        $crop = $this->getMockBuilder(\FemtoPixel\Crop\Crop::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getFormat', 'getResizeEngine', 'getAvailableFormat'))
            ->getMock();
        $crop->expects($this->exactly(4))
            ->method('getFormat')
            ->willReturnOnConsecutiveCalls('format1', 'format2', 'format3', 'format4');
        $crop->expects($this->exactly(4))
            ->method('getAvailableFormat')
            ->withConsecutive(
                array($this->equalTo('format1')),
                array($this->equalTo('format2')),
                array($this->equalTo('format3')),
                array($this->equalTo('format4'))
            )
            ->willReturnOnConsecutiveCalls(
                $formats['format1'],
                $formats['format2'],
                $formats['format3'],
                $formats['format4']
            );
        $crop->expects($this->exactly(4))->method('getResizeEngine')->willReturn($resizeEngine);
        foreach ($expectedResults as $line) {
            $reflectionMethod->invoke($crop, $filePath, $info);
        }
    }

    public function testRender()
    {
        $filePath = '/var/www/file.png';
        $filePath2 = '/var/www/file2.png';

        $resizeEngine = $this->getMockBuilder(\FemtoPixel\Crop\ResizeEngine::class)
            ->setMethods(array('getImageInfo'))
            ->getMock();
        $resizeEngine->expects($this->exactly(4))
            ->method('getImageInfo')
            ->withConsecutive(
                array($this->equalTo($filePath2)),
                array($this->equalTo($filePath)),
                array($this->equalTo($filePath2)),
                array($this->equalTo($filePath))
            )
            ->willReturn(array(ResizeEngine::INFO_MIME => 'image/png'));
        $crop = $this->getMockBuilder(\FemtoPixel\Crop\Crop::class)
            ->disableOriginalConstructor()
            ->setMethods(
                array(
                    'getFilePath',
                    'phpFileExists',
                    'phpHeader',
                    'getComputedDefaultFilePath',
                    'getResizeEngine',
                    'getFormat',
                    'phpReadfile',
                    'resize'
                )
            )
            ->getMock();
        $crop->expects($this->exactly(4))->method('getFilePath')->willReturn($filePath);
        $crop->expects($this->exactly(4))
            ->method('phpFileExists')
            ->with($this->equalTo($filePath))
            ->willReturnOnConsecutiveCalls(false, true, false, true);
        $crop->expects($this->exactly(4))
            ->method('getFormat')
            ->willReturnOnConsecutiveCalls(
                \FemtoPixel\Crop\Crop::FORMAT_ORIGINAL,
                \FemtoPixel\Crop\Crop::FORMAT_ORIGINAL,
                'test',
                'test'
            );
        $crop->expects($this->exactly(4))
            ->method('phpHeader')
            ->withConsecutive(
                array($this->equalTo('HTTP/1.0 404 Not Found')),
                array($this->equalTo('Content-Type: image/png')),
                array($this->equalTo('Content-Type: image/png')),
                array($this->equalTo('HTTP/1.0 404 Not Found'))
            );
        $crop->expects($this->exactly(2))
            ->method('phpReadfile')
            ->withConsecutive(array($this->equalTo($filePath2)), array($this->equalTo($filePath)));
        $crop->expects($this->exactly(2))
            ->method('resize')
            ->withConsecutive(
                array(
                    $this->equalTo($filePath2),
                    $this->equalTo(array(ResizeEngine::INFO_MIME => 'image/png'))
                ),
                array(
                    $this->equalTo($filePath),
                    $this->equalTo(array(ResizeEngine::INFO_MIME => 'image/png'))
                )
            );
        $crop->expects($this->exactly(2))->method('getComputedDefaultFilePath')->willReturn($filePath2);
        $crop->expects($this->exactly(4))->method('getResizeEngine')->willReturn($resizeEngine);
        /** @var $crop \FemtoPixel\Crop\Crop */
        for ($i = 0; $i < 4; $i++) {
            $crop->render();
        }
    }
}
