<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\UserModel;
use CodeIgniter\API\ResponseTrait;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization');

class Admin extends BaseController
{
    use ResponseTrait;
    protected $adminModel;
    protected $userModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->userModel = new UserModel();
    }

    public function options()
    {
        return $this->respondNoContent();
    }

    public function index()
    {
        $data = $this->adminModel->findAll();
        log_message('debug', 'AdminController index(): Retrieved all admin data.');
        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $data = $this->adminModel->where("id_admin", $id)->first();
        if ($data) {
            log_message('debug', 'AdminController show(): Admin data found for ID: ' . $id);
            return $this->respond($data, 200);
        } else {
            log_message('info', 'AdminController show(): Admin data not found for ID: ' . $id);
            return $this->failNotFound("Data tidak ditemukan");
        }
    }

    public function create()
    {
        $data = $this->request->getJSON(true);
        log_message('debug', 'AdminController create(): Raw JSON received: ' . json_encode($data));
        // dd($data); // <--- UNCOMMENT INI UNTUK MELIHAT DATA DITERIMA

        if (empty($data) || !isset($data['id_user']) || !isset($data['username'])) {
            log_message('error', 'AdminController create(): Payload empty or missing id_user/username. Payload: ' . json_encode($data));
            return $this->fail([
                'message' => 'Data tidak valid atau parameter id_user/username tidak lengkap.'
            ], 400);
        }

        $userCheck = $this->userModel->where('id_user', $data['id_user'])
            ->where('username', $data['username'])
            ->first();

        if (!$userCheck) {
            log_message('error', 'AdminController create(): User check failed. id_user: ' . $data['id_user'] . ', username: ' . $data['username']);
            return $this->fail([
                'message' => 'ID User dan username tidak sesuai dengan data di tabel user.'
            ], 400);
        }

        log_message('debug', 'AdminController create(): Data to be saved by AdminModel: ' . json_encode($data));
        // dd($data); // <--- UNCOMMENT INI UNTUK MELIHAT DATA SEBELUM SAVE

        if (!$this->adminModel->save($data)) {
            $errors = $this->adminModel->errors();
            log_message('error', 'AdminController create(): Failed to save admin. Model errors: ' . json_encode($errors));
            log_message('error', 'AdminController create(): Data that caused save failure: ' . json_encode($data));
            // return $this->fail($errors, 400); // Jangan langsung return fail dulu, biarkan die() bekerja
            dd($errors); // <--- UNCOMMENT INI UNTUK MELIHAT ERROR MODEL SECARA LANGSUNG
        }

        log_message('info', 'AdminController create(): Admin successfully added. Saved data: ' . json_encode($data));
        // die("Data saved successfully in application logic, checking database now."); // <--- UNCOMMENT INI UNTUK MENGETAHUI MODEL SAVE BERHASIL DI APLIKASI

        $response = [
            'status' => 200,
            'error' => null,
            'message' => [
                'success' => 'Berhasil Menambah Data Admin',
            ]
        ];
        return $this->respond($response, 200);
    }

    // ... (fungsi update dan delete) ...


    // Mengubah data admin berdasarkan ID
    public function update($id = null)
    {
        $input = $this->request->getJSON(true);
        log_message('debug', 'AdminController update(): Raw JSON received for update (ID: ' . $id . '): ' . json_encode($input));

        if (!is_array($input) || empty($input)) {
            log_message('error', 'AdminController update(): Invalid or empty JSON payload for update.');
            return $this->fail('Tidak ada data yang dikirim atau format JSON tidak valid.', 400);
        }

        // Cek apakah data admin dengan ID tersebut ada
        $ifExist = $this->adminModel->where('id_admin', $id)->first();
        if (!$ifExist) {
            log_message('info', 'AdminController update(): Admin data not found for update ID: ' . $id);
            return $this->failNotFound("Data tidak ditemukan");
        }

        // Validasi: cek apakah id_user dan username sesuai di tabel user (jika disertakan di payload)
        if (isset($input['id_user']) && isset($input['username'])) {
            $userCheck = $this->userModel->where('id_user', $input['id_user'])
                ->where('username', $input['username'])
                ->first();

            if (!$userCheck) {
                log_message('error', 'AdminController update(): User check failed for update. id_user: ' . $input['id_user'] . ', username: ' . $input['username']);
                return $this->fail([
                    'message' => 'ID User dan username tidak sesuai dengan data di tabel user'
                ], 400);
            }
        }
        // Jika id_user atau username tidak ada di payload update, validasi ini akan di-skip.

        // PENTING: Untuk update, gunakan metode update($id, $data) dari model
        if (!$this->adminModel->update($id, $input)) { // Gunakan $input langsung
            $errors = $this->adminModel->errors();
            log_message('error', 'AdminController update(): Failed to update admin. Model errors: ' . json_encode($errors));
            log_message('error', 'AdminController update(): Data that caused update failure: ' . json_encode($input));
            return $this->fail($errors, 400);
        }

        log_message('info', 'AdminController update(): Admin successfully updated for ID: ' . $id . ' with data: ' . json_encode($input));
        $response = [
            'status' => 200,
            'error' => null,
            'message' => [
                'success' => 'Berhasil Mengubah Data Admin',
            ]
        ];
        return $this->respond($response, 200);
    }

    // Menghapus data admin berdasarkan ID
    public function delete($id = null)
    {
        $data = $this->adminModel->where('id_admin', $id)->first(); // Gunakan first()
        if ($data) {
            $this->adminModel->delete($id);
            log_message('info', 'AdminController delete(): Admin successfully deleted with ID: ' . $id);
            $response = [
                'status' => 200,
                'error' => null,
                'message' => [
                    'success' => 'Berhasil Menghapus Data Admin',
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            log_message('info', 'AdminController delete(): Admin data not found for deletion ID: ' . $id);
            return $this->failNotFound("Data tidak ditemukan");
        }
    }
}