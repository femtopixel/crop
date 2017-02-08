<?php
declare(strict_types = 1);

namespace FemtoPixel\Crop;

/**
 * Class Crop
 * @package FemtoPixel\Crop
 */
class Crop
{
    const FORMAT_ORIGINAL = 'original';

    private $format = self::FORMAT_ORIGINAL;
    private $filePath = self::FORMAT_ORIGINAL;
    private $availableFormats = array();
    private $defaultImage = '';
    private $resizeEngine = null;

    /**
     * @param string $filePath
     * @param string $format
     * @param string $defaultImage
     */
    public function __construct(string $filePath, string $format = self::FORMAT_ORIGINAL, string $defaultImage = '')
    {
        $this->setFilePath($filePath)->setFormat($format)->setDefaultImage($defaultImage);
    }

    /**
     * Define all formats from an array
     * @param array $formats
     * @return Crop
     */
    public function setFormatsFromArray(array $formats) : Crop
    {
        $this->availableFormats = $formats;
        return $this;
    }

    /**
     * Define a format type to a name
     * @param string $formatName
     * @param \FemtoPixel\Crop\Format $format
     * @return Crop
     */
    public function setAvailableFormat(string $formatName, \FemtoPixel\Crop\Format $format) : Crop
    {
        $this->availableFormats[$formatName] = $format;
        return $this;
    }

    /**
     * Render specified image
     * @throws Exception\FormatNotAvailable
     */
    public function render()
    {
        $filePath = $this->getFilePath();
        if (!$this->phpFileExists($filePath)) {
            $this->phpHeader('HTTP/1.0 404 Not Found');
            $filePath = $this->getComputedDefaultFilePath();
        }
        $info = $this->getResizeEngine()->getImageInfo($filePath);
        if ($this->getFormat() == self::FORMAT_ORIGINAL) {
            $this->phpHeader("Content-Type: {$info[ResizeEngine::INFO_MIME]}");
            $this->phpReadfile($filePath);
            return;
        }
        $this->resize($filePath, $info);
    }

    /**
     * @param string $filePath
     * @return bool
     * @codeCoverageIgnore
     */
    protected function phpFileExists(string $filePath) : bool
    {
        return file_exists($filePath);
    }

    /**
     * @param string $filename
     * @param bool $useIncludePath
     * @param resource $context
     * @return int
     * @codeCoverageIgnore
     */
    protected function phpReadfile(string $filename, ?bool $useIncludePath = null, $context = null) : int
    {
        return readfile($filename, $useIncludePath, $context);
    }

    /**
     * @param string $string
     * @param bool $replace
     * @param int $httpResponseCode
     * @codeCoverageIgnore
     */
    protected function phpHeader(string $string, bool $replace = true, ?int $httpResponseCode = null)
    {
        return header($string, $replace, $httpResponseCode);
    }

    /**
     * @return string
     */
    public function getFilePath() : string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return Crop
     */
    public function setFilePath(string $filePath) : Crop
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
        $format = $this->getFormat();
        if ($format == self::FORMAT_ORIGINAL) {
            return $this->getDefaultImage();
        }
        $formatRequest = $this->getAvailableFormat($format);
        return (isset($formatRequest[Format::DEFAULT_IMAGE]))
            ? $formatRequest[Format::DEFAULT_IMAGE]
            : $this->getDefaultImage();
    }

    /**
     * Returns format name defined
     * @return string
     */
    public function getFormat() : string
    {
        return $this->format;
    }

    /**
     * Define format name to get
     * @param string $format
     * @return Crop
     */
    public function setFormat(string $format = self::FORMAT_ORIGINAL) : Crop
    {
        $this->format = (string)$format;
        return $this;
    }

    /**
     * @param string|null $format
     * @return \FemtoPixel\Crop\Format[]|\FemtoPixel\Crop\Format
     * @throws Exception\FormatNotAvailable
     */
    public function getAvailableFormat(?string $format = null)
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
    public function isAvailableFormat(string $format) : bool
    {
        return isset($this->availableFormats[$format]);
    }

    /**
     * @return string
     */
    public function getDefaultImage() : string
    {
        return $this->defaultImage;
    }

    /**
     * @param string $defaultFilePath
     * @return Crop
     */
    public function setDefaultImage(?string $defaultFilePath = null) : Crop
    {
        $this->defaultImage = $defaultFilePath
            ? (string)$defaultFilePath
            : __DIR__ . DIRECTORY_SEPARATOR . 'missing.png';
        return $this;
    }

    /**
     * @return ResizeEngine
     * @codeCoverageIgnore
     */
    protected function getResizeEngine() : ResizeEngine
    {
        return ($this->resizeEngine = ($this->resizeEngine ?: new ResizeEngine()));
    }

    /**
     * @param string $filePath
     * @param array $info
     * @throws Exception\FormatNotAvailable
     */
    protected function resize($filePath, array $info)
    {
        $formatDestination = $this->getAvailableFormat($this->getFormat());
        $ratioOriginal = $info[ResizeEngine::INFO_WIDTH] / $info[ResizeEngine::INFO_HEIGHT];
        $width = (int)$formatDestination[Format::WIDTH];
        $height = (int)$formatDestination[Format::HEIGHT];
        $formatFull = isset($formatDestination[Format::FULL]) ? $formatDestination[Format::FULL] : Format::FULL_NONE;
        if ($formatFull == Format::FULL_WIDTH) {
            $width = (int)$formatDestination[Format::WIDTH];
            $height = (int) ($formatDestination[Format::WIDTH] * (1 / $ratioOriginal));
        } elseif ($formatFull == Format::FULL_HEIGHT) {
            $height = (int)$formatDestination[Format::HEIGHT];
            $width = (int) ($formatDestination[Format::HEIGHT] * $ratioOriginal);
        }
        $this->getResizeEngine()
            ->resize(
                $filePath,
                $formatFull == Format::FULL_CROPPED ? $formatDestination[Format::WIDTH] : $width,
                $formatFull == Format::FULL_CROPPED ? $formatDestination[Format::HEIGHT] : $height,
                $formatFull == Format::FULL_CROPPED
            );
    }
}
