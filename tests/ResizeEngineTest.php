<?php
namespace Tests;


class ResizeEngineTest extends \PHPUnit_Framework_TestCase
{
    public function testGetImageInfoSuccessWhenGif()
    {
        $filePath = '/var/www/my/image.png';

        $resource = new \stdClass();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('imagecreatefromgif', 'imagesx', 'imagesy'))
            ->getMock();
        $mockGd->expects($this->once())->method('imagecreatefromgif')->willReturn($resource);
        $mockGd->expects($this->once())->method('imagesx')->with($this->equalTo($resource))->willReturn(100);
        $mockGd->expects($this->once())->method('imagesy')->with($this->equalTo($resource))->willReturn(200);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('verifyFormatCompatibility', 'getGd'))
            ->getMock();
        $resizeEngine->expects($this->once())
            ->method('verifyFormatCompatibility')
            ->with($this->equalTo($filePath))
            ->willReturn('image/gif');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $result = array(
            \FemtoPixel\Crop\ResizeEngine::INFO_WIDTH => 100,
            \FemtoPixel\Crop\ResizeEngine::INFO_HEIGHT => 200,
            \FemtoPixel\Crop\ResizeEngine::INFO_MIME => 'image/gif',
        );
        $this->assertSame($result, $resizeEngine->getImageInfo($filePath));
    }

    public function testGetImageInfoSuccessWhenJpeg()
    {
        $filePath = '/var/www/my/image.png';

        $resource = new \stdClass();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('imagecreatefromjpeg', 'imagesx', 'imagesy'))
            ->getMock();
        $mockGd->expects($this->once())->method('imagecreatefromjpeg')->willReturn($resource);
        $mockGd->expects($this->once())->method('imagesx')->with($this->equalTo($resource))->willReturn(100);
        $mockGd->expects($this->once())->method('imagesy')->with($this->equalTo($resource))->willReturn(200);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('verifyFormatCompatibility', 'getGd'))
            ->getMock();
        $resizeEngine->expects($this->once())
            ->method('verifyFormatCompatibility')
            ->with($this->equalTo($filePath))
            ->willReturn('image/jpeg');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $result = array(
            \FemtoPixel\Crop\ResizeEngine::INFO_WIDTH => 100,
            \FemtoPixel\Crop\ResizeEngine::INFO_HEIGHT => 200,
            \FemtoPixel\Crop\ResizeEngine::INFO_MIME => 'image/jpeg',
        );
        $this->assertSame($result, $resizeEngine->getImageInfo($filePath));
    }

    public function testGetImageInfoSuccessWhenPng()
    {
        $filePath = '/var/www/my/image.png';

        $resource = new \stdClass();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('imagecreatefrompng', 'imagesx', 'imagesy'))
            ->getMock();
        $mockGd->expects($this->once())->method('imagecreatefrompng')->willReturn($resource);
        $mockGd->expects($this->once())->method('imagesx')->with($this->equalTo($resource))->willReturn(100);
        $mockGd->expects($this->once())->method('imagesy')->with($this->equalTo($resource))->willReturn(200);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('verifyFormatCompatibility', 'getGd'))
            ->getMock();
        $resizeEngine->expects($this->once())
            ->method('verifyFormatCompatibility')
            ->with($this->equalTo($filePath))
            ->willReturn('image/png');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $result = array(
            \FemtoPixel\Crop\ResizeEngine::INFO_WIDTH => 100,
            \FemtoPixel\Crop\ResizeEngine::INFO_HEIGHT => 200,
            \FemtoPixel\Crop\ResizeEngine::INFO_MIME => 'image/png',
        );
        $this->assertSame($result, $resizeEngine->getImageInfo($filePath));
    }

    public function testGetImageInfoFailsWhenSvg()
    {
        $filePath = '/var/www/my/image.png';

        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')->getMock();
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('verifyFormatCompatibility', 'getGd'))
            ->getMock();
        $resizeEngine->expects($this->once())
            ->method('verifyFormatCompatibility')
            ->with($this->equalTo($filePath))
            ->willReturn('image/svg');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $this->setExpectedException('\Exception', "Your GD installation does not support image/svg image types");
        $resizeEngine->getImageInfo($filePath);
    }

    public function testVerifyFormatCompatibilityGif()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'verifyFormatCompatibility');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('determineFormat', 'getGd'))
            ->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('gd_info'))
            ->getMock();
        $mockGd->expects($this->once())->method('gd_info')->willReturn(array('GIF Create Support' => true));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('determineFormat')
            ->with($this->equalTo($filePath))
            ->willReturn('image/gif');
        $this->assertSame('image/gif', $reflectionMethod->invoke($resizeEngine, $filePath));
    }

    public function testVerifyFormatCompatibilityJpeg()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'verifyFormatCompatibility');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('determineFormat', 'getGd'))
            ->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('gd_info'))
            ->getMock();
        $mockGd->expects($this->once())->method('gd_info')->willReturn(array('JPG Support' => true));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('determineFormat')
            ->with($this->equalTo($filePath))
            ->willReturn('image/jpeg');
        $this->assertSame('image/jpeg', $reflectionMethod->invoke($resizeEngine, $filePath));
    }

    public function testVerifyFormatCompatibilityPng()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'verifyFormatCompatibility');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('determineFormat', 'getGd'))
            ->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('gd_info'))
            ->getMock();
        $mockGd->expects($this->once())->method('gd_info')->willReturn(array('PNG Support' => true));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('determineFormat')
            ->with($this->equalTo($filePath))
            ->willReturn('image/png');
        $this->assertSame('image/png', $reflectionMethod->invoke($resizeEngine, $filePath));
    }

    public function testVerifyFormatCompatibilityJpegViaSvg()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'verifyFormatCompatibility');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('determineFormat', 'getGd'))
            ->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('gd_info'))
            ->getMock();
        $mockGd->expects($this->once())->method('gd_info')->willReturn(array('JPEG Support' => true));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('determineFormat')
            ->with($this->equalTo($filePath))
            ->willReturn('image/svg');
        $this->assertSame('image/svg', $reflectionMethod->invoke($resizeEngine, $filePath));
    }

    public function testVerifyFormatCompatibilityFails()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'verifyFormatCompatibility');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('determineFormat', 'getGd'))
            ->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('gd_info'))
            ->getMock();
        $mockGd->expects($this->once())->method('gd_info')->willReturn(array());
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('determineFormat')
            ->with($this->equalTo($filePath))
            ->willReturn('image/svg');
        $this->setExpectedException('\Exception', 'Your GD installation does not support image/svg image types');
        $reflectionMethod->invoke($resizeEngine, $filePath);
    }

    public function testDetermineFormatGif()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'determineFormat');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->with($this->equalTo($filePath))
            ->willReturn(array('mime' => 'image/gif'));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertSame('image/gif', $reflectionMethod->invoke($resizeEngine, $filePath));
    }

    public function testDetermineFormatPng()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'determineFormat');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->with($this->equalTo($filePath))
            ->willReturn(array('mime' => 'image/png'));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertSame('image/png', $reflectionMethod->invoke($resizeEngine, $filePath));
    }

    public function testDetermineFormatJpeg()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'determineFormat');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->with($this->equalTo($filePath))
            ->willReturn(array('mime' => 'image/jpeg'));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertSame('image/jpeg', $reflectionMethod->invoke($resizeEngine, $filePath));
    }

    public function testDetermineFormatFailsSvg()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'determineFormat');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->with($this->equalTo($filePath))
            ->willReturn(array('mime' => 'image/svg'));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->setExpectedException('\Exception', "Image format not supported: image/svg");
        $reflectionMethod->invoke($resizeEngine, $filePath);
    }

    public function testDetermineFormatFailsWhenNoInfo()
    {
        $filePath = '/var/www/my/image.png';
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'determineFormat');
        $reflectionMethod->setAccessible(true);

        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->with($this->equalTo($filePath))
            ->willReturn(false);
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->setExpectedException('\Exception', "File is not a valid image: {$filePath}");
        $reflectionMethod->invoke($resizeEngine, $filePath);
    }

    public function testGetResourceJpeg()
    {
        $filePath = '/var/www/my/image.png';
        $type = IMAGETYPE_JPEG;
        $resource = new \stdClass();
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'getResource');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('imagecreatefromjpeg'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('imagecreatefromjpeg')
            ->with($this->equalTo($filePath))
            ->willReturn($resource);
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertSame($resource, $reflectionMethod->invoke($resizeEngine, $type, $filePath));
    }

    public function testGetResourceGif()
    {
        $filePath = '/var/www/my/image.png';
        $type = IMAGETYPE_GIF;
        $resource = new \stdClass();
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'getResource');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('imagecreatefromgif'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('imagecreatefromgif')
            ->with($this->equalTo($filePath))
            ->willReturn($resource);
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertSame($resource, $reflectionMethod->invoke($resizeEngine, $type, $filePath));
    }

    public function testGetResourcePng()
    {
        $filePath = '/var/www/my/image.png';
        $type = IMAGETYPE_PNG;
        $resource = new \stdClass();
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'getResource');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('imagecreatefrompng'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('imagecreatefrompng')
            ->with($this->equalTo($filePath))
            ->willReturn($resource);
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertSame($resource, $reflectionMethod->invoke($resizeEngine, $type, $filePath));
    }

    public function testGetResourceSwc()
    {
        $filePath = '/var/www/my/image.png';
        $type = IMAGETYPE_SWC;
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'getResource');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $this->assertFalse($reflectionMethod->invoke($resizeEngine, $type, $filePath));
    }

    public function testPrepareImageResizedJpeg()
    {
        $type = IMAGETYPE_JPEG;
        $width = 100;
        $height = 200;
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'prepareImageResized');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('imagecreatetruecolor', 'imagecolortransparent'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('imagecreatetruecolor')
            ->with($this->equalTo($width), $this->equalTo($height))
            ->willReturn($resource2);
        $mockGd->expects($this->never())->method('imagecolortransparent');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $return = $reflectionMethod->invoke($resizeEngine, $width, $height, $type, $resource);
        $this->assertSame($resource2, $return);
        $this->assertNotSame($resource, $return);
    }

    public function testPrepareImageResizedGif()
    {
        $type = IMAGETYPE_GIF;
        $width = 100;
        $height = 200;
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'prepareImageResized');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(
                array(
                    'imagecreatetruecolor',
                    'imagecolortransparent',
                    'imagecolorstotal',
                    'imagecolorsforindex',
                    'imagecolorallocate',
                    'imagefill',
                    'imagealphablending'
                )
            )
            ->getMock();
        $transparency = 1;
        $mockGd->expects($this->once())
            ->method('imagecreatetruecolor')
            ->with($this->equalTo($width), $this->equalTo($height))
            ->willReturn($resource2);
        $mockGd->expects($this->exactly(2))
            ->method('imagecolortransparent')
            ->withConsecutive(
                array($this->equalTo($resource)),
                array($this->equalTo($resource2), $this->equalTo(172))
            )
            ->willReturnOnConsecutiveCalls($transparency, null);
        $mockGd->expects($this->once())
            ->method('imagecolorstotal')
            ->with($this->equalTo($resource))
            ->willReturn(2);
        $mockGd->expects($this->once())
            ->method('imagecolorsforindex')
            ->with($this->equalTo($resource), $this->equalTo($transparency))
            ->willReturn(array('red' => 'red', 'green' => 'green', 'blue' => 'blue'));
        $mockGd->expects($this->once())
            ->method('imagecolorallocate')
            ->with($this->equalTo($resource2), $this->equalTo('red'), $this->equalTo('green'), $this->equalTo('blue'))
            ->willReturn(172);
        $mockGd->expects($this->once())
            ->method('imagefill')
            ->with($this->equalTo($resource2), $this->equalTo(0), $this->equalTo(0), $this->equalTo(172));
        $mockGd->expects($this->never())->method('imagealphablending');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $return = $reflectionMethod->invoke($resizeEngine, $width, $height, $type, $resource);
        $this->assertSame($resource2, $return);
        $this->assertNotSame($resource, $return);
    }

    public function testPrepareImageResizedGifWithoutTransparency()
    {
        $type = IMAGETYPE_GIF;
        $width = 100;
        $height = 200;
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'prepareImageResized');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(
                array(
                    'imagecreatetruecolor',
                    'imagecolortransparent',
                    'imagecolorstotal',
                    'imagecolorsforindex',
                )
            )
            ->getMock();
        $transparency = 12;
        $mockGd->expects($this->once())
            ->method('imagecreatetruecolor')
            ->with($this->equalTo($width), $this->equalTo($height))
            ->willReturn($resource2);
        $mockGd->expects($this->once())
            ->method('imagecolortransparent')
            ->with($this->equalTo($resource))
            ->willReturn($transparency);
        $mockGd->expects($this->once())
            ->method('imagecolorstotal')
            ->with($this->equalTo($resource))
            ->willReturn(2);
        $mockGd->expects($this->never())->method('imagecolorsforindex');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $return = $reflectionMethod->invoke($resizeEngine, $width, $height, $type, $resource);
        $this->assertSame($resource2, $return);
        $this->assertNotSame($resource, $return);
    }

    public function testPrepareImageResizedPng()
    {
        $type = IMAGETYPE_PNG;
        $width = 100;
        $height = 200;
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'prepareImageResized');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(
                array(
                    'imagecreatetruecolor',
                    'imagecolortransparent',
                    'imagecolorstotal',
                    'imagecolorsforindex',
                    'imagealphablending',
                    'imagecolorallocatealpha',
                    'imagefill',
                    'imagesavealpha',
                )
            )
            ->getMock();
        $transparency = 1;
        $mockGd->expects($this->once())
            ->method('imagecreatetruecolor')
            ->with($this->equalTo($width), $this->equalTo($height))
            ->willReturn($resource2);
        $mockGd->expects($this->once())
            ->method('imagecolortransparent')
            ->with($this->equalTo($resource))
            ->willReturn($transparency);
        $mockGd->expects($this->once())->method('imagecolorstotal')->with($this->equalTo($resource))->willReturn(1);
        $mockGd->expects($this->never())->method('imagecolorsforindex');
        $mockGd->expects($this->once())
            ->method('imagealphablending')
            ->with($this->equalTo($resource2), $this->equalTo(false));
        $mockGd->expects($this->once())
            ->method('imagecolorallocatealpha')
            ->with(
                $this->equalTo($resource2),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(127)
            )
            ->willReturn(5000);
        $mockGd->expects($this->once())
            ->method('imagefill')
            ->with($this->equalTo($resource2), $this->equalTo(0), $this->equalTo(0), $this->equalTo(5000));
        $mockGd->expects($this->once())
            ->method('imagesavealpha')
            ->with($this->equalTo($resource2), $this->equalTo(true));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $return = $reflectionMethod->invoke($resizeEngine, $width, $height, $type, $resource);
        $this->assertSame($resource2, $return);
        $this->assertNotSame($resource, $return);
    }
    
    public function testRenderGif()
    {
        $type = IMAGETYPE_GIF;
        $resource = new \stdClass();
        $output = '/var/www/image.png';

        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'render');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')->setMethods(array('imagegif'))->getMock();
        $mockGd->expects($this->once())
            ->method('imagegif')
            ->with($this->equalTo($resource), $this->equalTo($output));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertTrue($reflectionMethod->invoke($resizeEngine, $type, $resource, $output));
    }

    public function testRenderJpeg()
    {
        $type = IMAGETYPE_JPEG;
        $resource = new \stdClass();
        $output = '/var/www/image.png';

        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'render');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')->setMethods(array('imagejpeg'))->getMock();
        $mockGd->expects($this->once())
            ->method('imagejpeg')
            ->with($this->equalTo($resource), $this->equalTo($output), $this->equalTo(100));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertTrue($reflectionMethod->invoke($resizeEngine, $type, $resource, $output));
    }

    public function testRenderPng()
    {
        $type = IMAGETYPE_PNG;
        $resource = new \stdClass();
        $output = '/var/www/image.png';

        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'render');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')->setMethods(array('imagepng'))->getMock();
        $mockGd->expects($this->once())
            ->method('imagepng')
            ->with($this->equalTo($resource), $this->equalTo($output), $this->equalTo(9 - ((0.9 * 100) / 10.0)));
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertTrue($reflectionMethod->invoke($resizeEngine, $type, $resource, $output));
    }

    public function testRenderSwf()
    {
        $type = IMAGETYPE_SWF;
        $resource = new \stdClass();
        $output = '/var/www/image.png';

        $reflectionMethod = new \ReflectionMethod('\FemtoPixel\Crop\ResizeEngine', 'render');
        $reflectionMethod->setAccessible(true);
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')->setMethods(array('getGd'))->getMock();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')->setMethods(array('imagepng'))->getMock();
        $mockGd->expects($this->never())->method('imagepng');
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $this->assertFalse($reflectionMethod->invoke($resizeEngine, $type, $resource, $output));
    }

    public function testFailResize()
    {
        $resizerEngine = new \FemtoPixel\Crop\ResizeEngine();
        $this->assertFalse($resizerEngine->resize(null));
    }

    public function testFailResizeOnBadInfo()
    {
        $file = '/var/www/image.png';
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->willReturn(array(0, 1, 2));
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('getGd', 'getResource'))
            ->getMock();
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())->method('getResource')->with($this->equalTo(2), $this->equalTo($file));
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $this->assertFalse($resizeEngine->resize($file));
    }

    public function testFailResizeOnBadOutput()
    {
        $file = '/var/www/image.png';
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize', 'imagecopyresampled'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->willReturn(array(100, 200, 2));
        $mockGd->expects($this->once())
            ->method('imagecopyresampled')
            ->with(
                $this->equalTo($resource2),
                $this->equalTo($resource),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(100),
                $this->equalTo(200),
                $this->equalTo(100),
                $this->equalTo(200)
            );
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('getGd', 'getResource', 'prepareImageResized'))
            ->getMock();
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('getResource')
            ->with($this->equalTo(2), $this->equalTo($file))
            ->willReturn($resource);
        $resizeEngine->expects($this->once())
            ->method('prepareImageResized')
            ->with($this->equalTo(100), $this->equalTo(200), $this->equalTo(2), $this->equalTo($resource))
            ->willReturn($resource2);
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $this->assertFalse($resizeEngine->resize($file, 0, 0, false, null));
    }

    public function testResizeOnFile()
    {
        $file = '/var/www/image.png';
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize', 'imagecopyresampled'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->willReturn(array(100, 200, 2));
        $mockGd->expects($this->once())
            ->method('imagecopyresampled')
            ->with(
                $this->equalTo($resource2),
                $this->equalTo($resource),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(100),
                $this->equalTo(200),
                $this->equalTo(100),
                $this->equalTo(200)
            );
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('getGd', 'getResource', 'prepareImageResized', 'render'))
            ->getMock();
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('getResource')
            ->with($this->equalTo(2), $this->equalTo($file))
            ->willReturn($resource);
        $resizeEngine->expects($this->once())
            ->method('prepareImageResized')
            ->with($this->equalTo(100), $this->equalTo(200), $this->equalTo(2), $this->equalTo($resource))
            ->willReturn($resource2);
        $resizeEngine->expects($this->once())
            ->method('render')
            ->with($this->equalTo(2), $this->equalTo($resource2), $this->equalTo($file))
            ->willReturn(true);
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $this->assertTrue($resizeEngine->resize($file, 0, 0, false, \FemtoPixel\Crop\ResizeEngine::OUTPUT_FILE));
    }

    public function testResizeReturnAndCrop()
    {
        $file = '/var/www/image.png';
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize', 'imagecopyresampled'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->willReturn(array(100, 200, 2));
        $mockGd->expects($this->once())
            ->method('imagecopyresampled')
            ->with(
                $this->equalTo($resource2),
                $this->equalTo($resource),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(50),
                $this->equalTo(20),
                $this->equalTo(20),
                $this->equalTo(100),
                $this->equalTo(100)
            );
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('getGd', 'getResource', 'prepareImageResized'))
            ->getMock();
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('getResource')
            ->with($this->equalTo(2), $this->equalTo($file))
            ->willReturn($resource);
        $resizeEngine->expects($this->once())
            ->method('prepareImageResized')
            ->with($this->equalTo(20), $this->equalTo(20), $this->equalTo(2), $this->equalTo($resource))
            ->willReturn($resource2);
        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $this->assertSame($resource2, $resizeEngine->resize($file, 20, 20, true, \FemtoPixel\Crop\ResizeEngine::OUTPUT_RETURN));
    }

    public function testResizeBrowserAndCrop()
    {
        $file = '/var/www/image.png';
        $resource = new \stdClass();
        $resource2 = new \stdClass();
        $mockGd = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine\Gd')
            ->setMethods(array('getimagesize', 'imagecopyresampled', 'image_type_to_mime_type'))
            ->getMock();
        $mockGd->expects($this->once())
            ->method('getimagesize')
            ->willReturn(array(100, 200, 2));
        $mockGd->expects($this->once())
            ->method('imagecopyresampled')
            ->with(
                $this->equalTo($resource2),
                $this->equalTo($resource),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(0),
                $this->equalTo(50),
                $this->equalTo(20),
                $this->equalTo(20),
                $this->equalTo(100),
                $this->equalTo(100)
            );
        $mockGd->expects($this->once())
            ->method('image_type_to_mime_type')
            ->with($this->equalTo(2))
            ->willReturn('image/png');
        $resizeEngine = $this->getMockBuilder('\FemtoPixel\Crop\ResizeEngine')
            ->setMethods(array('getGd', 'getResource', 'prepareImageResized', 'render', 'phpHeader'))
            ->getMock();
        $resizeEngine->expects($this->once())->method('getGd')->willReturn($mockGd);
        $resizeEngine->expects($this->once())
            ->method('getResource')
            ->with($this->equalTo(2), $this->equalTo($file))
            ->willReturn($resource);
        $resizeEngine->expects($this->once())
            ->method('prepareImageResized')
            ->with($this->equalTo(20), $this->equalTo(20), $this->equalTo(2), $this->equalTo($resource))
            ->willReturn($resource2);
        $resizeEngine->expects($this->once())
            ->method('phpHeader')
            ->with($this->equalTo('Content-Type: image/png'));
        $resizeEngine->expects($this->once())
            ->method('render')
            ->with($this->equalTo(2), $this->equalTo($resource2), $this->equalTo(null))
            ->willReturn(true);

        /** @var $resizeEngine \FemtoPixel\Crop\ResizeEngine */
        $this->assertTrue($resizeEngine->resize($file, 20, 20, true));
    }
}
