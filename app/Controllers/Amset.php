<?php

namespace App\Controllers;

/**
 * La calsses Amset représent le root de l'application
 */
class Amset extends BaseController
{

    /**
     * Le function main dirige vers la page d'accueil
     * @return String
     */
    public function main(): string
    {
        return view('accueil');
    }
}
