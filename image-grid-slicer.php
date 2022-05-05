<?php

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

$srcImagePath = '';
$rows = 0;
$columns = 0;

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

if (!preg_match('/\.(gif|jpe?g|png)/i', $srcImagePath)) {
    throw new ErrorException("The file specified does not appear to be an image");
}

$srcImageFileName = preg_replace('/^(?:.+\/)?([^\/]+)$/i', '$1', $srcImagePath);
if (false === strpos($srcImagePath, "/")) {
    $srcImagePath = __DIR__ . '/';
} else {
    $srcImagePath = preg_replace('/^(.+\/)?(?:[^\/]+)$/i', '$1', $srcImagePath);
}

$imagick = new Imagick($srcImagePath . $srcImageFileName);

$sliceBaseName = preg_replace('/^(.+)\.(?:gif|jpe?g|png)$/i', '$1', $srcImageFileName);
$srcImageWidth = $imagick->getImageWidth();
$srcImageHeight = $imagick->getImageHeight();
$sliceWidth = $srcImageWidth / $columns;
$sliceHeight = $srcImageHeight / $rows;

for ($row = 0; $row < $rows; $row++) {
    for ($col = 0; $col < $columns; $col++) {
        $new_imagick = $imagick->getImageRegion( (int) $sliceWidth,
            (int) $sliceHeight,
            (int) ($col * $sliceWidth),
            (int) ($row * $sliceHeight)
        );
        $new_imagick->writeImage($srcImagePath . "{$sliceBaseName}_slice_r{$row}_c$col.png");
    }
}