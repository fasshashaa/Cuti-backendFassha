<?php namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
    protected $table      = 'dosen_wali'; // Sesuai dengan nama tabel di database Anda
    protected $primaryKey = 'id_dosen';   // Sesuai dengan primary key tabel dosen_wali

    protected $useAutoIncrement = true;

    protected $returnType     = 'array'; // Bisa 'array' atau 'object'
    protected $useSoftDeletes = false;

    // Kolom-kolom yang ada di tabel dosen_wali dan boleh diisi
    protected $allowedFields = [
        'nama_dosen', // Ada di tabel dosen_wali
        'nidn',       // Ada di tabel dosen_wali
        'id_user',    // Ada di tabel dosen_wali
    ];

    // Dates
    protected $useTimestamps = false; // Tabel dosen_wali tidak memiliki kolom created_at/updated_at
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
}