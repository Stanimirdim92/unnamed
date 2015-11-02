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

interface GDInterface
{
    /**
     * Check Free Type support.
     *
     * @return bool
     */
    public function hasFreeTypeSupport();

    /**
     * Check Free Type Linkage support.
     *
     * @return string|null
     */
    public function getFreeTypeLinkage();

    /**
     * Check T1Lib support.
     *
     * @return bool
     */
    public function hasT1LibSupport();

    /**
     * Check GIF file read support.
     *
     * @return bool
     */
    public function hasGIFReadSupport();

    /**
     * Check GIF file creation support.
     *
     * @return bool
     */
    public function hasGIFCreateSupport();

    /**
     * Check JPEG|JPG file support.
     *
     * @return bool
     */
    public function hasJPEGSupport();

    /**
     * Check PNG file support.
     *
     * @return bool
     */
    public function hasPNGSupport();
}
