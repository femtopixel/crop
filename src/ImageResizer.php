<?php

namespace FemtoPixel\ImageResizer;

/**
 * Class ImageResizer
 * @package FemtoPixel
 */
class ImageResizer
{
    const FORMAT_ORIGINAL = 'original';
    const DEFAULT_IMAGE = __DIR__ . DIRECTORY_SEPARATOR . 'missing.png';

    private $format = self::FORMAT_ORIGINAL;
    private $filePath = self::FORMAT_ORIGINAL;
    private $availableFormats = array();
    private $defaultImage = self::DEFAULT_IMAGE;

    /**
     * @param string $filePath
     * @param string $format
     * @param string $defaultImage
     */
    public function __construct($filePath, $format = self::FORMAT_ORIGINAL, $defaultImage = self::DEFAULT_IMAGE)
    {
        $this->setFilePath($filePath)->setFormat($format)->setDefaultImage($defaultImage);
    }

    /**
     * Define all formats from an array
     * @param array $formats
     * @return $this
     */
    public function setFormatsFromArray(array $formats)
    {
        $this->availableFormats = (array)$formats;
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
     * Render specified image
     * @throws Exception\FormatNotAvailable
     */
    public function render()
    {
        $filePath = $this->getFilePath();
        if (!file_exists($filePath)) {
            header('HTTP/1.0 404 Not Found');
            $filePath = $this->getComputedDefaultFilePath();
        }
        $info = $this->getResizeEngine()->getImageInfo($filePath);
        if ($this->getFormat() == self::FORMAT_ORIGINAL) {
            header("Content-type: {$info[ResizeEngine::INFO_MIME]}");
            readfile($filePath);
        } else {
            $this->resize($filePath, $info);
        }
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = (string)$filePath;
        return $this;
    }

    /**
     * @return Format|mixed|string
     * @throws Exception\FormatNotAvailable
     */
    protected function getComputedDefaultFilePath()
    {
        return ($this->getFormat() != self::FORMAT_ORIGINAL &&
            isset($this->getAvailableFormat($this->getFormat())[Format::DEFAULT_IMAGE])
        )
            ? $this->getAvailableFormat($this->getFormat())[Format::DEFAULT_IMAGE]
            : $this->getDefaultImage();
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
     * @return string
     */
    public function getDefaultImage()
    {
        return $this->defaultImage;
    }

    /**
     * @param string $defaultFilePath
     * @return $this
     */
    public function setDefaultImage($defaultFilePath = self::DEFAULT_IMAGE)
    {
        $this->defaultImage = (string)$defaultFilePath;
        return $this;
    }

    /**
     * @return ResizeEngine
     */
    protected function getResizeEngine()
    {
        return new ResizeEngine();
    }

    /**
     * @param $filePath
     * @param $info
     * @throws Exception\FormatNotAvailable
     */
    protected function resize($filePath, $info)
    {
        $formatDestination = $this->getAvailableFormat($this->getFormat());
        $ratioOriginal = $info[ResizeEngine::INFO_WIDTH] / $info[ResizeEngine::INFO_HEIGHT];
        $ratioDestination = $formatDestination[Format::WIDTH] / $formatDestination[Format::HEIGHT];
        $width = $formatDestination[Format::WIDTH];
        $height = $formatDestination[Format::HEIGHT];
        $formatFull = $formatDestination[Format::FULL];
        if ($formatFull == Format::FULL_AUTO) {
            list($width, $height) = $this->getWidthHeightForRatio($ratioDestination, $ratioOriginal);
        } elseif ($formatFull == Format::FULL_WIDTH) {
            $width = $formatDestination[Format::WIDTH];
            $height = $formatDestination[Format::WIDTH] * (1 / $ratioOriginal);
        } elseif ($formatFull == Format::FULL_HEIGHT) {
            $height = $formatDestination[Format::HEIGHT];
            $width = $formatDestination[Format::HEIGHT] * $ratioOriginal;
        }
        $this->getResizeEngine()->resize($filePath, $width, $height, $formatFull !== Format::FULL_NONE);
    }

    /**
     * @param float $ratioDestination
     * @param float $ratioOriginal
     * @return array
     * @throws Exception\FormatNotAvailable
     */
    protected function getWidthHeightForRatio($ratioDestination, $ratioOriginal)
    {
        $formatDestination = $this->getAvailableFormat($this->getFormat());
        if ($ratioDestination < $ratioOriginal) {
            $height = $formatDestination[Format::HEIGHT];
            $width = $formatDestination[Format::HEIGHT] * $ratioOriginal;
            if ($width > $formatDestination[Format::WIDTH] && !$formatDestination[Format::CROPPED]) {
                $width = $formatDestination[Format::WIDTH];
                $height = $formatDestination[Format::WIDTH] * (1 / $ratioOriginal);
            }
        } else {
            $width = $formatDestination[Format::WIDTH];
            $height = $formatDestination[Format::WIDTH] * (1 / $ratioOriginal);
            if ($height > $formatDestination[Format::HEIGHT] && !$formatDestination[Format::CROPPED]) {
                $height = $formatDestination[Format::HEIGHT];
                $width = $formatDestination[Format::HEIGHT] * $ratioOriginal;
            }
        }
        return array($width, $height);
    }
}
