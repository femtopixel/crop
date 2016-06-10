<?php
namespace FemtoPixel\ImageResizer;

/**
 * Class ResizeEngine
 * @package FemtoPixel\ImageResizer
 */
class ResizeEngine
{
    const OUTPUT_FILE = 'file';
    const OUTPUT_BROWSER = 'browser';
    const OUTPUT_RETURN = 'return';

    const INFO_WIDTH = 'width';
    const INFO_HEIGHT = 'height';
    const INFO_MIME = 'mime';

    /**
     * image resize function
     * @param string $file - file name to resize
     * @param int $width - new image width
     * @param int $height - new image height
     * @param bool $crop - crop image to requested size
     * @param string $output - name of the new file (include path if needed)
     * @return boolean|resource
     */
    public function resize($file, $width = 0, $height = 0, $crop = false, $output = self::OUTPUT_BROWSER)
    {
        if ($height <= 0 && $width <= 0) return false;
        if ($file === null) return false;

        # Setting defaults and meta
        $info = getimagesize($file);
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;

        $final_width = ($width <= 0) ? $width_old : $width;
        $final_height = ($height <= 0) ? $height_old : $height;
        # Calculating proportionality

        if ($crop) {
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }

        # Loading image to memory according to type
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;
            default:
                return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor($final_width, $final_height);
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);

            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);

        # Preparing a method of providing result
        switch (strtolower($output)) {
            case self::OUTPUT_BROWSER:
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = null;
                break;
            case self::OUTPUT_FILE:
                $output = $file;
                break;
            case self::OUTPUT_RETURN:
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination and image quality
        $quality = 100;
        switch ($info[2]) {
            case IMAGETYPE_GIF:
                imagegif($image_resized, $output);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($image_resized, $output, $quality);
                break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9 * $quality) / 10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default:
                return false;
        }

        return true;
    }

    /**
     * @param string $filePath
     * @return string
     * @throws \Exception
     */
    protected function determineFormat($filePath)
    {
        $formatInfo = getimagesize($filePath);

        // non-image files will return false
        if ($formatInfo === false) {
            throw new \Exception("File is not a valid image: {$filePath}");
        }

        $mimeType = isset($formatInfo['mime']) ? $formatInfo['mime'] : null;

        switch ($mimeType) {
            case 'image/gif':
            case 'image/jpeg':
            case 'image/png':
                return $mimeType;
            default:
                throw new \Exception("Image format not supported: {$mimeType}");
        }
    }

    /**
     * @param string $filePath
     * @return string
     * @throws \Exception
     */
    protected function verifyFormatCompatibility($filePath)
    {
        $gdInfo = gd_info();
        $format = $this->determineFormat($filePath);

        switch ($format) {
            case 'image/gif':
                $isCompatible = $gdInfo['GIF Create Support'];
                break;
            case 'image/jpeg':
                $isCompatible = (isset($gdInfo['JPG Support']) || isset($gdInfo['JPEG Support']));
                break;
            case 'image/png':
                $isCompatible = $gdInfo['PNG Support'];
                break;
            default:
                $isCompatible = false;
        }

        if (!$isCompatible) {
            // one last check for "JPEG" instead
            $isCompatible = $gdInfo['JPEG Support'];

            if (!$isCompatible) {
                throw new \Exception("Your GD installation does not support {$format} image types");
            }
        }
        return $format;
    }

    /**
     * @param string $filePath
     * @return array
     * @throws \Exception
     */
    public function getImageInfo($filePath)
    {
        $format = $this->verifyFormatCompatibility($filePath);
        switch ($format) {
            case 'image/gif':
                $resource = imagecreatefromgif($filePath);
                break;
            case 'image/jpeg':
                $resource = imagecreatefromjpeg($filePath);
                break;
            case 'image/png':
                $resource = imagecreatefrompng($filePath);
                break;
            default:
                throw new \Exception("Your GD installation does not support {$format} image types");
        }

        return array(
            self::INFO_WIDTH => imagesx($resource),
            self::INFO_HEIGHT => imagesy($resource),
            self::INFO_MIME => $format,
        );
    }
}
