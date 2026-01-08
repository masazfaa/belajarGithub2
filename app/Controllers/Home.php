<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Dashboard Latihan Github',
            // 'dataLaporan' => $this->m_aset->getAllLaporan(),
            'isi' => 'v_home'
        ];

        return view('template/v_wrapperr', $data);
    }
}
