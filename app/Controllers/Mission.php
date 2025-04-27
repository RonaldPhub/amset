<?php

namespace App\Controllers;

/**
 * La classes Mission représent le contrôleur de l'entité métier mission 
 */
class Mission extends BaseController
{
    private $missionModel;
    private $clientModel;
    private $profilModel;
    private $salarieModel;

   /**
    * Le constructeur de la classe Mission contrôleur
    */
    public function __construct()
    {
        $this->missionModel = model('Mission');
        $this->clientModel = model('Client');
        $this->profilModel = model('Profil');
        $this->salarieModel = model('Salarie');
    }

    /**
     * Méthode qui vérifie l'authorization d'un utilisateur sur la classe Mission contrôleur
     * @return Boolean
     */
    private function isAuthorized(): bool
    {
        $user = auth()->user();
        return $user->inGroup('admin') || $user->inGroup('com');
    }


    /**
     * Méthode qui affiche la liste tous les mssion dans la vue
     */
    public function liste()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }
        //Contient la liste de tout les mission
        $listeMission = $this->missionModel->findAll();

        //Contient la liste des missions avec les clients et profils associés
        $clientMissionProfils = $this->missionModel->getClientMissionProfil();

        //Contient la liste des missions avec leur clients associés 
        $missionClients = $this->missionModel->getMissionClient(); 

        //Contient la liste des missions avec leur salariés associés
        $listeJoinMissionSalarie = $this->missionModel->getJoinMissionSalarie();
        
        return view('mission_liste', [
            'listeMission' => $listeMission,
            'missionClients' => $missionClients,
            'clientMissionProfils' => $clientMissionProfils,
            'listeJoinMissionSalaries' => $listeJoinMissionSalarie
        ]);
    }

    /**
     * Méthode qui dirige vers la vue d'ajout mission
     */
    public function ajout()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $listeClient = $this->clientModel->findAll();
        $listeProfil = $this->profilModel->findAll();

        return view('mission_ajoute', [
            'listeClient' => $listeClient,
            'listeProfil' => $listeProfil
        ]);
    }

    /**
     * Méthode qui créer un nouvelle mission
     */
    public function create()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $data = $this->request->getPost();
        $this->missionModel->save($data);

        // récupérer l'id de mission généré lors de la précédente insertion
        $nouvelMissionId = $this->missionModel->getInsertID();

        $profils = $this->request->getPost('profils[]');

        //Un boucle qui parcour tout les profiles potentiellement créer  
        foreach ($profils as $idProfil) {
            //Contient le nombre de profil
            $nbre = $this->request->getPost($idProfil);
            //L'insertion vers la base
            $this->missionModel->addProfil($nouvelMissionId, $idProfil, $nbre);
        }

        return redirect('mission_liste');
    }

    /**
     * Méthode qui affiche le mission à modifier
     */
    public function modif($missionId)
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        //Récupère le mission par rapport à son id
        $mission = $this->missionModel->find($missionId);
        //Table client
        $listeClient = $this->clientModel->findAll();
        //Contient la liste des profils
        $listeProfil = $this->profilModel->findAll();
        //Contient la jointure sur client, mission, profil
        $missionJoins = $this->missionModel->getJoinMissionInfo($missionId);
        //Contient les profils absent dans la misison
        $profilNotInMissions = $this->profilModel->getProfilNotInMission($missionId);

        return view('mission_modifier', [
            'mission' => $mission,
            'missionJoins' => $missionJoins,
            'listeClient' => $listeClient,
            'listeProfil' => $listeProfil,
            'profilNotInMissions' => $profilNotInMissions
        ]);
    }

    /**
     * Méthode qui mettre à jour les données de mission
     */
    public function update()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        //Contient les données à modifier
        $data = $this->request->getPost();
        if (isset($data)) {

            $data = $this->request->getPost();
            $this->missionModel->save($data);

            $missionId = $this->request->getPost('ID_MISSION');
        
            $this->missionModel->deleteSalarieMission($missionId);


            return redirect('mission_liste');
        }
    }

    /**
     * Méthode qui gère la modification d'ajout du profil dans un mission
     */
    public function updateAddProfil()
    {

        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $missionId = $this->request->getPost('ID_MISSION');
        $profilId = $this->request->getPost('ID_PROFIL');

        $nbre = $this->request->getPost('NOMBRE_PROFIL');

        $this->missionModel->addProfil($missionId, $profilId, $nbre);
        return redirect()->to('modif-mission-' . $missionId);
    }

    /**
     * Méthode qui gère la modification de suppression du profil dans un mission
     */
    public function updateDeleteProfil()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $missionId = $this->request->getPost('ID_MISSION');
        $profilId = $this->request->getPost('ID_PROFIL');
        $this->missionModel->deleteProfil($missionId, $profilId);

        return redirect()->to('modif-mission-' . $missionId);
    }

    /**
     * Méthode qui supprime un mission
     */
    public function delete()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }


        $missionId = $this->request->getPost('ID_MISSION');
        
        $this->missionModel->deleteProfilMission($missionId);
        $this->missionModel->deleteSalarieMission($missionId);
        $this->missionModel->delete($missionId);

        return redirect('mission_liste');
    }

    /**
     * Méthode qui dirige vers la page d'affectation des salariés au misison
     */
    public function PageAttributionDesSalarie($missionId)
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $mission = $this->missionModel->find($missionId);

        //Contient tout les mission avec leur profils associés
        $profilsMission = $this->missionModel->getMissionProfil($missionId);

        $listeSalarie = $this->salarieModel->findAll();
        $profilsSalarie = [];
        foreach ($listeSalarie as $salarie) {
            $profilsSalarie[] = $this->salarieModel->getProfil($salarie['ID_SALARIE']);
        }

        return view('mission_affecter_salarier', [
            'mission' => $mission,
            'profilsMission' => $profilsMission,
            'listeSalarie' => $listeSalarie,
            'profilsSalarie' => $profilsSalarie,
        ]);
    }

    /**
     * Méthode qui valide l'affectation des salariés au mission
     */
    public function affect()
    {

        $data = $this->request->getPost();
        $nbr = $this->request->getPost('nbr');

        $missionId = $this->request->getPost('ID_MISSION_0');

        $this->missionModel->deleteSalarieMission($missionId);

        //Un boucle qui va parcourir le nombre de fois de profil
        for ($i = 0; ($i < $nbr); $i++) {
            $salarieId = $this->request->getPost('ID_SALARIE_' . $i);
            $missionId = $this->request->getPost('ID_MISSION_' . $i);
            $salarieId2 = $this->request->getPost('ID_SALARIE_' . ($i + 1));
           
            $nombreMission = $this->missionModel->getNombreSalarieMission($missionId);
           
            if ($salarieId != '' || $salarieId != null) {
                if ($salarieId == $salarieId2) {
                    echo '<h1>Selection des salariés non valide !<h1>';
                    echo '<a href=' . url_to("mission_attribution", $missionId) . '><button>Retour</button>';

                    $this->missionModel->deleteSalarieMission($missionId);
                    die();
                   
                } else {

                    $this->missionModel->addSalarieMission($salarieId, $missionId);
                }
            } else {
                echo '<h1>Selection des salariés vide !<h1>';
                echo '<a href=' . url_to("mission_attribution", $missionId) . '><button>Retour</button>';
                $this->missionModel->deleteSalarieMission($missionId);
                die();
            }
        };
        return redirect()->to(url_to("mission_liste", $missionId));
    }

}
