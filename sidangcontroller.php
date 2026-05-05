<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\PendaftaranSidang;
use app\models\Staff; // Ditambahkan untuk mengambil data Kaprodi
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;

class SidangController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['daftar', 'status', 'print'],
                        'roles' => ['mahasiswa'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Pendaftaran Sidang & Upload 9 Berkas
     */
    public function actionDaftar()
    {
        $user_id = Yii::$app->user->id;
        $model = PendaftaranSidang::find()->where(['mahasiswa_id' => $user_id])->one();
        
        if (!$model) {
            $model = new PendaftaranSidang();
            $model->mahasiswa_id = $user_id;
        }

        $uploadPath = Yii::getAlias('@webroot/uploads/sidang/');
        if (!is_dir($uploadPath)) {
            FileHelper::createDirectory($uploadPath);
        }

        if ($model->load(Yii::$app->request->post())) {
            // Menangkap 9 Berkas PDF
            $instances = [
                'fileNilaiInstance' => 'file_nilai',
                'fileSertifikatInstance' => 'file_sertifikat',
                'fileSeminarInstance' => 'file_seminar',
                'fileBimbinganInstance' => 'file_bimbingan',
                'fileBukuBimbinganInstance' => 'file_buku_bimbingan',
                'fileTurnitinInstance' => 'file_turnitin',
                'fileIjazahInstance' => 'file_ijazah',
                'fileBebasPerpusInstance' => 'file_bebas_perpus',
                'fileBebasKeuanganInstance' => 'file_bebas_keuangan',
            ];

            foreach ($instances as $instanceName => $dbColumn) {
                $model->$instanceName = UploadedFile::getInstance($model, $instanceName);
                if ($model->$instanceName) {
                    $fileName = $user_id . '_' . $dbColumn . '_' . time() . '.' . $model->$instanceName->extension;
                    $model->$instanceName->saveAs($uploadPath . $fileName);
                    $model->$dbColumn = 'uploads/sidang/' . $fileName;
                }
            }

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Pendaftaran Berhasil! 9 Berkas telah diunggah.');
                return $this->redirect(['status']);
            }
        }

        return $this->render('daftar', ['model' => $model]);
    }

    public function actionStatus()
    {
        $user_id = Yii::$app->user->id;
        $model = PendaftaranSidang::find()->where(['mahasiswa_id' => $user_id])->one();
        return $this->render('status', ['model' => $model]);
    }

    /**
     * Cetak Kartu dengan Nama Kaprodi Otomatis
     */
    public function actionPrint($id)
    {
        $model = PendaftaranSidang::findOne([
            'id' => $id, 
            'mahasiswa_id' => Yii::$app->user->id
        ]);

        if (!$model || $model->status_akhir != 'Disetujui untuk Sidang') {
            Yii::$app->session->setFlash('error', 'Kartu belum tersedia.');
            return $this->redirect(['status']);
        }

        // OTOMATISASI: Cari data Kaprodi berdasarkan Program Studi mahasiswa
        $kaprodi = Staff::findOne([
            'bagian' => 'kaprodi',
            'program_studi' => $model->mahasiswa->program_studi
        ]);

        // Mengirimkan variabel 'kaprodi' ke View
        return $this->renderPartial('print_kartu', [
            'model' => $model,
            'kaprodi' => $kaprodi,
        ]);
    }
}s
