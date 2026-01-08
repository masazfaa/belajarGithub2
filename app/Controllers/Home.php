<?php

namespace App\Controllers;

use App\Models\m_aset;

class Home extends BaseController
{
    protected $db;
    protected $m_aset;

    public function __construct()
    {
        $this->m_aset = new m_aset();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Laporan Kerusakan',
            'dataLaporan' => $this->m_aset->getAllLaporan(),
            'isi' => 'v_home'
        ];

        return view('template/v_wrapperr', $data);
    }

    public function getLaporanJson()
    {
        // Ambil data terbaru dari model
        $data = $this->m_aset->getAllLaporan();
        
        // Kirim sebagai JSON (Data Mentah)
        return $this->response->setJSON($data);
    }

    public function data()
    {
        $data = [
            'title' => 'Manajemen Laporan',
            'dataLaporan' => $this->m_aset->getAllLaporan(),
            'isi' => 'v_data'
        ];
            helper('color'); // Panggil helper-nya di controller Home

        return view('template/v_wrapper', $data);
    }

    public function save()
    {
        // Ambil data laporan dari form
        $laporanData = [
            'laporan' => $this->request->getPost('laporan'),
            'tingkat_kerusakan' => $this->request->getPost('tingkat_kerusakan'),
            'progress_perbaikan' => $this->request->getPost('progress_perbaikan'),
            'data_koordinat' => $this->request->getPost('data_koordinat'),
        ];

        // Simpan laporan ke database
        $laporanId = $this->m_aset->addLaporan($laporanData);

        // Upload banyak foto
        $files = $this->request->getFiles();
        if (isset($files['foto'])) {
            foreach ($files['foto'] as $foto) {
                if ($foto->isValid() && !$foto->hasMoved()) {
                    $newName = $foto->getRandomName();
                    $foto->move(FCPATH . 'uploads/foto_laporan', $newName);
                    $this->m_aset->addFoto([
                        'laporan_id' => $laporanId,
                        'url_foto' => 'uploads/foto_laporan/' . $newName
                    ]);
                }
            }
        }

        return redirect()->to(base_url('home/data'))->with('success', 'Laporan berhasil ditambahkan.');
    }

    public function delete($id)
    {
        // Ambil semua foto terkait
        $fotos = $this->m_aset->getFotoByLaporanId($id);
        foreach ($fotos as $foto) {
            $filePath = FCPATH . $foto['url_foto'];
            if (is_file($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus laporan (otomatis hapus foto karena ON DELETE CASCADE)
        $this->m_aset->delete($id);

        return redirect()->to(base_url('home/data'))->with('success', 'Laporan berhasil dihapus.');
    }

    public function updateData()
    {
        $id = $this->request->getPost('id');
        $data = [
            'laporan' => $this->request->getPost('laporan'),
            'tingkat_kerusakan' => $this->request->getPost('tingkat_kerusakan'),
            'progress_perbaikan' => $this->request->getPost('progress_perbaikan'),
            'data_koordinat' => $this->request->getPost('data_koordinat'),
        ];
        $this->m_aset->update($id, $data);
    
        // Tambah foto baru
        $files = $this->request->getFiles()['foto'] ?? [];
        foreach ($files as $foto) {
            if ($foto->isValid() && !$foto->hasMoved()) {
                $newName = $foto->getRandomName();
                $foto->move(FCPATH . 'uploads/foto_laporan', $newName);
                $this->m_aset->addFoto([
                    'laporan_id' => $id,
                    'url_foto' => 'uploads/foto_laporan/' . $newName
                ]);
            }
        }
    
        // Ganti foto yang sudah ada
        $replacements = $this->request->getFiles()['replace_foto'] ?? [];
        foreach ($replacements as $fotoId => $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/foto_laporan', $newName);
    
                $fotoLama = $this->db->table('foto_laporan')->getWhere(['id' => $fotoId])->getRowArray();
                if ($fotoLama && file_exists(FCPATH . $fotoLama['url_foto'])) {
                    unlink(FCPATH . $fotoLama['url_foto']);
                }
    
                $this->db->table('foto_laporan')->where('id', $fotoId)->update([
                    'url_foto' => 'uploads/foto_laporan/' . $newName
                ]);
            }
        }
    
        return redirect()->to(base_url('home/data'))->with('success', 'Laporan berhasil diperbarui.');
    }
    
        public function get_fotos($laporan_id)
    {
        $fotos = $this->db->table('foto_laporan')
            ->where('laporan_id', $laporan_id)
            ->get()
            ->getResultArray();
    
        return $this->response->setJSON($fotos);
    }


    public function delete_foto($id)
    {
        $foto = $this->db->table('foto_laporan')->where('id', $id)->get()->getRowArray();
    
        if ($foto) {
            // Hapus file dari storage
            $filePath = FCPATH . $foto['url_foto'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
    
            // Hapus dari database
            $this->db->table('foto_laporan')->where('id', $id)->delete();
        }
    
        return $this->response->setJSON(['status' => 'success']);
    }


    public function logout()
    {
        service('authentication')->logout();
        session()->setFlashdata('message', 'Anda telah berhasil logout.');
        session()->destroy();
        return redirect()->to('/login');
    }
}
