<?php
namespace FemtoPixel\Crop\ResizeEngine;

/**
 * Class Gd
 * @package FemtoPixel\Crop\ResizeEngine
 * @codeCoverageIgnore
 * @method array getimagesize(string $filename, array &$imageinfo = null)
 * @method resource imagecreatefromjpeg ( string $filename )
 * @method resource imagecreatefromgif ( string $filename )
 * @method resource imagecreatefrompng ( string $filename )
 * @method resource imagecreatetruecolor ( int $width , int $height )
 * @method int imagecolortransparent ( resource $image , int $color = null )
 * @method int imagecolorstotal ( resource $image )
 * @method array imagecolorsforindex ( resource $image , int $index )
 * @method int imagecolorallocate ( resource $image , int $red , int $green , int $blue )
 * @method bool imagefill ( resource $image , int $x , int $y , int $color )
 * @method bool imagealphablending ( resource $image , bool $blendmode )
 * @method int imagecolorallocatealpha ( resource $image , int $red , int $green , int $blue , int $alpha )
 * @method bool imagesavealpha ( resource $image , bool $saveflag )
 * @method bool imagecopyresampled(resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
 * @method string image_type_to_mime_type ( int $imagetype )
 * @method bool imagegif ( resource $image, string $filename = null )
 * @method bool imagejpeg ( resource $image, string $filename = null, int $quality = 100 )
 * @method bool imagepng ( resource $image, string $filename = null, int $quality =100, int $filters = 0 )
 * @method array gd_info ()
 * @method int imagesx ( resource $image )
 * @method int imagesy ( resource $image )
 */
class Gd
{
    /**
     * @param string $method
     */
    public function __call($method, $params)
    {
        call_user_func_array(array($method), $params);
    }
}