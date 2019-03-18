<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 19/04/16
 * Time: 11:30
 */

namespace App\Model\Util;


class ImageBase64
{

    private function readImage($pathImage) {
        if (!file_exists($pathImage)) {
            throw new \Exception("File image not exists!");
        }

        $binaryImage = file_get_contents($pathImage);
        $mime_type = mime_content_type($pathImage);

        return array(
            "image" => $binaryImage,
            "mime" => $mime_type
        );
    }

    /**
     * @param $pathImage
     * @return string 
     */
    public function castPathFile($pathImage) {
        $arrayImage = $this->readImage($pathImage);
        return "data:" . $arrayImage['mime'] . ';base64,' . base64_encode($arrayImage['image']);
    }
    
    public function castBinary($binaryImage, $mimeType = "image/png") {
        if (is_null($mimeType))  {
            $temporaryFile = tempnam(sys_get_temp_dir(), 'TMP_');
            file_put_contents($temporaryFile, $binaryImage);
            
            $mimeType = mime_content_type($temporaryFile);
        }

        return "data:image/" . $mimeType . ';base64,' . base64_encode($binaryImage);
    }

    public function castStream($streamImage, $mimeType = "image/png") {
        $binaryImage = stream_get_contents($streamImage);

        return "data:image/" . $mimeType . ';base64,' . base64_encode($binaryImage);
    }

}