<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new Ali\DatatableBundle\AliDatatableBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new ADesigns\CalendarBundle\ADesignsCalendarBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),

            new Gestime\AgendaBundle\GestimeAgendaBundle(),
            new Gestime\ApiBundle\GestimeApiBundle(),
            new Gestime\CoreBundle\GestimeCoreBundle(),
            new Gestime\StatsBundle\GestimeStatsBundle(),
            new Gestime\UserBundle\GestimeUserBundle(),
            new Gestime\EventBundle\GestimeEventBundle(),
            new Gestime\MessageBundle\GestimeMessageBundle(),
            new Gestime\TelephonieBundle\GestimeTelephonieBundle(),
            new Gestime\FormBundle\GestimeFormBundle(),
            new Gestime\RapportsBundle\GestimeRapportsBundle(),
            new Gestime\HomeBundle\GestimeHomeBundle(),
            new Gestime\Doc24Bundle\GestimeDoc24Bundle(),
            new Gestime\SynchroV1Bundle\GestimeSynchroV1Bundle(),

            new FOS\UserBundle\FOSUserBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Dunglas\AngularCsrfBundle\DunglasAngularCsrfBundle(),

            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Scheb\TwoFactorBundle\SchebTwoFactorBundle(),

            new Knp\Bundle\GaufretteBundle\KnpGaufretteBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),

            new Genemu\Bundle\FormBundle\GenemuFormBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Snc\RedisBundle\SncRedisBundle(),
            new Misteio\CloudinaryBundle\MisteioCloudinaryBundle(),

            new Ivory\GoogleMapBundle\IvoryGoogleMapBundle(),


        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
