<?php

namespace ImageGridSlicer;

use ErrorException;
use Imagick;
use ImagickException;

/**
 * Ed Cottrell's tool for slicing an image along a grid.
 * (c) 2022 Ed Cottrell
 *
 * In this initial version, the tool simply slices a given image into P
 * columns and Q rows of equal size, saving each slice as a PNG file.
 *
 * In the future, it may allow unevenly-sized rows and columns,
 * alternative output formats, custom naming schemes, etc.
 */

class ImageGridSlicer {
    private string $sliceBaseFileName;
    public string $srcImagePath;
    public string $srcImageFileName;
    public int $rows = 0;
    public int $columns = 0;
    private Imagick $imagickObj;

    public function __construct(?string $srcImagePath, ?int $rows, ?int $columns)
    {
        $this->srcImagePath = $srcImagePath;
        $this->rows = $rows;
        $this->columns = $columns;
        if ($this->srcImagePath) {
            $this->loadImageFromFile($srcImagePath);
        }
    }

    public function getSliceBaseFileName(): string
    {
        $this->sliceBaseFileName = preg_replace('/^(.+)\.(?:gif|jpe?g|png)$/i', '$1', $this->srcImageFileName);
        return $this->sliceBaseFileName;
    }

    /**
     * @throws ErrorException
     * @throws ImagickException
     */
    public function loadImageFromFile(?string $srcImagePath) : Imagick
    {
        if (!empty($srcImagePath)) {
            $this->srcImagePath = $srcImagePath;
        }
        $this->validateImageIsImage();

        // Get Image File path
        $this->srcImageFileName = preg_replace('/^(?:.+\/)?([^\/]+)$/i', '$1', $this->srcImagePath);
        if (false === strpos($this->srcImagePath, "/")) {
            $srcImagePath = __DIR__ . '/';
        } else {
            $srcImagePath = preg_replace('/^(.+\/)?(?:[^\/]+)$/i', '$1', $this->srcImagePath);
        }

        // Load the Image
        $this->imagickObj = new Imagick($srcImagePath . $this->srcImageFileName);

        return $this->imagickObj;
    }

    /** @noinspection PhpUnused */
    public function loadImagickImage(Imagick $imagickObj) : Imagick
    {
        $this->imagickObj = $imagickObj;
        $this->sliceBaseFileName = null;
        $this->srcImagePath = null;
        $this->srcImageFileName = null;
        return $this->imagickObj;
    }

    /**
     * @throws ImagickException
     */
    public function slice() : void {
        // Figure out how to slice the image
        $srcImageWidth = $this->imagickObj->getImageWidth();
        $srcImageHeight = $this->imagickObj->getImageHeight();
        $sliceWidth = $srcImageWidth / $this->columns;
        $sliceHeight = $srcImageHeight / $this->rows;

        $this->getSliceBaseFileName();

        // Slice away
        for ($row = 0; $row < $this->rows; $row++) {
            for ($col = 0; $col < $this->columns; $col++) {
                $new_imagick = $this->imagickObj->getImageRegion( (int) $sliceWidth,
                    (int) $sliceHeight,
                    (int) ($col * $sliceWidth),
                    (int) ($row * $sliceHeight)
                );
                $new_imagick->writeImage($this->srcImagePath . "{$this->sliceBaseFileName}_slice_r{$row}_c$col.png");
            }
        }
    }

    /**
     * @throws ErrorException
     */
    private function validateImageIsImage() : void {
        if (!preg_match('/\.(gif|jpe?g|png)/i', $this->srcImagePath)) {
            throw new ErrorException("Security Exception: File $this->srcImagePath does not appear to be an image file");
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $this->srcImagePath);
        $allowedMimeTypes = [
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/png'
        ];
        if (in_array($mimeType, $allowedMimeTypes) === false) {
            throw new ErrorException("Security Exception: File $this->srcImagePath does not appear to be an image file");
        }
    }
}

// If called directly from the CLI, then process the arguments
if (!empty($argv) && basename($argv[0]) === basename(__FILE__)) {
    // Parse Command Line
    if ($argc <= 1) {
        throw new ErrorException("You must specify an image, the number of rows, and the number of columns");
    }
    $arguments = join(" ", array_slice($argv, 1));
    $matched = preg_match('/^(?P<srcImagePath>.+) +(?P<rows>\d+) +(?P<columns>\d+)$/i', $arguments, $matches);
    if ($matched) {
        $srcImagePath = $matches['srcImagePath'];
        $rows = $matches['rows'];
        $columns = $matches['columns'];
    } else {
        $matched = preg_match('/^(?P<srcImagePath>.+?) +--/i', $arguments, $matches);
        if (!$matched) {
            throw new ErrorException("You must specify the number of rows");
        }
        $srcImagePath = $matches['srcImagePath'];

        $matched = preg_match('/(?:--rows|-r) +(?P<rows>\d+)/i', $arguments, $matches);
        if (!$matched) {
            throw new ErrorException("You must specify the number of rows");
        }
        $rows = $matches['rows'];

        $matched = preg_match('/(?:--columns|-c) +(?P<columns>\d+)/i', $arguments, $matches);
        if (!$matched) {
            throw new ErrorException("You must specify the number of columns");
        }
        $columns = $matches['columns'];
    }

    $pigs = new ImageGridSlicer($srcImagePath, $rows, $columns);
    $pigs->slice();
}