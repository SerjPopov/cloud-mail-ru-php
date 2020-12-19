<?php

namespace SergPopov\CloudMailRu;


/**
 * Class CloudMailRu.
 * @package SergPopov\CloudMailRu
 */
class CloudMailRu implements CloudMailRuInterface
{
    /**
     * @var CloudAuthorizer
     */
    private $authorizer;

    /**
     * @var CloudFolder
     */
    private $folder;

    /**
     * @var CloudFile
     */
    private $file;

    /**
     * CloudMailRu constructor.
     * @param string $user
     * @param string $password
     */
    public function __construct(string $user, string $password)
    {
        $this->authorizer = new CloudAuthorizer($user, $password);
        $this->folder = new CloudFolder($this->authorizer);
        $this->file = new CloudFile($this->authorizer);
    }

    /**
     * Authorization in the cloud.
     * This method must be performed at the beginning of working with the cloud.
     * @return CloudMailRu
     * @throws CloudMailRuException
     */
    public function login()
    {
        $this->authorizer->login();
        return $this;
    }

    /**
     * Getting a list of directories and files in a specified directory.
     * @param string $pathOnCloud
     * @return array An array describing the contents of the directory.
     *  [
     *      'folders' => [
     *              'folder1',
     *              'folder2',
     *          ],
     *      'files' => [
     *              'file1.txt',
     *              'file2.jpg',
     *          ],
     *  ];
     * @throws CloudMailRuException
     */
    public function folderList(string $pathOnCloud)
    {
        $responseStr = $this->folder->folderList($pathOnCloud);
        return FolderHelper::parseFolderList($responseStr);
    }

    /**
     * Adding a directory to the cloud.
     * If nonexistent directories are present in the path, they will be created.
     * @param string $pathFolderOnCloud Full path of the created directory in the cloud.
     *                                  '/' - root directory.
     * @return CloudMailRu
     * @throws CloudMailRuException
     */
    public function folderAdd(string $pathFolderOnCloud)
    {
        $this->folder->folderAdd($pathFolderOnCloud);
        return $this;
    }

    /**
     * Uploading a file to the cloud.
     * If nonexistent directories are present in the path, they will be created.
     * If a file with the same name already exists in the cloud,
     * then the new file will NOT be replaced,
     * and an exception will NOT be thrown.
     * @param string $pathLocalFile Path in the filesystem to the downloaded file.
     * @param string $pathFileOnCloud Full path to the file in the cloud.
     *                                '/' - root directory.
     * @return CloudMailRu
     * @throws CloudMailRuException
     */
    public function fileUpload(string $pathLocalFile, string $pathFileOnCloud)
    {
        $this->file->fileUpload($pathLocalFile, $pathFileOnCloud);
        return $this;
    }

    /**
     * Deleting a file from the cloud.
     * @param string $pathOnCloud Full path to the file in the cloud.
     *                            '/' - root directory.
     * @return CloudMailRu
     * @throws CloudMailRuException
     */
    public function fileRemove(string $pathOnCloud)
    {
        $this->file->fileRemove($pathOnCloud);
        return $this;
    }


    /**
     * Obtaining a public link for downloading a file by any unauthorized user.
     * Example URL: https://cloud.mail.ru/public/5gww/3RUS5aTTT
     * @param string $pathOnCloud Full path to the file in the cloud.
     *                            '/' - root directory.
     * @return string
     * @throws CloudMailRuException
     */
    public function filePublish(string $pathOnCloud)
    {
        return $this->file->filePublish($pathOnCloud);
    }
}