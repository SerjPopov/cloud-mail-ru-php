<?php

namespace SergPopov\CloudMailRu;

class FolderHelper
{
    /**
     * @param string $str
     * @return array an array describing the contents of the directory
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
    public static function parseFolderList(string $str): array
    {
        $list = json_decode($str)->body->list;

        if (!is_array($list)) {
            throw new CloudMailRuException('Error parsing data of folder list');
        }

        $folders = [];
        $files = [];
        foreach ($list as $item) {

            if ($item->type == null || $item->name == null) {
                throw new CloudMailRuException('Error parsing data of folder list - unknown data');
            }

            switch ($item->type) {
                case 'folder' :
                    $folders[] = $item->name;
                    break;
                case 'file' :
                    $files[] = $item->name;
                    break;
            }
        }

        return [
            'folders' => $folders,
            'files' => $files,
        ];
    }
}