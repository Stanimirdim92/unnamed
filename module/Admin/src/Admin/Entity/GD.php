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

use Admin\Entity\GDInterface;
use Admin\Exception\BadMethodCallException;
use Admin\Exception\InvalidArgumentException;
use Admin\Exception\RuntimeException;

final class GD implements GDInterface {

    /**
     * The GD library
     *
     * @var GD
     */
    private $gd = null;

    /**
     * @method __construct
     *
     * @param  string $version minimum GD version
     */
    public function __construct($version = '2.0.1')
    {
        $this->loadGDInfo();
        $this->checkGDVersion($version);
    }

    /**
     * Load GD library
     *
     * @throws BadMethodCallException if gd_info doesn't exists
     */
    private function loadGDInfo()
    {
        if (!function_exists('gd_info')) {
            throw new BadMethodCallException('GD library has not been installed');
        }

        $this->gd = gd_info();
    }

    /**
     * Check minimum needed GD version
     *
     * @param string $version
     * @throws InvalidArgumentException on invalid version
     */
    private function checkGDVersion($version = "2.0.1")
    {
        if (version_compare(GD_VERSION, $version, '<')) {
            throw new InvalidArgumentException(sprintf('GD2 version %s or higher is required', $version));
        }
    }

    /**
     * Check Free Type support
     *
     * @return bool
     */
    public function hasFreeTypeSupport()
    {
        return $this->gd['FreeType Support'];
    }

    /**
     * Check Free Type Linkage support
     *
     * @return string|null
     */
    public function getFreeTypeLinkage()
    {
        if ($this->hasFreeTypeSupport()) {
            return $this->gd['FreeType Linkage'];
        }
        return null;
    }

    /**
     * Check T1Lib support
     *
     * @return bool
     */
    public function hasT1LibSupport()
    {
        return $this->gd["T1Lib Support"];
    }

    /**
     * Check GIF file read support
     *
     * @return bool
     */
    public function hasGIFReadSupport()
    {
        return $this->gd["GIF Read Support"];
    }

    /**
     * Check GIF file creation support
     *
     * @return bool
     */
    public function hasGIFCreateSupport()
    {
        return $this->gd["GIF Create Support"];
    }

    /**
     * Check JPEG|JPG file support
     *
     * @return bool
     */
    public function hasJPEGSupport()
    {
        return $this->gd["JPEG Support"];
    }

    /**
     * Check PNG file support
     *
     * @return bool
     */
    public function hasPNGSupport()
    {
        return $this->gd["PNG Support"];
    }
}
