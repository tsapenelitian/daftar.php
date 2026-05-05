<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class PendaftaranSidang extends ActiveRecord
{
    // Tambahkan variabel virtual untuk menampung file ke-9
    public $fileNilaiInstance;
    public $fileSertifikatInstance;
    public $fileSeminarInstance;
    public $fileBimbinganInstance;
    public $fileBukuBimbinganInstance;
    public $fileTurnitinInstance;
    public $fileIjazahInstance;
    public $fileBebasPerpusInstance;
    public $fileBebasKeuanganInstance; // Properti baru untuk berkas keuangan

    public static function tableName()
    {
        return 'pendaftaran_sidang';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['mahasiswa_id', 'judul_skripsi'], 'required'],
            [['mahasiswa_id', 'status_baak', 'status_keuangan', 'status_perpustakaan', 'status_kaprodi', 'created_at', 'updated_at'], 'integer'],
            [['catatan_baak', 'catatan_keuangan', 'catatan_perpustakaan', 'catatan_kaprodi'], 'string'],
            
            // Tambahkan file_bebas_keuangan ke daftar kolom string
            [['judul_skripsi', 'file_nilai', 'file_sertifikat', 'file_seminar', 'file_bimbingan', 'file_buku_bimbingan', 'file_turnitin', 'file_ijazah', 'file_bebas_perpus', 'file_bebas_keuangan', 'status_akhir'], 'string', 'max' => 255],
            
            // VALIDASI SAAT DAFTAR BARU (Wajib Upload 9 Berkas)
            [[
                'fileNilaiInstance', 'fileSertifikatInstance', 'fileSeminarInstance', 
                'fileBimbinganInstance', 'fileBukuBimbinganInstance', 
                'fileTurnitinInstance', 'fileIjazahInstance', 'fileBebasPerpusInstance',
                'fileBebasKeuanganInstance' // Wajib saat mendaftar
            ], 'file', 
                'skipOnEmpty' => false, 
                'extensions' => 'pdf', 
                'maxSize' => 1024 * 1024 * 5, 
                'on' => 'default'
            ],
            
            // VALIDASI SAAT STAFF MEMPROSES (Tidak Wajib Upload Ulang)
            [[
                'fileNilaiInstance', 'fileSertifikatInstance', 'fileSeminarInstance', 
                'fileBimbinganInstance', 'fileBukuBimbinganInstance', 
                'fileTurnitinInstance', 'fileIjazahInstance', 'fileBebasPerpusInstance',
                'fileBebasKeuanganInstance' 
            ], 'file', 
                'skipOnEmpty' => true, 
                'extensions' => 'pdf', 
                'on' => 'update'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mahasiswa_id' => 'Mahasiswa',
            'judul_skripsi' => 'Judul Skripsi',
            'status_baak' => 'Status BAAK',
            'status_keuangan' => 'Status Keuangan',
            'status_perpustakaan' => 'Status Perpustakaan',
            'status_kaprodi' => 'Status Kaprodi',
            'status_akhir' => 'Status Akhir',
            'fileBebasKeuanganInstance' => 'Surat Bebas Keuangan',
        ];
    }

    public function getMahasiswa()
    {
        return $this->hasOne(Mahasiswa::class, ['user_id' => 'mahasiswa_id']);
    }



    public function getKaprodi()
{
    // Mencari staff dengan bagian 'kaprodi' yang prodinya sama dengan mahasiswa
    return Staff::find()
        ->where(['bagian' => 'kaprodi'])
        ->andWhere(['program_studi' => $this->mahasiswa->program_studi])
        ->one();
}
    /**
     * Logika otomatis sebelum data disimpan ke database.
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // Mengecek apakah SEMUA validator sudah menyetujui (nilai 1)
            if ($this->status_baak == 1 && $this->status_keuangan == 1 && $this->status_perpustakaan == 1 && $this->status_kaprodi == 1) {
                $this->status_akhir = 'Disetujui untuk Sidang';
            } else {
                $this->status_akhir = 'Proses Validasi';
            }
            return true;
        }
        return false;
    }
}
