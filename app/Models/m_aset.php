<?php

namespace App\Models;

use CodeIgniter\Model;

class m_aset extends Model
{
    protected $table = 'laporan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'laporan', 
        'tingkat_kerusakan', 
        'progress_perbaikan', 
        'data_koordinat'
    ];

public function getAllLaporan()
{
    $laporan = $this->db->table('laporan')->get()->getResultArray();

    foreach ($laporan as &$row) {
        $row['fotos'] = $this->db->table('foto_laporan')
            ->where('laporan_id', $row['id'])
            ->get()
            ->getResultArray();
    }

    return $laporan;
}

    // Ambil satu laporan berdasarkan ID
    public function getLaporanById($id)
    {
        return $this->where('id', $id)->first();
    }

    // Tambah laporan baru
    public function addLaporan($data)
    {
        return $this->insert($data);
    }

    // ===============================
    // FOTO Laporan (tabel foto_laporan)
    // ===============================
    protected $fotoTable = 'foto_laporan';

    // Ambil semua foto berdasarkan laporan_id
    public function getFotoByLaporanId($laporan_id)
    {
        return $this->db->table($this->fotoTable)
                        ->where('laporan_id', $laporan_id)
                        ->get()
                        ->getResultArray();
    }

    // Tambah foto untuk laporan tertentu
    public function addFoto($data)
    {
        return $this->db->table($this->fotoTable)->insert($data);
    }
}
