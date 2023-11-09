This is a simple class and command-line tool to slice an image into a grid using PHP. It relies on core PHP functionality and the [Imagick](https://github.com/Imagick/imagick) PHP extension but has no other dependencies.

# Usage

The tool can be invoked in three ways:

1. By including it and using the class in a PHP application, as in the following:

    ```php
    include 'image-grid-slicer.php';
    $srcImagePath = 'ham.gif';
    $rows = 2;
    $columns = 2;
    $slicer = new phpImageGridSlicer($srcImagePath, $rows, $columns);
    $slicer->slice();
    ```

2. On the command line, using a syntax consisting simply of the file name, the number of rows, and the number of columns:
    ```bash
    # This method uses a command-file-rows-columns syntax:
    # php image-grid-slicer.php image_file rows columns 
    #
    # Example:
    
    php image-grid-slicer.php image.png 3 4
    ```
3. On the command line, using named parameters:
    ```bash
    # php image-grid-slicer.php image_file --rows rows --columns columns 
    # OR
    # php image-grid-slicer.php image_file --columns columns --rows rows
    #
    # Examples:
    
    php image-grid-slicer.php image.png --rows 3 --columns 4
    php image-grid-slicer.php image.png --columns 6 --rows 1
    ```

Files are automatically named as `{$BASE_FILE_NAME}_slice_r_{$ROW}_c_{$COLUMN}.{$ORIGINAL_EXTENSION}`, *e.g.*, `My Image_slice_r2_c3.png`.

The tool will throw `ErrorException`s for invalid parameters or if the file specified does not appear to be an image based on its MIME type or extension. It will not throw any other type of exception or error directly, but it will pass through `ImagickException`s or any other exceptions thrown by Imagick. 

# Future Plans

My plans for this library depend on what users ask for, any pull requests I receive, and my own needs. In general, though, I'll likely prioritize the following features:

- specify a different output directory (right now, it saves slices to the same directory as the original image)
- specify a different output file naming format (prefix and suffix, like `My Image 7` for sequentially-numbered slices)
- specify an output file format (.jpeg to .png, for example)
- specify uneven row and column sizes
- specify resolutions for output files