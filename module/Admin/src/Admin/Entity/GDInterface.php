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
 * @copyright  2015 (c) Stanimir Dimitrov.
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    0.0.12
 * @link       TBA
 */

namespace Admin\Entity;

interface GDInterface {

    /**
     * Check Free Type support
     *
     * @return bool
     */
    public function hasFreeTypeSupport();

    /**
     * Check Free Type Linkage support
     *
     * @return string|null
     */
    public function getFreeTypeLinkage();

    /**
     * Check T1Lib support
     *
     * @return bool
     */
    public function hasT1LibSupport();

    /**
     * Check GIF file read support
     *
     * @return bool
     */
    public function hasGIFReadSupport();

    /**
     * Check GIF file creation support
     *
     * @return bool
     */
    public function hasGIFCreateSupport();

    /**
     * Check JPEG|JPG file support
     *
     * @return bool
     */
    public function hasJPEGSupport();

    /**
     * Check PNG file support
     *
     * @return bool
     */
    public function hasPNGSupport();
}
