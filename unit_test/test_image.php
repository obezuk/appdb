<?php

/* unit tests for class image */

require_once("path.php");
require_once("test_common.php");

DEFINE(TEST_IMAGE_FILENAME, "/images/appdb_montage.jpg");
DEFINE(TEST_IMAGE_OUTPUT_FILENAME, "/tmp/tmpfile.png");
DEFINE(TEST_IMAGE_WIDTH, 391);
DEFINE(TEST_IMAGE_HEIGHT, 266);
DEFINE(TEST_IMAGE_WATERMARK, "/images/watermark.png");

function test_image_constructor()
{
    test_start(__FUNCTION__);

    $sImageFilename = TEST_IMAGE_FILENAME;

    /* create a new image from a known image file */
    $oImage = new Image($sImageFilename);

    if(!$oImage->isLoaded())
    {
        echo "Error, unable to load image filename of ".$sImageFilename."\n";
        echo "Internal filename is: ".$oImage->sFile."\n";
        return false;
    }

    /* make sure the image size is correct */
    if($oImage->get_width() != TEST_IMAGE_WIDTH)
    {
        echo "Expected width of ".TEST_IMAGE_WIDTH.", got ".$oImage->get_width()."\n";
        return false;
    }

    if($oImage->get_height() != TEST_IMAGE_HEIGHT)
    {
        echo "Expected width of ".TEST_IMAGE_HEIGHT.", got ".$oImage->get_height()."\n";
        return false;
    }

    /* test that isLoaded() returns false if we create an */
    /* image object from a file that doesn't exist */
    $oImage = new Image("somefilethatdoesntexist.png");
    if($oImage->isLoaded())
    {
        echo "Error, isLoaded() returned true for a image that doesn't exist, expected false!\n";
        return false;
    }
    
    return true;
}

function test_image_make_thumbnail()
{
    test_start(__FUNCTION__);

    $sImageFilename = TEST_IMAGE_FILENAME;

    /* create a new image from a known image file */
    $oImage = new Image($sImageFilename);

    if(!$oImage->isLoaded())
    {
        echo "Error, unable to load image filename of ".$sImageFilename."\n";
        echo "Internal filename is: ".$oImage->sFile."\n";
        return false;
    }

    $iWidth = 100;
    $iHeight = ($iWidth * TEST_IMAGE_HEIGHT) / TEST_IMAGE_WIDTH; /* compute the expected height
                                                                  from the ratio of the height
                                                                  to width of the original image */
    $iBorderWidth = 20;
    $oImage->make_thumb($iWidth, $iHeight, $iBorderWidthm, "#0000FF");

    /* did we get the correct size? */
    $iActualWidth = $oImage->get_width();
    if($iActualWidth != $iWidth)
    {
        echo "Expected width of $iWidth, got ".$iActualWidth."\n";
        echo $oImage->get_debuglog(false);
        return false;
    }

    $iActualHeight = $oImage->get_height();
    if($iActualHeight != $iHeight)
    {
        echo "Expected height of $iHeight, got ".$iActualHeight."\n";
        echo $oImage->get_debuglog(false);
        return false;
    }

    return true;
}

function test_image_make_full()
{
    test_start(__FUNCTION__);

    $sImageFilename = TEST_IMAGE_FILENAME;

    /* create a new image from a known image file */
    $oImage = new Image($sImageFilename);

    if(!$oImage->isLoaded())
    {
        echo "Error, unable to load image filename of ".$sImageFilename."\n";
        echo "Internal filename is: ".$oImage->sFile."\n";
        return false;
    }

    $iWidth = 1000;
    $iHeight = ($iWidth * TEST_IMAGE_HEIGHT) / TEST_IMAGE_WIDTH;
    $oImage->make_full($iWidth, $iHeight);

    /* we expect the width and height to be limited to the size of the image */
    $iWidth = TEST_IMAGE_WIDTH;
    $iHeight = TEST_IMAGE_HEIGHT;

    /* did we get the correct size? */
    $iActualWidth = $oImage->get_width();
    if($iActualWidth != $iWidth)
    {
        echo "Expected width of $iWidth, got ".$iActualWidth."\n";
        echo $oImage->get_debuglog(false);
        return false;
    }

    $iActualHeight = $oImage->get_height();
    if($iActualHeight != $iHeight)
    {
        echo "Expected height of $iHeight, got ".$iActualHeight."\n";
        echo $oImage->get_debuglog(false);
        return false;
    }

    return true;
}

function test_image_output_to_file()
{
    test_start(__FUNCTION__);

    $sImageFilename = TEST_IMAGE_FILENAME;

    /* create a new image from a known image file */
    $oImage = new Image($sImageFilename);

    if(!$oImage->isLoaded())
    {
        echo "Error, unable to load image filename of ".$sImageFilename."\n";
        echo "Internal filename is: ".$oImage->sFile."\n";
        return false;
    }

    /* write the file to disk */
    if(!$oImage->output_to_file(TEST_IMAGE_OUTPUT_FILENAME))
    {
        echo "image::output_to_file failed to output to filename of ".TEST_IMAGE_OUTPUT_FILENAME."\n";
        return false;
    }

    /* check that we can load this file up */
    $oImage2 = new Image(TEST_IMAGE_OUTPUT_FILENAME, true);
    if(!$oImage2->isLoaded())
    {
        echo "Error, unable to load newly output image filename of ".TEST_IMAGE_OUTPUT_FILENAME."\n";
        echo "Internal filename is: ".$oImage2->sFile."\n";
        return false;
    }

    /* and make sure we can now remove it */
    $oImage2->delete();

    /* and check that it is unlinked by trying to open it up again */
    $oImage2 = new Image(TEST_IMAGE_OUTPUT_FILENAME, true);
    if($oImage2->isLoaded())
    {
        echo "Error, unlinking filename of ".TEST_IMAGE_OUTPUT_FILENAME." failed, we are able to\n";
        echo "  open up a file that should have been deleted.\n";
        echo "Internal filename is: ".$oImage2->sFile."\n";
        return false;
    }

    return true;
}

function test_image_add_watermark()
{
    test_start(__FUNCTION__);

    $sImageFilename = TEST_IMAGE_FILENAME;

    /* create a new image from a known image file */
    $oImage = new Image($sImageFilename);

    if(!$oImage->isLoaded())
    {
        echo "Error, unable to load image filename of ".$sImageFilename."\n";
        echo "Internal filename is: ".$oImage->sFile."\n";
        return false;
    }

    /* load the watermark up */
    $oWatermark = new Image(TEST_IMAGE_WATERMARK);
    if(!$oWatermark->isLoaded())
    {
        echo "Error, unable to load image filename of ".TEST_IMAGE_WATERMARK."\n";
        echo "Internal filename is: ".$oWatermark->sFile."\n";
        return false;
    }

    $oImage->add_watermark($oWatermark->get_image_resource(), 50, 50);

    return true;
}




if(!test_image_constructor())
    echo "test_image_constructor() failed!\n";
else
    echo "test_image_constructor() passed\n";

if(!test_image_make_thumbnail())
    echo "test_image_make_thumbnail() failed!\n";
else
    echo "test_image_make_thumbnail() passed\n";

if(!test_image_make_full())
    echo "test_image_make_full() failed!\n";
else
    echo "test_image_make_full() passed\n";

if(!test_image_output_to_file())
    echo "test_image_output_to_file() failed!\n";
else
    echo "test_image_output_to_file() passed\n";

if(!test_image_add_watermark())
    echo "test_image_add_watermark() failed!\n";
else
    echo "test_image_add_watermark() passed\n";

?>