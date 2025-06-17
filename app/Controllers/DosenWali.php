<?php namespace App\Controllers;

use App\Models\DosenWaliModel;
use App\Models\UserModel; // Diperlukan untuk validasi id_user
use CodeIgniter\API\ResponseTrait;

class DosenWali extends BaseController
{
    use ResponseTrait;
    protected $dosenWaliModel;
    protected $userModel;

    public function __construct()
    {
        $this->dosenWaliModel = new DosenWaliModel();
        $this->userModel = new UserModel();
    }

    // Untuk handle CORS preflight requests
    public function options()
    {
        return $this->respondNoContent();
    }

    // GET all Dosen Wali
    public function index()
    {
        $data = $this->dosenWaliModel->findAll();
        return $this->respond($data, 200);
    }

    // GET Dosen Wali by ID
    public function show($id = null)
    {
        $data = $this->dosenWaliModel->find($id);
        if ($data) {
            return $this->respond($data, 200);
        } else {
            return $this->failNotFound("Data dosen wali tidak ditemukan.");
        }
    }

    // POST new Dosen Wali
    public function create()
    {
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->fail(['message' => 'Data tidak boleh kosong.'], 400);
        }

        // Pastikan id_user ada di tabel user sebelum disimpan
        if (isset($data['id_user'])) {
            $userExists = $this->userModel->find($data['id_user']);
            if (!$userExists) {
                return $this->fail(['message' => 'ID User tidak ditemukan di tabel user.'], 400);
            }
        }

        if (!$this->dosenWaliModel->validate($data)) {
            return $this->fail($this->dosenWaliModel->errors(), 400);
        }

        if ($this->dosenWaliModel->insert($data)) {
            $response = [
                'status'  => 201, // Created
                'error'   => null,
                'message' => 'Berhasil Menambah Data Dosen Wali',
                'data'    => $data // Mengembalikan data yang disimpan
            ];
            return $this->respondCreated($response);
        } else {
            return $this->fail(['message' => 'Gagal menambah data dosen wali.'], 500);
        }
    }

    // PUT update Dosen Wali
    public function update($id = null)
    {
        $data = $this->request->getJSON(true);

        if (empty($data)) {
            return $this->fail(['message' => 'Data tidak boleh kosong.'], 400);
        }

        $dosenExists = $this->dosenWaliModel->find($id);
        if (!$dosenExists) {
            return $this->failNotFound("Data dosen wali tidak ditemukan untuk ID: " . $id);
        }

        // Pastikan id_user ada di tabel user jika diupdate
        if (isset($data['id_user'])) {
            $userExists = $this->userModel->find($data['id_user']);
            if (!$userExists) {
                return $this->fail(['message' => 'ID User tidak ditemukan di tabel user.'], 400);
            }
        }
        
        // Hapus primary key dari data update agar tidak di-update
        if (isset($data['id_dosen'])) {
            unset($data['id_dosen']);
        }

        // Pastikan NIDN tidak duplikat jika diupdate
        if (isset($data['nidn']) && $data['nidn'] !== $dosenExists['nidn']) {
            if ($this->dosenWaliModel->where('nidn', $data['nidn'])->countAllResults() > 0) {
                return $this->fail(['nidn' => 'NIDN sudah terdaftar.'], 400);
            }
        }

        if (!$this->dosenWaliModel->validate($data)) {
            return $this->fail($this->dosenWaliModel->errors(), 400);
        }

        if ($this->dosenWaliModel->update($id, $data)) {
            $response = [
                'status'  => 200, // OK
                'error'   => null,
                'message' => 'Berhasil Memperbarui Data Dosen Wali',
                'data'    => $data // Mengembalikan data yang diperbarui
            ];
            return $this->respond($response);
        } else {
            return $this->fail(['message' => 'Gagal memperbarui data dosen wali.'], 500);
        }
    }

    // DELETE Dosen Wali
    public function delete($id = null)
    {
        $dosenExists = $this->dosenWaliModel->find($id);
        if (!$dosenExists) {
            return $this->failNotFound("Data dosen wali tidak ditemukan untuk ID: " . $id);
        }

        if ($this->dosenWaliModel->delete($id)) {
            $response = [
                'status'  => 200, // OK
                'error'   => null,
                'message' => 'Berhasil Menghapus Data Dosen Wali',
            ];
            return $this->respondDeleted($response);
        } else {
            return $this->fail(['message' => 'Gagal menghapus data dosen wali.'], 500);
        }
    }
}