<?php
/**
 * A service model class encapsulating the functionality for image management.
 */
namespace User\Service;

/**
 * The image manager service. Responsible for getting the list of uploaded
 * files and resizing the images.
 */
class ImageManager 
{
    /**
     * The directory where we save image files.
     * @var string
     */
    private $saveToDir = './public/files/utilizadores/';
        
    /**
     * Returns path to the directory where we save the image files.
     * @return string
     */
    public function getSaveToDir() 
    {
        return $this->saveToDir;
    }
    
    /**
     * Returns the path to the saved image file.
     * @param string $fileName Image file name (without path part).
     * @return string Path to image file.
     */
    public function getImagePathByName($fileName,$postId) 
    {
        // Take some precautions to make file name secure
        str_replace("/", "", $fileName);  // Remove slashes
        str_replace("\\", "", $fileName); // Remove back-slashes
                
        // Return concatenated directory name and file name.
        $savedir=$this->saveToDir.$postId.'/';
        return $savedir.$fileName;                
    }


    /**
     * Returns the array of saved file names.
     * @return array List of uploaded file names.
     */
    public function getSavedFiles($postId) 
    {
        // The directory where we plan to save uploaded files.
        $savedir=$this->saveToDir.$postId.'/';
        // Check whether the directory already exists, and if not,
        // create the directory.
        if(!is_dir($savedir)) {
            if(!mkdir($savedir)) {
                throw new \Exception('Could not create directory for uploads: '. error_get_last());
            }
        }
        
        // Scan the directory and create the list of uploaded files.
        $files = array();        
        $handle  = opendir($savedir);
        while (false !== ($entry = readdir($handle))) {
            
            if($entry=='.' || $entry=='..')
                continue; // Skip current dir and parent dir.
            
            $files[] = $entry;
        }
        
        // Return the list of uploaded files.
        return $files;
    }
    
    /**
     * Retrieves the file information (size, MIME type) by image path.
     * @param string $filePath Path to the image file.
     * @return array File information.
     */
    public function getImageFileInfo($filePath) 
    {
        // Try to open file        
        if (!is_readable($filePath)) {            
            return false;
        }
                
        // Get file size in bytes.
        $fileSize = filesize($filePath);

        // Get MIME type of the file.
        $finfo = finfo_open(FILEINFO_MIME);
        $mimeType = finfo_file($finfo, $filePath);
        if($mimeType===false)
            $mimeType = 'application/octet-stream';
        
        return [
            'size' => $fileSize,
            'type' => $mimeType 
        ];
    }
    
    /**
     * Returns the image file content. On error, returns boolean false. 
     * @param string $filePath Path to image file.
     * @return string|false
     */
    public function getImageFileContent($filePath) 
    {
        return file_get_contents($filePath);
    }


    /**
     * Resizes the image, keeping its aspect ratio.
     * @param string $filePath
     * @param int $desiredWidth
     * @return string Resulting file name.
     */
    public  function resizeImage($filePath, $desiredWidth = 240) 
    {
        // Get original image dimensions.
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        // Calculate aspect ratio
        $aspectRatio = $originalWidth/$originalHeight;
        // Calculate the resulting height
        $desiredHeight = $desiredWidth/$aspectRatio;
        



        // Resize the image
        $resultingImage = imagecreatetruecolor($desiredWidth, $desiredHeight);

        //$originalImage = imagecreatefromjpeg($filePath);
        $originalImage = imagecreatefromstring(file_get_contents($filePath));
        
        $stamp = imagecreatefrompng('./public/img/water.png');
        
        // Set the margins for the stamp and get the height/width of the stamp image

        
        list($originalWidths, $originalHeights) = getimagesize('./public/img/water.png');
        if($originalWidth<600){
            $stampw = imagecreate(150, 50);
            imagecopyresampled($stampw,$stamp, 0, 0, 0, 0, 
                150, 50, $originalWidths, $originalHeights);
            
            $marge_right = 10;
            $marge_bottom = 10;
            $sx = imagesx($stampw);
            $sy = imagesy($stampw);
            imagecopy($originalImage, $stampw, imagesx($originalImage) - $sx - $marge_right, imagesy($originalImage) - $sy - $marge_bottom, 0, 0, imagesx($stampw), imagesy($stampw));

        }else
        {
                     $stampw = imagecreate(400, 100);
            imagecopyresampled($stampw,$stamp, 0, 0, 0, 0, 
                400, 100, $originalWidths, $originalHeights);
            $marge_right = 10;
            $marge_bottom = 10;
            $sx = imagesx($stampw);
            $sy = imagesy($stampw);
            imagecopy($originalImage, $stampw, imagesx($originalImage) - $sx - $marge_right, imagesy($originalImage) - $sy - $marge_bottom, 0, 0, imagesx($stampw), imagesy($stampw));

        }

   
        imagecopyresampled($resultingImage,$originalImage, 0, 0, 0, 0, 
                $desiredWidth, $desiredHeight, $originalWidth, $originalHeight);

        // Save the resized image to temporary location
        $tmpFileName = tempnam("/tmp", "FOO");
        imagejpeg($resultingImage, $tmpFileName, 80);
        
        // Return the path to resulting image.
        return $tmpFileName;
    }
    
    
    
        /**
     * Resizes the image, keeping its aspect ratio.
     * @param string $filePath
     * @param int $desiredWidth
     * @return string Resulting file name.
     */
    public  function watermainImage($filePath) 
    {

        // Get original image dimensions.
        list($originalWidth, $originalHeight) = getimagesize($filePath);
        
        $desiredHeight=$originalHeight;
        $desiredWidth=$originalWidth;
      
        // Resize the image
        $resultingImage = imagecreatetruecolor($originalWidth, $originalHeight);

        $originalImage = imagecreatefromstring(file_get_contents($filePath));
        
        
        $stamp = imagecreatefrompng('./public/img/water.png');
        
        // Set the margins for the stamp and get the height/width of the stamp image

        list($originalWidths, $originalHeights) = getimagesize('./public/img/water.png');
        if($originalWidth<600){
            $stampw = imagecreate(150, 50);
            imagecopyresampled($stampw,$stamp, 0, 0, 0, 0, 
                150, 50, $originalWidths, $originalHeights);
            
            $marge_right = 10;
            $marge_bottom = 10;
            $sx = imagesx($stampw);
            $sy = imagesy($stampw);
            imagecopy($originalImage, $stampw, imagesx($originalImage) - $sx - $marge_right, imagesy($originalImage) - $sy - $marge_bottom, 0, 0, imagesx($stampw), imagesy($stampw));

        }else
        {
            $stampw = imagecreate(400, 100);
            imagecopyresampled($stampw,$stamp, 0, 0, 0, 0, 
                400, 100, $originalWidths, $originalHeights);
            $marge_right = 10;
            $marge_bottom = 10;
            $sx = imagesx($stampw);
            $sy = imagesy($stampw);
            imagecopy($originalImage, $stampw, imagesx($originalImage) - $sx - $marge_right, imagesy($originalImage) - $sy - $marge_bottom, 0, 0, imagesx($stampw), imagesy($stampw));

        }

        imagecopyresampled($resultingImage,$originalImage, 0, 0, 0, 0, 
                $desiredWidth, $desiredHeight, $originalWidth, $originalHeight);

        // Save the resized image to temporary location
        $tmpFileName = tempnam("/tmp", "FOO");
        imagejpeg($resultingImage, $tmpFileName, 80);
        
        // Return the path to resulting image.
        return $tmpFileName;
    }
}



