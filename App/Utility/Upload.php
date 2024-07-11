<?php

declare(strict_types=1);

namespace App\Utility;

use App\Exception\FileFormatException;
use Exception;

class Upload {

    const array FILE_EXTENSIONS_ALLOWED = ['jpg', 'jpeg', 'png'];

    /**
     * Valider l'extension d'un fichier
     *
     * @param array $file
     *
     * @throws FileFormatException
     *
     * @return void
     */
    public static function validateFileExtension(array $file): void
    {
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

        if (!in_array($fileExtension, self::FILE_EXTENSIONS_ALLOWED)) {
            throw new FileFormatException("L'image doit être au format JPEG ou PNG");
        }
    }

    /**
     * Charger un fichier
     *
     * @param array $file
     * @param string $fileName
     *
     * @return string
     *
     * @throws Exception
     */
    public static function uploadFile(array $file, string $fileName): string
    {
        $currentDirectory = getcwd();
        $uploadDirectory = "/storage/";

        $fileSize = $file['size'];
        $fileTmpName = $file['tmp_name'];

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $pictureName = basename($fileName . '.'. $fileExtension);

        $uploadPath = $currentDirectory . $uploadDirectory . $pictureName;

        if ($fileSize > 4000000) {
            throw new FileFormatException("L'image ne doit pas dépasser 4 Mo");
        }

        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if ($didUpload) {
            return $pictureName;
        } else {
            throw new Exception("An error occurred while uploading the file");
        }
    }
}
