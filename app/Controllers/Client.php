<?php

namespace App\Controllers;

/**
 * La classes Client représent le contrôleur de l'entité métier client 
 */
class Client extends BaseController
{
    private $clientModel;
    private $missionModel;

    /**
     * Le constructeur de la classe contrôleur Client
     */
    public function __construct()
    {
        $this->clientModel = model('Client');
        $this->missionModel = model('Mission');

    }

    /**
     * Méthode qui vérifie l'authorization d'un utilisateur sur la classe Client
     * @return Boolean
     */
    private function isAuthorized(): bool
    {
        $user = auth()->user();
        return $user->inGroup('admin') || $user->inGroup('com');

    }

    
    /**
     * Méthode qui affiche la liste des clients dans la vue
     */
    public function liste()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $clients = $this->clientModel->findAll();
        $missions = $this->missionModel->findAll();
        return view(
            'clients_liste',
            [
                'clients' => $clients,
                'missions' => $missions
            ]
        );

    }

    /**
     * Méthode qui redirige vers la vue d'ajout client
     */
    public function ajout()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        return view('clients_ajout');
    }

    /**
     * Méthode qui créer un nouvelle client
     */
    public function create()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $data = $this->request->getPost();

        $this->clientModel->save($data);

        return redirect('client_liste');
    }

    /**
     * Méthode qui affiche le client à modifier
     * @param interger $idClient
     */
    public function modif($idClient)
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $client = $this->clientModel->find($idClient);

        return view('clients_modifier', ['client' => $client]);
    }

    /**
     * Méthode qui modifie un client
     */
    public function update()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $data = $this->request->getPost();

        $this->clientModel->save($data);

        return redirect('client_liste');
    }
    
    /**
     * Méthode qui sert à supprimer un client
     */
    public function delete()
    {
        if (!$this->isAuthorized()) {
            return redirect('accueil');
        }

        $clientData = $this->request->getPost(['ID_CLIENT']);

        $missionData = $this->clientModel->getIdMission($clientData);

        $this->clientModel->deleteMissionProfils($missionData);

        $this->clientModel->deleteMissionSalarie($missionData);

        $this->clientModel->deleteMissionClient($clientData['ID_CLIENT']);

        $this->clientModel->delete($clientData);

        return redirect('client_liste');
    }
}
