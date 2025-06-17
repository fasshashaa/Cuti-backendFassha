<?php namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table      = 'admin';
    protected $primaryKey = 'id_admin';
    protected $useAutoIncrement = true; // Sesuai dengan gambar DB Anda

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'id_user',      // Ini sekarang VARCHAR
        'username',     // Ini VARCHAR
    ];

    // Dates (sesuaikan jika ada created_at/updated_at di tabel admin Anda)
    protected $useTimestamps = false; 

    // *** PERHATIAN: VALIDASI DISESUAIKAN UNTUK id_user TIPE VARCHAR ***
    protected $validationRules    = [
        'id_user'  => 'required|max_length[20]', // Hapus 'integer' karena ini VARCHAR. Tambah max_length sesuai DB.
        'username' => 'required|min_length[3]|max_length[255]',
        // Jika Anda ingin memastikan id_user ada di tabel 'user', Anda tidak bisa pakai
        // 'is_not_unique[user.id_user]' langsung jika id_user di tabel 'user' adalah INT
        // dan di tabel 'admin' adalah VARCHAR. Anda harus melakukan pengecekan ini
        // secara manual di controller jika diperlukan.
    ];
    protected $validationMessages = [
        'id_user' => [
            'required'   => 'ID User wajib diisi.',
            'max_length' => 'ID User maksimal 20 karakter.',
        ],
        'username' => [
            'required'   => 'Nama pengguna wajib diisi.',
            'min_length' => 'Nama pengguna minimal 3 karakter.',
            'max_length' => 'Nama pengguna maksimal 255 karakter.',
        ],
    ];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;
}