<?php

/**
 * RepondeurUpload class file
 *
 * PHP Version 5.5
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\TelephonieBundle\Upload;

use Gaufrette\Filesystem;

/**
 * Upload des répondeurs sur l'ACP
 *
 */
class RepondeurUploader
{
    private $filesystem;

    /**
     * [__construct description]
     * @param Filesystem $filesystem [description]
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * [upload description]
     * @param string $localFileName
     * @param string $distantFileName
     * @param string $contentType
     * @return boolean
     */
    public function upload($localFileName, $distantFileName, $contentType = 'audio/wav')
    {
        $filename = $localFileName;
        $adapter = $this->filesystem->getAdapter();
        ////$adapter->setMetadata($distantFileName, array('contentType' => $contentType ));
        $adapter->write($distantFileName, file_get_contents($localFileName));

        return true;
    }
}
