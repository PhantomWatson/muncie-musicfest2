<?php
namespace App\Media;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Imagine\Image\Box;

class Media
{

    /**
     * Handles an upload of a band-associated media file, makes
     * sure the filename is prefixed with the bandname, prevents
     * overwriting, and enforces allowed file types.
     *
     * @param string $uploadDir
     * @param array $fileTypes
     * @return array
     */
    public function upload($uploadDir, $fileTypes, $newFilename)
    {

        $verifyToken = md5(Configure::read('uploadToken').$_POST['timestamp']);

        if (empty($_FILES) || $_POST['token'] != $verifyToken) {
            return [
                'message' => 'Security code incorrect',
                'success' => false
            ];
        }

        // Validate extension
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $extension = strtolower($fileParts['extension']);
        if (! in_array($extension, $fileTypes)) {
            return [
                'message' => 'Invalid file type ('.$extension.')',
                'success' => false
            ];
        }

        // Abort if file exists
        $targetFile = $uploadDir.$newFilename;
        $existingFile = new File($targetFile);
        if ($existingFile->exists()) {
            return [
                'message' => 'File has already been uploaded',
                'success' => false
            ];
        }

        // Move file
        $tempFile = $_FILES['Filedata']['tmp_name'];
        if (move_uploaded_file($tempFile, $targetFile)) {
            return [
                'message' => 'Upload successful',
                'success' => true
            ];
        }
    }

    /**
     * Pulled from http://stackoverflow.com/a/2021729
     *
     * @param string $filename
     * @return string
     */
    public function sanitizeFilename($filename)
    {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following caracters -_~,;![]().
        // If you don't need to handle multi-byte characters
        // you can use preg_replace rather than mb_ereg_replace
        // Thanks @Łukasz Rysiak!
        $filename = mb_ereg_replace("([^\w\s\d\-_~,;!\[\]\(\).])", '', $filename);
        // Remove any runs of periods (thanks falstro!)
        $filename = mb_ereg_replace("([\.]{2,})", '', $filename);

        return $filename;
    }

    /**
     * Make a new "Band Name {$n}.ext" filename
     *
     * @param string $uploadDir
     * @return string
     */
    public function generatePictureFilename($uploadDir)
    {
        $bandsTable = TableRegistry::get('Bands');
        $band = $bandsTable->get($_POST['bandId']);
        $bandName = trim($band->name);
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $extension = strtolower($fileParts['extension']);
        $picturesTable = TableRegistry::get('Pictures');
        $pictureCount = $picturesTable->find('all')
            ->where(['band_id' => $_POST['bandId']])
            ->count();

        // Increment $n if it's necessary to create a unique filename
        for ($n = ($pictureCount + 1); true; $n++) {
            $newFilename = $this->sanitizeFilename("$bandName $n.$extension");
            $targetFile = $uploadDir.$newFilename;
            $existingFile = new File($targetFile);
            if (! $existingFile->exists()) {
                break;
            }
        }

        return $newFilename;
    }

    /**
     * Extracts what seems to be the name of the song, based on the filename
     *
     * @return string
     */
    public function extractTrackName($bandName)
    {
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $originalFilename = $fileParts['filename'];
        $bandPrefix = "$bandName - ";
        $strpos = stripos($originalFilename, $bandPrefix);
        if ($strpos === 0) {
            $trackName = substr($originalFilename, strlen($bandPrefix));
        } else {
            $trackName = $originalFilename;
        }
        return trim($trackName);
    }

    /**
     * Creates a filename that's prefixed with the band name and sanitized
     *
     * @param string $bandName
     * @param string $trackName
     * @return string
     */
    public function generateSongFilename($bandName, $trackName, $extension)
    {
        return $this->sanitizeFilename("$bandName - $trackName.$extension");
    }

    /**
     * Process the uploading of a song and adding a record to the 'songs' table
     *
     * @return array
     */
    public function uploadSong()
    {
        $uploadDir = ROOT.DS.'webroot'.DS.'music'.DS;
        $fileTypes = ['mp3'];
        $bandsTable = TableRegistry::get('Bands');
        $band = $bandsTable->get($_POST['bandId']);
        $bandName = trim($band->name);
        $trackName = $this->extractTrackName($bandName);
        $fileParts = pathinfo($_FILES['Filedata']['name']);
        $extension = strtolower($fileParts['extension']);
        $newFilename = $this->generateSongFilename($bandName, $trackName, $extension);

        // Attempt to complete upload
        $result = $this->upload($uploadDir, $fileTypes, $newFilename);
        if (! $result['success']) {
            return $result;
        }

        // Determine length (in seconds) of song
        $getID3 = new \getID3;
        $trackInfo = $getID3->analyze($uploadDir.$newFilename);
        $seconds = round($trackInfo['playtime_seconds']);

        // Add record to database
        $songsTable = TableRegistry::get('Songs');
        $song = $songsTable->newEntity([
            'band_id' => $_POST['bandId'],
            'title' => $trackName,
            'filename' => $newFilename,
            'seconds' => $seconds
        ]);
        if ($song->errors()) {
            $result['success'] = false;
            $result['message'] = 'Error saving song to database: '.json_encode($song->errors());
        } else {
            $song = $songsTable->save($song);
        }

        $result['trackName'] = $trackName;
        $result['filename'] = $newFilename;
        $result['songId'] = $song->id;
        return $result;
    }

    /**
     * Process the uploading of a picture and adding a record to the 'pictures' table
     *
     * @return array
     */
    public function uploadPicture()
    {
        $uploadDir = ROOT.DS.'webroot'.DS.'img'.DS.'bands'.DS;
        $fileTypes = ['png', 'jpg', 'gif'];
        $newFilename = $this->generatePictureFilename($uploadDir);

        // Attempt to complete upload
        $result = $this->upload($uploadDir, $fileTypes, $newFilename);
        if (! $result['success']) {
            return $result;
        }

        $imagine = new \Imagine\Gd\Imagine();
        $imgPath = $uploadDir.$newFilename;
        $image = $imagine->open($imgPath);

        // Shrink image to max dimensions
        $maxDimension = 2000;
        $width = $image->getSize()->getWidth();
        $height = $image->getSize()->getHeight();
        if ($width > $maxDimension || $height > $maxDimension) {
            $size = new \Imagine\Image\Box(2000, 2000);
            $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET;
            $image->thumbnail($size, $mode)->save($imgPath);
        }

        // Create thumbnail
        $size = new \Imagine\Image\Box(200, 200);
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;
        $thumbPath = $uploadDir.'thumb'.DS.$newFilename;
        $image->thumbnail($size, $mode)->save($thumbPath);

        // Add record to database
        $picturesTable = TableRegistry::get('Pictures');
        $picture = $picturesTable->newEntity([
            'band_id' => $_POST['bandId'],
            'filename' => $newFilename
        ]);
        if ($picture->errors()) {
            $result['success'] = false;
            $result['message'] = 'Error saving picture to database: '.json_encode($picture->errors());
        } else {
            $picture = $picturesTable->save($picture);
        }

        $result['filename'] = $newFilename;
        $result['pictureId'] = $picture->id;
        return $result;
    }
}
