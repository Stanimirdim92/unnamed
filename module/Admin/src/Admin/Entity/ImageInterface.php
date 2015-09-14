<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2015 Stanimir Dimitrov <stanimirdim92@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author     Stanimir Dimitrov <stanimirdim92@gmail.com>
 * @copyright  2015 Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.12
 * @link       TBA
 */

namespace Admin\Entity

interface ImageInterface {

    /**
     * Holds all config data for all methods
     *
     * @param array $options
     */
    public function setOptions(array $options = []);

    /**
     * Get all options set
     */
    public function getOptions();

    /**
     * Set an individual option
     *
     * Keys are normalized to lowercase.
     *
     * @param  string $option
     * @param  mixed $value
     */
    public function setOption($option, $value);

    /**
     * Get an individual option
     *
     * Keys are normalized to lowercase.
     *
     * Returns null for unfound options
     *
     * @param  string $option
     * @return mixed
     */
    public function getOption($option);

    /**
     * The function will return false for invalid images
     */
    public function getImageInfo();

    /**
     * Extract the file format by mime-type
     *
     * This function will throw exceptions for invalid images / mime-types
     */
    private function extractImageFormat();

    /**
     * Try to create a new image from the supplied file
     */
    private function createImageFromFormat();

    /**
     * See if we can create GIF images
     */
    private function imageCreateFromGIF();

    /**
     * See if we can create JEPG|JPG images
     */
    private function imageCreateFromJPEG();

    /**final
     * See if we can create PNG images
     */
    private function imageCreateFromPNG();

    /**
     * See if we can create WEBP images
     */
    private function imageCreateFromWEBP();

    /**
     * Create the image with the given width and height
     *
     * @param int width
     * @param int height
     *
     */
    public function resize($width = 1, $height = 1);
}
