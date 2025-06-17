<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization');

class User extends BaseController
{
    protected $model;
    use ResponseTrait;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index()
    {
        $data = $this->model->findAll();
        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $data = $this->model->where("id_user", $id)->findAll();
        if ($data) {
            return $this->respond($data, 200);
        } else {
            return $this->failNotFound("Data tidak ditemukan");
        }
    }

    public function showName($nama = null)
    {
        try {
            if (empty($nama)) {
                return $this->fail('Nama user harus diisi', 400);
            }

            $data = $this->model->where('username', $nama)->findAll();

            if (!empty($data)) {
                return $this->respond([
                    'status' => 200,
                    'message' => 'Data user ditemukan',
                    'data' => $data
                ], 200);
            } else {
                return $this->failNotFound('Data user dengan nama ' . $nama . ' tidak ditemukan');
            }
        } catch (\Exception $e) {
            return $this->fail('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    public function create()
    {
        // === PERBAIKAN DI SINI ===
        // Menggunakan getJSON(true) untuk mem-parsing body JSON ke array asosiatif
        $data = $this->request->getJSON(true); 

        // Tambahkan logging untuk debugging lebih lanjut jika diperlukan
        log_message('debug', 'User Controller create(): Data diterima: ' . json_encode($data));

        // Pastikan $data bukan null atau array kosong setelah JSON parsing
        if (empty($data)) {
            log_message('error', 'User Controller create(): Tidak ada data JSON yang diterima atau JSON tidak valid.');
            return $this->fail('Tidak ada data yang dikirim atau format JSON tidak valid.', 400);
        }
        
        if (!$this->model->save($data)) {
            $errors = $this->model->errors();
            log_message('error', 'User Controller create(): Gagal menyimpan user. Errors: ' . json_encode($errors));
            return $this->fail($errors, 400);
        }
        
        log_message('debug', 'User Controller create(): User berhasil disimpan.');
        $response = [
            'status' => 200,
            'error' => null,
            'message' => [
                'success' => 'Berhasil Menambah Data',
            ]
        ];
        return $this->respond($response, 200);
    }

    public function update($id = null)
    {
        // Fungsi update() Anda sudah menggunakan getRawInput() yang juga bisa bekerja
        // Tapi akan lebih konsisten dan jelas jika menggunakan getJSON(true) jika inputnya JSON
        $data = $this->request->getJSON(true); // Lebih baik ini daripada getRawInput() untuk JSON
        
        // Pastikan $data bukan null atau array kosong setelah JSON parsing
        if (empty($data)) {
            return $this->fail('Tidak ada data yang dikirim atau format JSON tidak valid.', 400);
        }

        // Penting: Pastikan $id (dari URI) digunakan sebagai primary key untuk update
        // dan bukan ditimpa dari $data jika $data['id_user'] ada di body request.
        // Method update($id, $data) lebih eksplisit.
        if (!$this->model->update($id, $data)) {
            return $this->fail($this->model->errors());
        }

        $response = [
            'status' => 200,
            'error' => null,
            'message' => [
                'success' => 'Berhasil Mengubah Data',
            ]
        ];
        return $this->respond($response, 200);
    }

    public function delete($id = null)
    {
        $data = $this->model->where('id_user', $id)->findAll();
        if ($data) {
            $this->model->delete($id);
            $response = [
                'status' => 200,
                'error' => null,
                'message' => [
                    'success' => 'Berhasil Menghapus Data',
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            return $this->failNotFound("Data tidak ditemukan");
        }
    }
}