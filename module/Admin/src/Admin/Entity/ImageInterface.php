<?php
/**
 * MIT License.
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
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 *
 * @version    0.0.13
 *
 * @link       TBA
 */

namespace Admin\Entity;

interface ImageInterface
{
    /**
     * Get all options set.
     */
    public function getOptions();

    /**
     * Get an individual option.
     *
     * Keys are normalized to lowercase.
     *
     * Returns null for unfound options
     *
     * @param string $option
     */
    public function getOption($option);

    /**
     * The function will return false for invalid images.
     */
    public function getImageInfo();

    /**
     * Create the image with the given width and height.
     *
     * @param int width
     * @param int height
     */
    public function resize($width = 1, $height = 1);

    /**
     * @param string $path
     * @param string $fileName
     */
    public function save($path, $fileName);

    /**
     * Opens an existing image from $path.
     *
     * @param string $path
     */
    public function open($path);
}
