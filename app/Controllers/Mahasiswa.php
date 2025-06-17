<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DosenModel;
use App\Models\KajurModel;
use App\Models\MahasiswaModel;
use CodeIgniter\API\ResponseTrait;

// Menambahkan Header CORS Global
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header(
    "Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization"
);

class Mahasiswa extends BaseController
{
    use ResponseTrait;
    protected $userModel;
    protected $mahasiswaModel;
    protected $dosenModel;
    protected $kajurModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->dosenModel = new DosenModel();
        $this->mahasiswaModel = new MahasiswaModel();
        $this->kajurModel = new KajurModel();
    }

    public function index()
    {
        $data = $this->mahasiswaModel->findAll();
        return $this->respond($data, 200);
    }

    public function show($id = null)
    {
        $data = $this->mahasiswaModel->where("npm", $id)->findAll();
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
                return $this->fail("Nama mahasiswa harus diisi", 400);
            }

            $data = $this->mahasiswaModel
                ->where("nama_mahasiswa", $nama)
                ->findAll();

            if (!empty($data)) {
                return $this->respond(
                    [
                        "status" => 200,
                        "message" => "Data mahasiswa ditemukan",
                        "data" => $data,
                    ],
                    200
                );
            } else {
                return $this->failNotFound(
                    "Data mahasiswa dengan nama " . $nama . " tidak ditemukan"
                );
            }
        } catch (\Exception $e) {
            return $this->fail("Terjadi kesalahan: " . $e->getMessage(), 500);
        }
    }

    public function create()
    {
      $data = $this->request->getJSON(true);

// Tambahkan pengecekan ini:
if (!isset($data["npm"])) {
    return $this->fail(["message" => "NPM harus disertakan dalam request."], 400);
}

$npmCheck = $this->mahasiswaModel->where("npm", $data["npm"])->first();
if ($npmCheck) {
    return $this->fail(["message" => "NPM sudah digunakan"], 400);
}
// ... kode selanjutnya ...

        $userCheck = $this->userModel
            ->where("id_user", $data["id_user"])
            ->where("username", $data["nama_mahasiswa"])
            ->first();
        if (!$userCheck) {
            return $this->fail(
                [
                    "message" =>
                        "ID User tidak sesuai dengan yang ada di tabel user / Nama mahasiswa tidak ada di table user",
                ],
                400
            );
        }

        $kajurCheck = $this->kajurModel
            ->where("id_kajur", $data["id_kajur"])
            ->first();
        if (!$kajurCheck) {
            return $this->fail(
                ["message" => "ID Kajur tidak ditemukan di tabel kajur"],
                400
            );
        }

        if (!$this->mahasiswaModel->save($data)) {
            return $this->fail($this->mahasiswaModel->errors());
        }

        return $this->respond(
            [
                "status" => 200,
                "message" => ["success" => "Berhasil Menambah Data"],
            ],
            200
        );
    }
public function update($id = null)
{
    // Log awal request
    log_message('debug', 'Memulai update untuk NPM: ' . $id);

    // Menggunakan getJSON(true) untuk mem-parsing body JSON ke array asosiatif
    $input = $this->request->getJSON(true);

    log_message('debug', 'Data JSON yang diterima: ' . json_encode($input));

    // Pastikan $input adalah array dan tidak null
    if (!is_array($input)) {
        log_message('error', 'Payload JSON tidak valid: bukan array.');
        return $this->fail('Invalid JSON payload', 400);
    }

    $data = $input;
    $data['npm'] = $id; // Pastikan NPM dari URI digunakan sebagai primary key untuk update

    log_message('debug', 'Data yang akan diupdate (termasuk NPM dari URI): ' . json_encode($data));

    // Cek keberadaan data mahasiswa
    $ifExist = $this->mahasiswaModel->where("npm", $id)->first(); // Gunakan first() karena hanya butuh 1 baris
    if (!$ifExist) {
        log_message('error', 'Data mahasiswa dengan NPM ' . $id . ' tidak ditemukan.');
        return $this->failNotFound("Data tidak ditemukan");
    }
    log_message('debug', 'Data mahasiswa yang ditemukan di DB: ' . json_encode($ifExist));

    // --- Validasi ID User dan Nama Mahasiswa (dengan case-insensitive) ---
    if (!isset($data["id_user"]) || !isset($data["nama_mahasiswa"])) {
        log_message('error', "Parameter 'id_user' atau 'nama_mahasiswa' tidak ditemukan dalam body request.");
        return $this->fail(["message" => "Parameter 'id_user' atau 'nama_mahasiswa' tidak ditemukan dalam body request."], 400);
    }

    $userFromDb = $this->userModel->find($data["id_user"]);
    if (!$userFromDb) {
        log_message('error', 'User dengan ID ' . $data["id_user"] . ' tidak ditemukan di tabel user.');
        return $this->fail('ID User tidak ditemukan di tabel user', 400);
    }

    if (strtolower($userFromDb['username']) !== strtolower($data["nama_mahasiswa"])) {
        log_message('error', 'Ketidakcocokan ID User/username: DB username=' . $userFromDb['username'] . ' vs Request nama_mahasiswa=' . $data["nama_mahasiswa"]);
        return $this->fail(
            [
                "message" =>
                    "ID User dan username tidak sesuai dengan data di tabel user",
            ],
            400
        );
    }
    log_message('debug', 'Validasi ID User dan Nama Mahasiswa BERHASIL.');
    // --- Akhir Validasi ID User dan Nama Mahasiswa ---


    // --- Validasi ID Kajur ---
    if (!isset($data["id_kajur"])) {
        log_message('error', "Parameter 'id_kajur' tidak ditemukan dalam body request.");
        return $this->fail(["message" => "Parameter 'id_kajur' tidak ditemukan dalam body request."], 400);
    }

    $kajurCheck = $this->kajurModel->where("id_kajur", $data["id_kajur"])->first();
    if (!$kajurCheck) {
        log_message('error', 'ID Kajur ' . $data["id_kajur"] . ' tidak ditemukan di tabel kajur.');
        return $this->fail(
            ["message" => "ID Kajur tidak ditemukan di tabel kajur"],
            400
        );
    }
    log_message('debug', 'Validasi ID Kajur BERHASIL.');
    // --- Akhir Validasi ID Kajur ---

    // Cek duplikasi NPM hanya jika NPM di body berbeda dari NPM di URI
    if (isset($input['npm']) && $input['npm'] != $id) {
        $npmCheck = $this->mahasiswaModel
            ->where("npm", $input["npm"])
            ->first();
        if ($npmCheck) {
            log_message('error', 'NPM ' . $input["npm"] . ' yang diubah sudah digunakan oleh mahasiswa lain.');
            return $this->fail(["message" => "NPM yang diubah sudah digunakan oleh mahasiswa lain"], 400);
        }
    }


    // Lakukan update
    $updateResult = $this->mahasiswaModel->update($id, $data);

    if (!$updateResult) {
        $errors = $this->mahasiswaModel->errors();
        log_message('error', 'Gagal mengubah data: ' . json_encode($errors));
        return $this->fail($errors);
    }

    log_message('debug', 'Update data BERHASIL untuk NPM: ' . $id);
    return $this->respond(
        [
            "status" => 200,
            "message" => ["success" => "Berhasil Mengubah Data"],
        ],
        200
    );

} // Penutup fungsi update
    public function delete($npm = null)
    {
        $mahasiswa = $this->mahasiswaModel->where("npm", $npm)->first();

        if ($mahasiswa) {
            $this->mahasiswaModel->where("npm", $npm)->delete();
            return $this->respondDeleted([
                "status" => 200,
                "message" => ["success" => "Data berhasil dihapus."],
            ]);
        } else {
            return $this->failNotFound("Data dengan NPM $npm tidak ditemukan.");
        }
    }
}
