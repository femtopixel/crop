<?php
declare(strict_types = 1);

namespace FemtoPixel\Crop;

use FemtoPixel\Crop\Exception\FormatFullModeNotAvailable;

/**
 * Class Format
 * @package FemtoPixel\Crop
 */
class Format implements \ArrayAccess
{
    const WIDTH = 'width';
    const HEIGHT = 'height';

    const FULL = 'full';
    const FULL_WIDTH = 'width';
    const FULL_HEIGHT = 'height';
    const FULL_CROPPED = 'cropped';
    const FULL_NONE = 'none';

    const DEFAULT_IMAGE = 'default';

    private $data = array(
        self::DEFAULT_IMAGE => null
    );

    /**
     * @param int $width
     * @param int $height
     * @param string $full
     * @param string|null $defaultImage
     * @throws FormatFullModeNotAvailable
     */
    public function __construct(int $width, int $height, string $full = self::FULL_NONE, ?string $defaultImage = null)
    {
        $this->setFullMode($full)
            ->setHeight($height)
            ->setWidth($width);
        if ($defaultImage) {
            $this->setDefaultImage($defaultImage);
        }
    }

    /**
     * @param int $width
     * @return Format
     */
    public function setWidth(int $width) : Format
    {
        $this->data[self::WIDTH] = $width;
        return $this;
    }

    /**
     * @param int $height
     * @return Format
     */
    public function setHeight(int $height) : Format
    {
        $this->data[self::HEIGHT] = $height;
        return $this;
    }

    /**
     * @param string $fullMode
     * @return Format
     * @throws FormatFullModeNotAvailable
     */
    public function setFullMode(string $fullMode = self::FULL_NONE) : Format
    {
        $allowed = array(
            self::FULL_NONE => self::FULL_NONE,
            self::FULL_CROPPED => self::FULL_CROPPED,
            self::FULL_HEIGHT => self::FULL_HEIGHT,
            self::FULL_WIDTH => self::FULL_WIDTH,
        );
        if (!isset($allowed[$fullMode])) {
            throw new FormatFullModeNotAvailable("Format '$fullMode' is not allowed in : " . implode(', ', $allowed));
        }
        $this->data[self::FULL] = $fullMode;
        return $this;
    }

    /**
     * @return int
     */
    public function getWidth() : int
    {
        return $this->data[self::WIDTH];
    }

    /**
     * @return int
     */
    public function getHeight() : int
    {
        return $this->data[self::HEIGHT];
    }

    /**
     * @return string
     */
    public function getFullMode() : string
    {
        return $this->data[self::FULL];
    }

    /**
     * @param string|null $defaultImage
     * @return Format
     */
    public function setDefaultImage(?string $defaultImage = null) : Format
    {
        $this->data[self::DEFAULT_IMAGE] = (string)$defaultImage;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDefaultImage() : ?string
    {
        return $this->data[self::DEFAULT_IMAGE];
    }

    /**
     * @param string|int $offset
     * @return bool
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * @param string|int $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * @param string|int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @param string|int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
