<?php
namespace FemtoPixel\Crop\Tests;


class FormatTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct()
    {
        $format = new \FemtoPixel\Crop\Format(100, 200);
        $this->assertSame(100, $format->getWidth());
        $this->assertSame(200, $format->getHeight());
        $this->assertSame(\FemtoPixel\Crop\Format::FULL_NONE, $format->getFullMode());
        $this->assertSame(null, $format->getDefaultImage());

        $format = new \FemtoPixel\Crop\Format('5', '50', \FemtoPixel\Crop\Format::FULL_CROPPED, 'test');
        $this->assertSame(5, $format->getWidth());
        $this->assertSame(50, $format->getHeight());
        $this->assertSame(\FemtoPixel\Crop\Format::FULL_CROPPED, $format->getFullMode());
        $this->assertSame('test', $format->getDefaultImage());
    }

    public function testSetGet()
    {
        $format = new \FemtoPixel\Crop\Format(100, 200);
        $this->assertTrue(isset($format[\FemtoPixel\Crop\Format::HEIGHT]));
        $this->assertSame(200, $format->getHeight());
        $this->assertSame(200, $format[\FemtoPixel\Crop\Format::HEIGHT]);
        unset($format[\FemtoPixel\Crop\Format::HEIGHT]);
        $this->assertFalse(isset($format[\FemtoPixel\Crop\Format::HEIGHT]));
        $format[\FemtoPixel\Crop\Format::HEIGHT] = 10;
        $this->assertSame(10, $format->getHeight());
        $this->assertSame(10, $format[\FemtoPixel\Crop\Format::HEIGHT]);
        $this->assertSame(100, $format->getWidth());
        $this->assertSame(100, $format[\FemtoPixel\Crop\Format::WIDTH]);
        $this->assertSame($format, $format->setWidth('59'));
        $this->assertSame(59, $format->getWidth());
        $this->assertSame(59, $format[\FemtoPixel\Crop\Format::WIDTH]);
        $this->assertSame($format, $format->setHeight('59'));
        $this->assertSame(59, $format->getHeight());
        $this->assertSame(59, $format[\FemtoPixel\Crop\Format::HEIGHT]);
    }

    public function testFailSetFullModeWhenFormatNotAllowed()
    {
        $format = new \FemtoPixel\Crop\Format(10, 10);
        $this->expectException(\FemtoPixel\Crop\Exception\FormatFullModeNotAvailable::class);
        $this->expectExceptionMessage("Format 'not existing' is not allowed in : none, cropped, height, width");
        $format->setFullMode('not existing');
    }
}
