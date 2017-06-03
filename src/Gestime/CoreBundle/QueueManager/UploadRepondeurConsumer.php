<?php

namespace Gestime\CoreBundle\QueueManager;

use Symfony\Component\DependencyInjection\ContainerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Process\Process;

/**
 * Consommateur RabbitMQ
 *
 */
class UploadRepondeurConsumer implements ConsumerInterface
{
    protected $container;
    protected $entityManager;
    /**
     *  Main constructor
     *
     *  @param (ContainerInterface) $container
     *  @param (EntityManager)      $entityManager
     *
     *  @return (void)
     */
    public function __construct(ContainerInterface $container,  $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     *  Main execute method
     *  Execute actions for a given message
     *
     *  @param (AMQPMessage) $msg An instance of `PhpAmqpLib\Message\AMQPMessage` with the $msg->body being the data sent over RabbitMQ.
     *
     *  @return (boolean) Execution status (true if everything's of, false if message should be re-queued)
     */
    public function execute(AMQPMessage $msg)
    {
        echo 'Debut du traitement'."\n";

        $repondeurToUploadId = unserialize($msg->body);

        echo 'Id repondeur à traiter: '.$repondeurToUploadId."\n";
      $repondeurToUpload = $this->entityManager
                     ->getRepository('GestimeCoreBundle:Repondeur')
                     ->findOneByTag($repondeurToUploadId);

        echo 'Traitement du répondeur: '.$this->getFullPathName($repondeurToUpload->getName())."\n";

        // Transforme le fichier en CCIT ULaw (Compatible ACP)
        if (!$this->tranformToUlaw($repondeurToUpload->getName())) {
            return false;
        }

        echo 'Telechargement du répondeur: '.$this->getFullPathName($repondeurToUpload->getName())."\n";

        // Upload repondeur sur le repertoire distant
        if (!$this->uploadRepondeurToRemote($repondeurToUpload->getName(),
            $repondeurToUpload->getName()
        )
        ) {
            return false;
        }
        echo 'Fin du traitement'."\n";
    }

    protected function getRepondeurDir()
    {
        return $this->container->get('kernel')->getRootDir().'/'.$this->container->getParameter('upload_dir');
    }

    protected function getProcessULawDir()
    {
        return $this->container->get('kernel')->getRootDir().'/../repondeurs/'.'wma2ulaw.sh ';
    }

    protected function getFullPathName($filename)
    {
        return $this->getRepondeurDir().'/'.$filename;
    }

    /**
     *  Upload Repondeur to FTP Location
     *  @param (Repondeut) $repondeur répondeur téléchargé
     *
     *  @return (boolean) Process Status
     */
    protected function tranformToUlaw($fileName)
    {
        $process = new Process($this->getProcessULawDir().' '.$fileName.' '.$this->getRepondeurDir());
        $process->run();
        if (!$process->isSuccessful()) {
            return false;
        }

        return true;
    }

    /**
     *  Upload Repondeur to FTP Location
     *  @param (string) $downloadImagePath          Download image path
     *  @param (string) $saveImagePath              Save image path
     *
     *  @return (boolean) Download status (or true if file already exists)
     */
    protected function uploadRepondeurToRemote($localFileName, $distantFileName)
    {
        // Upload repondeur (Gaufrette)
        $status = true;
        try {
            $repondeurUploader = $this->container->get('gestime.repondeur_uploader');
            $status = $repondeurUploader->upload($this->getFullPathName($localFileName), $distantFileName);
        } catch (\Exception $e) {
            $status = false;
            echo 'Erreur au téléchargement du répondeur '.$distantFileName."\n";
        }

        // Return upload status
        return $status;
    }
}
