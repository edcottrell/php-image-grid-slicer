This is a simple command-line tool to slice an image into a grid using PHP. It relies on core PHP functionality and the Imagick PHP extension but has no other dependencies.

# Usage

The tool can be invoked in two ways:

```bash
# 1) Using a command-file-rows-columns syntax:
# php image-grid-slicer.php image_file rows columns 
#
# Example:

php image-grid-slicer.php image.png 3 4 

# 2) Using parameters:
# php image-grid-slicer.php image_file --rows rows --columns columns 
# OR
# php image-grid-slicer.php image_file --columns columns --rows rows
#
# Examples:

php image-grid-slicer.php image.png --rows 3 --columns 4
php image-grid-slicer.php image.png --columns 6 --rows 1
```

Files are automatically named as *BASE\_FILE\_NAME*\_slice\_r\_*row*\_c\_*column*.*original_extension*, *e.g.*, `My Image_slice_r2_c3.png`.

# Future Plans

My plans for this library depend on what users ask for, any pull requests I receive, and my own needs. In general, though, I'll likely prioritize the following features:

- wrap this up in a function so that it can be used as either a command line tool or an add-in in other projects
- specify uneven row and column sizes
- specify a different output file naming format (prefix and suffix, like `My Image 7` for sequentially-numbered slices)
- specify an output file format (.jpeg to .png, for example)
- specify resolutions for output files