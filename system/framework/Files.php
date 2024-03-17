<?php

namespace Sys\framework;

/**
 * Files Class
 *
 * This class provides functionality to handle file operations such as folder creation and file uploads.
 *
 * @category  Utility
 * @package   Core
 * @author    Mohd Fahmy Izwan Zulkhafri <faizzul14@gmail.com>
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @link      -
 * @version   1.0.0
 */
class Files
{
    /**
     * @var string The path
     */
    private $path = '../../';

    /**
     * @var string The upload directory path.
     */
    private $uploadDir = 'public/uploads';

    /**
     * @var int The maximum file size allowed in megabytes.
     */
    private $maxFileSize = 4;

    /**
     * @var mixed Allowed MIME types. Can be an array of MIME types, a single MIME type, or '*'.
     */
    private $allowedMimeTypes = '*';

    /**
     * Sets the upload directory.
     *
     * @param string $uploadDir The upload directory path.
     * @return void
     */
    public function setUploadDir(string $uploadDir, ?int $permission = 0775): void
    {
        $this->uploadDir = $uploadDir;
        $this->createFolder($permission);
    }

    /**
     * Sets the maximum file size allowed.
     *
     * @param int $maxFileSize The maximum file size allowed in megabytes.
     * @return void
     */
    public function setMaxFileSize(int $maxFileSize): void
    {
        $this->maxFileSize = $maxFileSize;
    }

    /**
     * Sets the allowed MIME types.
     *
     * @param mixed $allowedMimeTypes Allowed MIME types. Can be an array of MIME types, a single MIME type, or '*'.
     * @return void
     */
    public function setAllowedMimeTypes($allowedMimeTypes): void
    {
        $this->allowedMimeTypes = $allowedMimeTypes;
    }

    /**
     * Uploads a file.
     *
     * @param array $file The file to upload.
     * @return array An array containing upload status and file details.
     */
    public function upload(array $file): array
    {
        $targetDir = $this->path . $this->uploadDir . '/';
        $this->createFolder();

        $response = [
            'code' => 400,
            'message' => "",
            'files' => [
                'name' => $file["name"],
                'size' => $file["size"] ?? NULL,
                'path' => '',
                'folder' => $targetDir,
                'mime' => isset($file["tmp_name"]) && !empty($file["tmp_name"]) ? mime_content_type($file["tmp_name"]) : NULL,
            ],
            'isUpload' => false
        ];

        if ($file['error'] == UPLOAD_ERR_INI_SIZE) {
            $maxFileSize = ini_get('upload_max_filesize');
            $response['message'] = "The uploaded file exceeds the maximum file size limit of $maxFileSize. Please try uploading a smaller file.";
            return $response;
        }

        // Handle file upload
        $targetFile = $targetDir . basename($file["name"]);

        // Check file size
        if ($file["size"] > ($this->maxFileSize * 1024 * 1024)) {
            $response['message'] = "Sorry, your file exceeds the maximum file size of {$this->maxFileSize}MB.";
            return $response;
        }

        // Check file type
        $fileType = mime_content_type($file["tmp_name"]);
        if ($this->allowedMimeTypes !== '*') {
            if (is_array($this->allowedMimeTypes)) {
                $allowedTypes = implode(', ', $this->allowedMimeTypes);
                $response['message'] = "Sorry, only files of type(s) {$allowedTypes} are allowed.";
                if (!in_array($fileType, $this->allowedMimeTypes)) {
                    return $response;
                }
            } elseif (is_string($this->allowedMimeTypes)) {
                if ($fileType !== $this->allowedMimeTypes) {
                    $response['message'] = "Sorry, only files of type '{$this->allowedMimeTypes}' are allowed.";
                    return $response;
                }
            }
        }

        // Attempt to move the uploaded file
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $response['code'] = 200;
            $response['message'] = "The file has been uploaded";
            $response['files']['path'] = $targetFile;
            $response['isUpload'] = true;
        } else {
            $response['message'] = "Sorry, there was an error uploading your file.";
        }

        return $response;
    }

    /**
     * Creates a folder.
     *
     * This method creates a folder with the specified name within the provided upload directory.
     *
     * @param int|null $permission Optional. The permission mode for the created folder.
     * @return void
     */
    private function createFolder(?int $permission = 0775): void
    {
        $folderPath = $this->path . $this->uploadDir;

        // Check if folder already exists
        if (!is_dir($folderPath)) {
            // Create directory
            mkdir($folderPath, $permission, true);
        }
        touch($folderPath);
        chmod($folderPath, $permission);
    }
}
