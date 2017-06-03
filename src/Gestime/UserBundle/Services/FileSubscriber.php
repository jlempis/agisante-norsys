<?php

namespace Gestime\UserBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Gestime\CoreBundle\Entity\InfosDoc24;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event as VichEvent;
use Intervention\Image\ImageManager;


class FileSubscriber implements EventSubscriberInterface
{
  protected $container;

  public function __construct(ContainerInterface $container)
  {
    $this->container = $container;
  }

  /**
   * @return array
   */
  public static function getSubscribedEvents()
  {
    return [
      VichEvent\Events::POST_UPLOAD => 'postUpload'
    ];
  }

  private function getAvatarRootDir() {
    return '/var/www/web/avatars/';
  }

  /**
   * @param VichEvent\Event $event
   */
  public function postUpload(VichEvent\Event $event)
  {
    $object = $event->getObject();

    //Verification que l'objet est bien du type InfosDoc24
    $filename = pathinfo($object->getPhotoPath())['filename'];

    if ($object instanceof InfosDoc24) {
      //Traitement post image
      $manager = new ImageManager(array('driver' => 'imagick'));

      //Image page personnelle
      $manager
        ->make($this->getAvatarRootDir().$object->getPhotoPath())
        ->fit(140, 160)
        ->encode('jpg', 50)
        ->save($this->getAvatarRootDir().$filename.'.jpg');
      $object->setPhotoPath($filename);

      //Image list autocomplete
      $manager
        ->make($this->getAvatarRootDir().$object->getPhotoPath().'.jpg')
        ->fit(40, 40)
        ->encode('png')
        ->save($this->getAvatarRootDir().'small/'.$filename.'.png');

      //Cloudinary
       $cloudinary = $this -> container -> get('misteio_cloudinary_wrapper');
       $tt = $cloudinary -> upload($this->getAvatarRootDir().$filename.'.jpg', $filename, array());

    }
  }
}
