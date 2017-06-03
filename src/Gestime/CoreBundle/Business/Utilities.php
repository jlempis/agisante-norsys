<?php

namespace Gestime\CoreBundle\Business;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Gmap\Geocoder;

/**
 * Utilities
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class Utilities
{
    protected $container;
    private $entityManager;

    /**
     * [__construct description]
     * @param ContainerInterface $container
     * @param [type]             $entityManager
     */
    public function __construct(ContainerInterface $container, $entityManager)
    {
        $this->container = $container;
        $this->entityManager = $entityManager;
    }

    /**
     * [filenameWithoutExtension description]
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function filenameWithoutExtension($name)
    {
        return preg_replace('/\\.[^.\\s]{3,4}$/', '', $name);
    }

    /**
     * [time_overlap description]
     * @param \Datetime $startTime startTime (YYYY-MM-DD HH:MM:SS)
     * @param \Datetime $endTime   endTime (YYYY-MM-DD HH:MM:SS)
     * @param Array     $times
     * @return boolean
     */
    public function time_overlap($startTime, $endTime, $times)
    {
        if (count($times) == 0) {
            return false;
        }
        $ustart = strtotime($startTime);
        $uend   = strtotime($endTime);

        foreach ($times as $time) {
            $start = strtotime($time['start']);
            $end   = strtotime($time['end']);
            if ($ustart <= $end && $uend >= $start) {
                return true;
            }
        }

        return false;
    }

    /**
     * [writeArrayToFile description]
     * @param  [type] $directory [description]
     * @param  [type] $fileName  [description]
     * @param  [type] $data      [description]
     * @return [type]            [description]
     */
    public function writeArrayToFile($directory, $fileName, $data)
    {
        $handle = fopen($directory.'/'.$fileName, 'wb');
        if ($handle === false) {
            return false;
        }
        foreach ($data as $fields) {
            fputcsv($handle, $fields, ';');
        }
        fclose($handle);
    }

    function str_to_noaccent($str)
    {
        $url = $str;
        $url = preg_replace('#Ç#', 'C', $url);
        $url = preg_replace('#ç#', 'c', $url);
        $url = preg_replace('#è|é|ê|ë#', 'e', $url);
        $url = preg_replace('#È|É|Ê|Ë#', 'E', $url);
        $url = preg_replace('#à|á|â|ã|ä|å#', 'a', $url);
        $url = preg_replace('#@|À|Á|Â|Ã|Ä|Å#', 'A', $url);
        $url = preg_replace('#ì|í|î|ï#', 'i', $url);
        $url = preg_replace('#Ì|Í|Î|Ï#', 'I', $url);
        $url = preg_replace('#ð|ò|ó|ô|õ|ö#', 'o', $url);
        $url = preg_replace('#Ò|Ó|Ô|Õ|Ö#', 'O', $url);
        $url = preg_replace('#ù|ú|û|ü#', 'u', $url);
        $url = preg_replace('#Ù|Ú|Û|Ü#', 'U', $url);
        $url = preg_replace('#ý|ÿ#', 'y', $url);
        $url = preg_replace('#Ý#', 'Y', $url);

        return ($url);
    }

      public function getGeoLoc($adresse)
      {
        $adresse =  urlencode(preg_replace('/\r|\n/', ' ', $adresse));

        return Geocoder::getLocation($adresse);
      }

    /**
     * civilité
     * @param  integer $idCivilite
     * @return string  strCivilite
     *
     * Utilisé uniquement dans load-events
     * A modifier pour utiliser la table parametre
    */
    public function civilite($idCivilite)
    {

        $repository = $this->entityManager->getRepository('GestimeCoreBundle:Parametre');
        $civilites = $repository->getParamByType('Civilite')
                                ->getQuery()
                                ->getArrayResult();

        switch ($idCivilite) {
            case 1:
                return 'Mr';
                break;
            case 2:
                return 'Mme';
                break;
            case 3:
                return 'Melle';
                break;
            case 4:
                return 'Dr';
                break;
            default:
                return ' ';
                break;
        }
    }
}
