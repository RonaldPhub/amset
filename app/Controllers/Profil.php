<?php

namespace App\Controllers;

/**
 * La classes Profil représent le contrôleur de l'entité métier profil
 */
class Profil extends BaseController
{
    public $profilsModel;

    /**
    * Le constructeur de la classe Profil contrôleur
    */
    public function __construct()
    {
        $this->profilsModel = model('Profil');
    }

    /**
     * Méthode qui vérifie l'authorization d'un utilisateur sur la classe Profil contrôleur
     * @return Boolean
     */
    private function isAuthorized(): bool
    {
        $user = auth()->user();
        return $user->inGroup('admin');
    }

    /**
     * Méthode qui affiche la liste des profiles dans la vue
     */
    public function liste()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $listeProfils = $this->profilsModel->findall();
        return view(
            'profils_liste',
            [
                'listeProfils' => $listeProfils
            ]
        );
    }

   /**
    * Méthode qui dirige vers la vue d'ajout profil
    */
    public function ajout()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        return view('profils_ajoute');
    }

    /**
     * Méthdoe qui va créer un nouvelle profil
     */
    public function create()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $profilsData = $this->request->getpost();
        $this->profilsModel->save($profilsData);

        return redirect('profils_liste');
    }
    
    /**
     * Méthode qui vers la vue modification d'un profil 
     */
    public function modif($profil)
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $modifProfils = $this->profilsModel->find($profil);

        return view(
            'profils_modifier',
            [
                'afficheProfils' => $modifProfils
            ]
        );
    }

    /**
     * Méthode qui modifier un profil
     */
    public function update()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $profilsData = $this->request->getpost();
        $this->profilsModel->save($profilsData);

        return redirect('profils_liste');
    }
    
    /**
     * Méthode qui supprime un profil
     */
    public function delete()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }
        
        $profilsData = $this->request->getpost();
        $this->profilsModel->delete($profilsData['ID_PROFIL']);

        return redirect('profils_liste');
    }
}
