<?php

namespace App\Utility;

class Upload {


    public static function uploadFile($file, $fileName)
    {
        // Utiliser un chemin absolu vers le dossier public
        $uploadDirectory = dirname(dirname(__DIR__)) . "/public/storage/";

        $fileExtensionsAllowed = ['jpeg', 'jpg', 'png'];

        // Vérifier que le fichier a été correctement uploadé
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("Erreur lors de l'upload du fichier.");
        }

        $fileSize = $file['size'];
        $fileTmpName = $file['tmp_name'];

        // Vérifier que le fichier temporaire existe
        if (!file_exists($fileTmpName)) {
            throw new \Exception("Le fichier temporaire n'existe pas.");
        }

        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $pictureName = basename($fileName . '.'. $fileExtension);

        // S'assurer que le dossier storage existe
        if (!is_dir($uploadDirectory)) {
            if (!mkdir($uploadDirectory, 0755, true)) {
                throw new \Exception("Impossible de créer le dossier de stockage.");
            }
        }

        // Vérifier que le dossier est accessible en écriture
        if (!is_writable($uploadDirectory)) {
            throw new \Exception("Le dossier de stockage n'est pas accessible en écriture.");
        }

        $uploadPath = $uploadDirectory . $pictureName;

        if (!in_array($fileExtension, $fileExtensionsAllowed)) {
            throw new \Exception("Cette extension de fichier n'est pas autorisée. Veuillez uploader un fichier JPEG ou PNG.");
        }

        if ($fileSize > 4000000) {
            throw new \Exception("Le fichier dépasse la taille maximale autorisée (4MB).");
        }

        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if ($didUpload) {
            return $pictureName;
        } else {
            throw new \Exception("Une erreur s'est produite lors de l'upload. Veuillez réessayer.");
        }
    }
}
