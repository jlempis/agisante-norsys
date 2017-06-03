<?php

/**
 * PatientController class file
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Patient
 * Gestion des patients : Ajout, Modif, Suppression et Liste
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */
class PatientController extends Controller
{
    /**
     * Affichage de la table présentant les abonnés
     * @return form Données à afficher dans le template Twig
     */
    public function indexAction()
    {
        return $this->render('GestimeUserBundle:patients:index.html.twig');
    }
}
