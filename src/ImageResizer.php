<?php

namespace FemtoPixel\ImageResizer;

/**
 * Class ImageResizer
 * @package FemtoPixel
 */
class ImageResizer
{
    const FORMAT_ORIGINAL = 'original';

    private $format = self::FORMAT_ORIGINAL;
    private $filePath = self::FORMAT_ORIGINAL;
    private $availableFormats = array();

    /**
     * @param string $filePath
     * @param string $format
     */
    public function __construct($filePath, $format = self::FORMAT_ORIGINAL)
    {
        $this->setFilePath($filePath)->setFormat($format);
    }

    /**
     * Define all formats from an array
     * @param array $formats
     * @return $this
     */
    public function setFormatsFromArray(array $formats)
    {
        $this->availableFormats = (array) $formats;
        return $this;
    }

    /**
     * Define a format type to a name
     * @param string $formatName
     * @param \FemtoPixel\ImageResizer\Format $format
     * @return $this
     */
    public function setAvailableFormat($formatName, \FemtoPixel\ImageResizer\Format $format)
    {
        $this->availableFormats[(string)$formatName] = $format;
        return $this;
    }

    /**
     * @param string|null $format
     * @return \FemtoPixel\ImageResizer\Format[]|\FemtoPixel\ImageResizer\Format
     * @throws Exception\FormatNotAvailable
     */
    public function getAvailableFormat($format = null)
    {
        if ($format === null) {
            return $this->availableFormats;
        }
        if (false == $this->isAvailableFormat($format)) {
            throw new Exception\FormatNotAvailable("Requested '$format' format is not available");
        }
        return $this->availableFormats[$format];
    }

    /**
     * Check whether a format is available or not
     * @param string $format
     * @return bool
     */
    public function isAvailableFormat($format)
    {
        return isset($this->availableFormats[$format]);
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = (string) $filePath;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Define format name to get
     * @param string $format
     * @return $this
     */
    public function setFormat($format = self::FORMAT_ORIGINAL)
    {
        $this->format = (string)$format;
        return $this;
    }

    /**
     * Returns format name defined
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param string $filePath
     * @return \PHPThumb\GD
     */
    protected function getThumb($filePath)
    {
        return new \PHPThumb\GD($filePath);
    }

    /**
     * @param \PHPThumb\GD $thumb
     * @return \PHPThumb\GD
     * @throws Exception\FormatNotAvailable
     */
    protected function resize(\PHPThumb\GD $thumb)
    {
        if ($this->getFormat() == self::FORMAT_ORIGINAL) {
            return $thumb;
        }
        $formatDest = $this->getAvailableFormat($this->getFormat());
        $dimensions = $thumb->getCurrentDimensions();
        $ratioOriginal = $dimensions[Format::WIDTH] / $dimensions[Format::HEIGHT];
        $ratioDest = $formatDest[Format::WIDTH] / $formatDest[Format::HEIGHT];
        $width = $formatDest[Format::WIDTH];
        $height = $formatDest[Format::HEIGHT];
        $formatFull = $formatDest[Format::FULL];
        if ($formatFull == Format::FULL_AUTO || $formatFull == Format::FULL_NONE) {
            list($width, $height) = $this->getWidthHeightForRatio($ratioDest, $ratioOriginal);
            $formatFull = $formatFull == Format::FULL_AUTO ? Format::FULL_WIDTH : Format::FULL_NONE;
        }
        $thumb->setOptions(array('resizeUp' => true));
        if ($formatFull == Format::FULL_WIDTH) {
            $width = $formatDest[Format::WIDTH];
            $height = $formatDest[Format::WIDTH] * (1 / $ratioOriginal);
        } elseif ($formatFull == Format::FULL_HEIGHT) {
            $height = $formatDest[Format::HEIGHT];
            $width = $formatDest[Format::HEIGHT] * $ratioOriginal;
        }
        $thumb->resize($width, $height);
        if ($formatDest[Format::CROPPED]) {
            $thumb->cropFromCenter($formatDest[Format::WIDTH], $formatDest[Format::HEIGHT]);
        }
        return $thumb;
    }

    /**
     * @param float$ratioDest
     * @param float $ratioOriginal
     * @return array
     * @throws Exception\FormatNotAvailable
     */
    protected function getWidthHeightForRatio($ratioDest, $ratioOriginal)
    {
        $formatDest = $this->getAvailableFormat($this->getFormat());
        if ($ratioDest < $ratioOriginal) {
            $height = $formatDest[Format::HEIGHT];
            $width = $formatDest[Format::HEIGHT] * $ratioOriginal;
            if ($width > $formatDest[Format::WIDTH] && !$formatDest[Format::CROPPED]) {
                $width = $formatDest[Format::WIDTH];
                $height = $formatDest[Format::WIDTH] * (1 / $ratioOriginal);
            }
        } else {
            $width = $formatDest[Format::WIDTH];
            $height = $formatDest[Format::WIDTH] * (1 / $ratioOriginal);
            if ($height > $formatDest[Format::HEIGHT] && !$formatDest[Format::CROPPED]) {
                $height = $formatDest[Format::HEIGHT];
                $width = $formatDest[Format::HEIGHT] * $ratioOriginal;
            }
        }
        return array($width, $height);
    }

    /**
     * Render specified image
     * @throws Exception\FormatNotAvailable
     */
    public function render()
    {
        try {
            $thumb = $this->getThumb($this->getFilePath());
        } catch (\Exception $e) {
            header('HTTP/1.0 404 Not Found');
            $filePath = $this->getAvailableFormat($this->getFormat())[Format::DEFAULT_IMAGE];
            $thumb = $this->getThumb($filePath);
        }
        $this->resize($thumb)->show();
    }
}
