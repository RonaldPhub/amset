<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * La classes Client représent le modèle de l'entité métier client
 */
class Client extends Model
{
    protected $table            = 'client';
    protected $primaryKey       = 'ID_CLIENT';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'RAISON_SOCIAL',
        'NOM',
        'PRENOM',
        'EMAIL',
        'TELEPHONE',
        'ADRESSE',
        'CODE_POSTAL',
        'VILLE',
        'IMG'

    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Méthode de suppression d'un mission sur la table "profil_mission"
     * @param Integer $missionid
     */
    public function deleteMissionProfils($missionId){
        $db = \Config\Database::Connect();
        $builder = $db->table('profil_mission');
        $builder->where('ID_MISSION',$missionId);
        $builder->delete();

    }

    /**
     * Méthode de suppression d'un misison sur la table "salarie_mission"
     * @param Integer $missionId
     */
    public function deleteMissionSalarie($missionId){
        $db = \Config\Database::connect();
        $builder = $db->table('salarie_mission');
        $builder->where('ID_MISSION', $missionId);
        $builder->delete();
    }

    /**
     * Méthode de supression d'un client sur la table "mission"
     * @param Integer $clientId
     */
    public function deleteMissionClient($clientId){
        $db = \Config\Database::Connect();
        $builder = $db->table('mission');
        $builder->where('ID_CLIENT',$clientId);
        $builder->delete();

    }

    /**
     * Méthode de récupération d'un client par rapport à leur mission respective
     * @param Integer $idClient
     */
    public function getIdMission($idClient){
        return $this->db->table('mission')
            ->select('ID_MISSION')
            ->where('ID_CLIENT', $idClient)
            ->get()
            ->getRowArray();
    }
}



