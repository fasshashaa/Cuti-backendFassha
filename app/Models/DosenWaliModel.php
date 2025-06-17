<?php namespace App\Models;

use CodeIgniter\Model;

class DosenWaliModel extends Model
{
    protected $table      = 'dosen_wali';
    protected $primaryKey = 'id_dosen';

    protected $useAutoIncrement = true;
    protected $returnType     = 'array';

    // Tidak ada created_at/updated_at di DB
    protected $useTimestamps = false; 

    protected $allowedFields = [
        'nidn',
        'nama_dosen',
        'id_user',
    ];

    protected $validationRules = [
        'nidn'       => 'required|max_length[20]|is_unique[dosen_wali.nidn]',
        'nama_dosen' => 'required|max_length[50]',
        'id_user'    => 'required|integer|is_not_unique[user.id_user]', // Validasi id_user harus ada di tabel user
    ];

    protected $validationMessages = [
        'nidn' => [
            'required'   => 'NIDN harus diisi.',
            'max_length' => 'NIDN maksimal 20 karakter.',
            'is_unique'  => 'NIDN sudah terdaftar.',
        ],
        'nama_dosen' => [
            'required'   => 'Nama dosen harus diisi.',
            'max_length' => 'Nama dosen maksimal 50 karakter.',
        ],
        'id_user' => [
            'required'      => 'ID User harus diisi.',
            'integer'       => 'ID User harus berupa angka.',
            'is_not_unique' => 'ID User tidak ditemukan di tabel user.',
        ],
    ];
}