<?php

const SCAN_DIR = '../../../../../Volumes/video';

class CreateMovieFiles
{
    public function execute(): void
    {
        $index = 0;
        echo "Start with scanning a given folder ..." . PHP_EOL;
        $array = $this->dirToArray(SCAN_DIR);

        echo PHP_EOL . "... build an array ..." . PHP_EOL;
        $singleArray = [];
        $defaultArray = [];
        $subMovieArray = [];
        foreach ($array as $key => $item) {
            if (is_int($key)) {
                $singleArray[$index] = [
                    'title' => $item
                ];
            }
            if (is_array($item)) {
                if (count($item) < 4) {
                    $defaultArray[$index] = [
                        'folder' => $key,
                        'content' => $item
                    ];
                } else {
                    $subMovieArray[$index] = [
                        'folder' => $key,
                        'content' => $item
                    ];
                }
            }
            $index++;
        }
        $result = array_merge($defaultArray, $subMovieArray);

        echo PHP_EOL . "... and write to files" . PHP_EOL;
        $this->writeToFile($singleArray, 'singleLineResult.json');
        $this->defaultMovieArray($defaultArray);
        $this->subMovieArray($subMovieArray);
    }

    private function defaultMovieArray($aFolders): void
    {
        $aDefaultArray = [];
        foreach ($aFolders as $key => $folder) {
            $subtitle = '';
            $picture = '';
            $title = '';
            $tmpFile = '';
            $fileSize = 0;
            foreach ($folder['content'] as $item) {
                $extension = pathinfo($item, PATHINFO_EXTENSION);
                switch ($extension) {
                    case 'srt':
                    case 'sub':
                        $subtitle = $item;
                        break;
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                        $picture = $item;
                        break;
                    case 'mp4':
                    case 'mkv':
                    case 'avi':
                        $title = $item;
                        $fileSize = filesize(SCAN_DIR . '/' . $folder['folder'] . '/' . $item);
                        break;
                    default:
                        $tmpFile = $item;
                        break;
                }
                unset($item);
            }
            $aDefaultArray[$key] = [
                'folder' => $folder['folder'],
                'title' => $title,
                'subTitles' => $subtitle,
                'picture' => $picture,
                'fileSize' => $fileSize,
                'other' => $tmpFile
            ];
            unset($tmpFile);
            unset($title);
            unset($subtitle);
            unset($picture);
            unset($fileSize);
            unset($folder);
        }
        array_multisort($aDefaultArray);
        $fileName = 'defaultResult.json';
        $this->writeToFile($aDefaultArray, $fileName);
    }

    // TODO this is not ready
    private function subMovieArray($aContent): void
    {
        print_r($aContent);
        $subtitle = '';
        $picture = '';
        $title = '';
        $tmpFile = '';
        $aFoldersArray = [];
        foreach ($aContent as $content) {
            if (isset($content['content'])) {
                if (in_array('recycle', $content) ||
                    in_array('privat', $content)
                ) {
                    continue;
                }

                print_r($content);

                foreach ($content['content'] as $item) {

                }
                //$aSubMovieArray[] = $content;
            }
        }
        array_multisort($aSubMovieArray);
        $fileName = 'subMovies.txt';
        print_r($aSubMovieArray);
        $this->writeToFile($aSubMovieArray, $fileName);
    }

    /**
     * @param $path
     *
     * @return array
     */
    private function getDirectoryContent($path): array
    {
        $aContent = [];
        $contents = scandir($path);
        foreach ($contents as $content) {
            if ($content != '.' && $content != '..' && !str_starts_with($content, '.')) {
                $aContent[] = $content;
            }
        }

        return $aContent;
    }

    /**
     * @param $path
     *
     * @return int
     */
    private function countContent($path): int
    {
        $size = 0;
        $ignore = array('.', '..');
        $files = scandir($path);
        foreach ($files as $t) {
            if (in_array($t, $ignore)) {
                continue;
            } else {
                $size++;
            }

        }
        return $size;
    }

    /**
     * @param $aContent
     *
     * @param $sFileName
     */
    private function writeToFile($aContent, $sFileName): void
    {
        $fp = fopen($_SERVER['PWD'] . "/app/mediaFiles/" . $sFileName, "wb");
        file_put_contents($_SERVER['PWD'] . "/app/mediaFiles/" . $sFileName, json_encode($aContent));
        fclose($fp);
        echo PHP_EOL . "File $sFileName created" . PHP_EOL;
        echo PHP_EOL . "Write entries into $sFileName" . PHP_EOL;
    }

    private function dirToArray($dir): array
    {

        $result = array();

        $cdir = scandir($dir);
        $excludeArray = ['.','..','recycle','symfony','privat'];

        foreach ($cdir as $key => $value) {
            if (!in_array($value, $excludeArray)) {
                if (str_starts_with($value, '.')) {
                    continue;
                }
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }

    private function buildMovieArray($array): void
    {
        foreach ($array as $key => $item) {
            if (is_int($key)) {
                print_r($item);
                echo "\n";
            }
        }
        //print_r($array);die;
    }

    /**
     * @return array
     */
    private function getMovieArray(): array
    {
        $aFolders = array();
        $index = 0;
        // CHECKING WHETHER PATH IS A DIRECTORY OR NOT
        if (is_dir(SCAN_DIR)) {
            // GETTING INTO DIRECTORY
            $handle = opendir(SCAN_DIR);
            {
                // CHECKING FOR SMOOTH OPENING OF DIRECTORY
                if ($handle) {
                    //READING NAMES OF EACH ELEMENT INSIDE THE DIRECTORY
                    while (($folder = readdir($handle)) !== false) {
                        // CHECKING FOR FILENAME ERRORS
                        $dirPath = $this->excludeFiles($folder);
                        // GETTING INSIDE EACH SUBFOLDERS
                        if (is_dir($dirPath)) {
                            $aContent = $this->getDirectoryContent($dirPath);
                            if (count($aContent) < 4) {
                                $subtitle = '';
                                $picture = '';
                                $title = '';
                                $tmpFile = '';
                                $fileSize = 0;
                                foreach ($aContent as $item) {
                                    $subFolderDirPath = $dirPath . $item . "/";
                                    if (is_dir($subFolderDirPath) === false) {
                                        $extension = pathinfo($item, PATHINFO_EXTENSION);
                                        switch ($extension) {
                                            case 'srt':
                                                $subtitle = $item;
                                                break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png':
                                                $picture = $item;
                                                break;
                                            case 'mp4':
                                            case 'mkv':
                                            case 'avi':
                                                $title = $item;
                                                $fileSize = filesize($dirPath . $item);
                                                break;
                                            default:
                                                $tmpFile = $item;
                                        }
                                        $aFolders[$index] = [
                                            'folder' => $folder,
                                            'title' => $title,
                                            'subTitles' => $subtitle,
                                            'picture' => $picture,
                                            'fileSize' => $fileSize,
                                            'other' => $tmpFile
                                        ];
                                    }
                                    if (is_dir($subFolderDirPath) === true) {
                                        $aFolders[$index] = [
                                            'subFolders' => $item
                                        ];
                                    }
                                }
                            } else {
                                $aFolders[$index] = [
                                    'folder' => $folder,
                                    'files' => $aContent
                                ];
                            }
                        } else {
                            $aFolders[$index] = [
                                'fileName' => $folder
                            ];
                        }
                        $index++;
                    }
                }
            }
            closedir($handle);
        }

        return $aFolders;
    }
}
