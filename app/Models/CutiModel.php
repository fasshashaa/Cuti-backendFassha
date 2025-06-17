<?php

namespace App\Models;

use CodeIgniter\Model;

class CutiModel extends Model
{
     protected $useTimestamps = false;
    protected $table = "cuti";
     protected $createdField  = 'tgl_pengajuan'; 
    protected $primaryKey = "id_cuti"; // <--- WAJIB ADA!

    protected $allowedFields = [
        "npm",
        "semester",
        "tgl_pengajuan",
        "dokumen_pendukung",
        "alasan",
        "status",

        // tambahkan semua kolom lain yang boleh diisi
    ];

    protected $useAutoIncrement = true; // <--- Pastikan ini true
    protected $returnType = "object"; // atau 'object' sesuai kebutuhan
}
