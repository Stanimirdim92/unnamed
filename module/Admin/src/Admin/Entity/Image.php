<?php

/**
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.21
 *
 * @link       TBA
 */

namespace Admin\Entity;

use Admin\Exception\BadMethodCallException;
use Admin\Exception\InvalidArgumentException;
use Admin\Exception\RuntimeException;
use Admin\Entity\ImageInterface;

final class Image implements ImageInterface
{
    /**
     * A valid image path /path/to/image.png.
     *
     * @var string
     */
    private $imageFile = null;

    /**
     * Image format, taken from the mime type.
     *
     * @var string
     */
    private $format = null;

    /**
     * The current dimensions of the image.
     *
     * @var array
     */
    private $imageDimensions = ['width' => 1, 'height' => 1];

    /**
     * @var int
     */
    private $width = 320;

    /**
     * @var int
     */
    private $height = 270;

    /**
     * All config options for different image formats.
     *
     * @var array
     */
    private $options = [
        'preserve_alpha'          => true,
        'alpha_color_allocate'    => [255, 255, 255],
        'alpha_transperancy'      => 64,
        'png_compression_level'   => -1,
        'png_compression_filter'  => 'all',
        'jpeg_quality'            => 75,
        'foreground'              => [0, 0, 0],

        // still not used anywhere
        'interlace'               => 0,
        'transparency_mask_color' => [0, 0, 0],
    ];

    /**
     * All allowed mime types.
     *
     * @var array
     */
    private $allowedMimeTypes = [
        'image/gif',
        'image/jpeg',
        'image/png',
        'image/bmp',
        'image/webp',
        'image/wbmp',
    ];

    /**
     * PNG compression filters.
     *
     * @var array
     */
    private $pngFilterTypes = [
        'no'    => PNG_NO_FILTER,
        'none'  => PNG_FILTER_NONE,
        'sub'   => PNG_FILTER_SUB,
        'up'    => PNG_FILTER_UP,
        'avg'   => PNG_FILTER_AVG,
        'paeth' => PNG_FILTER_PAETH,
        'all'   => PNG_ALL_FILTERS,
    ];

    /**
     * The GD library.
     *
     * @var GD
     */
    private $gd = null;

    public function __construct()
    {
        $this->gd = new GD('2.0.1');
    }

    /**
     * Try to open and read image.
     *
     * @method open
     *
     * @param string $imageFile
     * @param array  $options
     *
     * @return self
     */
    public function open($imageFile, array $options = [])
    {
        /*
         * See if this really is a file
         */
        if (!is_file($imageFile)) {
            throw new InvalidArgumentException('Invalid image');
        }

        /*
         * Try reading its contents
         */
        $data = file_get_contents($imageFile);
        if (!$data) {
            throw new RuntimeException('Cannot open file');
        }

        /*
         * Try creating image from the read file
         */
        $resource = imagecreatefromstring($data);
        if (!is_resource($resource)) {
            throw new RuntimeException('Unable to open image');
        }

        $this->imageFile = $imageFile;
        $this->setOptions($options);
        $this->extractImageFormat();
        $this->createImageFromFormat();

        $this->imageDimensions = [
            'width'  => imagesx($this->getImageFile()),
            'height' => imagesy($this->getImageFile()),
        ];

        return $this;
    }

    /**
     * Free up memory.
     */
    public function __destruct()
    {
        if (is_resource($this->getImageFile())) {
            imagedestroy($this->getImageFile());
        }
    }

    /**
     * @return resource
     */
    private function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * Returns the format.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * The current dimensions of the image.
     *
     * @return array
     */
    public function getImageDimensions()
    {
        return $this->imageDimensions;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Holds all config data for all methods.
     *
     * @param array $options
     *
     * @return self
     */
    private function setOptions(array $options = [])
    {
        if (!is_array($options) && !$options instanceof \Traversable) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parameter provided to %s must be an array or Traversable',
                    __METHOD__
                )
            );
        }

        if (!empty($options)) {
            foreach ($options as $key => $value) {
                $this->options[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Get all options set.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get an individual option.
     *
     * Keys are normalized to lowercase.
     *
     * Returns null for not found options.
     *
     * @param string $option
     *
     * @return mixed
     */
    public function getOption($option)
    {
        $option = strtolower($option);
        if (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        }

        return null;
    }

    /**
     * The function will return false for invalid images.
     *
     * @return array|false
     */
    public function getImageInfo()
    {
        return getimagesize($this->getImageFile());
    }

    /**
     * Prepare new image size.
     *
     * @param int $width
     * @param int $height
     */
    private function checkImageSizes($width = 1, $height = 1)
    {
        if ($height < 1 || $width < 1) {
            throw new InvalidArgumentException('Image height and width must be at least 1 pixel');
        }

        $this->width = (int) $width;
        $this->height = (int) $height;
    }

    /**
     * Extract the file format by mime-type.
     *
     * @throws RuntimeException for invalid mime-types
     */
    private function extractImageFormat()
    {
        $format = $this->getImageInfo();

        if (!in_array($format['mime'], $this->allowedMimeTypes)) {
            throw new RuntimeException('Unsupported image format');
        }

        /*
         * strip out image/ from string and make the rest upper cases
         */
        $format = strtoupper(substr($format['mime'], 6));

        /*
         * Normalize formats
         */
        if ($format === 'JPG' || $format === 'PJPEG') {
            $format = 'JPEG';
        }

        if ($format === 'VND.WAP.WBMP') {
            $format = 'WBMP';
        }

        $this->format = $format;
    }

    /**
     * Try to create a new image from the supplied file.
     *
     * @throws RuntimeException on invalid image format
     */
    private function createImageFromFormat()
    {
        switch ($this->getFormat()) {
            case 'GIF':
                $this->imageFile = $this->imageCreateFromGIF();
                break;

            case 'JPEG':
                $this->imageFile = $this->imageCreateFromJPEG();
                break;

            case 'PNG':
                $this->imageFile = $this->imageCreateFromPNG();
                break;

            case 'WEBP':
                $this->imageFile = $this->imageCreateFromWEBP();
                break;

            default:
                throw new RuntimeException('Invalid image format');
                break;
        }
    }

    /**
     * See if we can create GIF images.
     *
     * @throws BadMethodCallException on missing support
     */
    private function imageCreateFromGIF()
    {
        if ($this->gd->hasGIFCreateSupport()) {
            return imagecreatefromgif($this->getImageFile());
        }

        throw new BadMethodCallException('Missing GIF create support');
    }

    /**
     * See if we can create JEPG|JPG images.
     *
     * @throws BadMethodCallException on missing support
     */
    private function imageCreateFromJPEG()
    {
        if ($this->gd->hasJPEGSupport()) {
            return imagecreatefromjpeg($this->getImageFile());
        }

        throw new BadMethodCallException('Missing JPEG support');
    }

    /**
     * See if we can create PNG images.
     *
     * @throws BadMethodCallException on missing support
     */
    private function imageCreateFromPNG()
    {
        if ($this->gd->hasPNGSupport()) {
            return imagecreatefrompng($this->getImageFile());
        }

        throw new BadMethodCallException('Missing PNG support');
    }

    /**
     * See if we can create WEBP images.
     *
     * @throws BadMethodCallException on missing support
     */
    private function imageCreateFromWEBP()
    {
        if ($this->gd->hasJPEGSupport() || $this->gd->hasPNGSupport()) {
            if (function_exists('imagecreatefromwebp')) {
                return imagecreatefromwebp($this->getImageFile());
            }
        }

        throw new BadMethodCallException('Missing WEBP support');
    }

    /**
     * Generates a GD image.
     *
     * @return resource
     */
    private function generateImage()
    {
        $resource = imagecreatetruecolor($this->getWidth(), $this->getHeight());

        imagealphablending($resource, $this->getOption('preserve_alpha'));
        imagesavealpha($resource, true);

        if (function_exists('imageantialias')) {
            imageantialias($resource, true);
        }

        $color = $this->getOption('alpha_color_allocate');
        if (!is_array($color)) {
            $color = [255, 255, 255];
            $this->options['alpha_color_allocate'] = $color;
        }

        $alpha = $this->getOption('alpha_transperancy');
        if ($alpha < 1 || $alpha > 127) {
            $alpha = 64;
            $this->options['alpha_transperancy'] = $alpha;
        }

        $transparentColor = imagecolorallocatealpha($resource, $color[0], $color[1], $color[2], $alpha);
        imagefill($resource, 0, 0, $transparentColor);
        imagecolortransparent($resource, $transparentColor);

        return $resource;
    }

    /**
     * Create the image with the given width and height.
     *
     * @param int $width
     * @param int $height
     *
     * @throws RuntimeException on invalid operation
     *
     * @return self
     */
    public function resize($width = 1, $height = 1)
    {
        $this->checkImageSizes($width, $height);

        $oldImageDimensions = $this->getImageDimensions();
        $newImage = $this->generateImage();

        imagealphablending($this->getImageFile(), true);
        imagealphablending($newImage, true);

        if (!imagecopyresampled($newImage, $this->getImageFile(), 0, 0, 0, 0, $this->getWidth(), $this->getHeight(), $oldImageDimensions['width'], $oldImageDimensions['height'])) {
            throw new RuntimeException('Image resizing has failed');
        }

        imagealphablending($this->getImageFile(), false);
        imagealphablending($newImage, false);
        imagedestroy($this->getImageFile());

        $this->imageFile = $newImage;

        return $this;
    }

    /**
     * @param string $path
     * @param string $fileName
     *
     * @throws RuntimeException
     */
    public function save($path, $fileName)
    {
        if (!$path || !is_dir($path)) {
            throw new RuntimeException('Path is not set');
        }

        if (!is_writable($path)) {
            throw new RuntimeException('Path is not writable');
        }

        if (!$fileName) {
            throw new RuntimeException('Image name is not set');
        }

        $format = strtolower($this->getFormat());
        $imageSaveMethod = 'image'.$format;
        $options = [$this->getImageFile(), $path.DIRECTORY_SEPARATOR.$fileName];

        $opt = $this->checkFormatOptions();
        foreach ($opt as $option) {
            $options[] = $option;
        }

        if (!call_user_func_array($imageSaveMethod, $options)) {
            throw new RuntimeException('Image save has failed');
        }
    }

    /**
     * @return array
     */
    private function checkFormatOptions()
    {
        $params = [];
        $format = strtolower($this->getFormat());

        if ($format === 'png') {
            $png = $this->pngOptions();
            $params[] = $png['level'];
            $params[] = $png['filter'];
        } elseif ($format === 'jpeg') {
            $params[] = $this->jpegOptions();
        } elseif ($format === 'wbmp') {
            $params[] = $this->wbmpOptions();
        }

        return $params;
    }

    /**
     * @return array
     */
    private function pngOptions()
    {
        $params = [];
        $params['level'] = $this->getOption('png_compression_level');
        $filter = $this->getOption('png_compression_filter');

        if ($params['level'] < 0 || $params['level'] > 9) {
            // http://www.zlib.net/manual.html
            $params['level'] = -1; // Z_DEFAULT_COMPRESSION
            $this->options['png_compression_level'] = $params['level'];
        }

        if (!is_string($filter)) {
            throw new RuntimeException('png_compression_filter must be a string '.gettype($filter).' given');
        }

        if (!in_array($filter, array_keys($this->pngFilterTypes))) {
            throw new RuntimeException(sprintf('png_compression_filter should be one or a combination of: "%s"', implode('', '', array_keys($this->pngFilterTypes))));
        }
        $params['filter'] = $this->pngFilterTypes[$filter];

        return $params;
    }

    /**
     * @return int
     */
    private function jpegOptions()
    {
        $jpegQuality = $this->getOption('jpeg_quality');

        if ($jpegQuality < 0 || $jpegQuality > 100) {
            $jpegQuality = 75;
            $this->options['jpeg_quality'] = $jpegQuality;
        }

        return $jpegQuality;
    }

    /**
     * @return array
     */
    private function wBmpOptions()
    {
        $foreground = $this->getOption('foreground');

        if (!is_array($foreground)) {
            $foreground = [0, 0, 0];
            $this->options['foreground'] = $foreground;
        }

        return $foreground;
    }
}
