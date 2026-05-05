
text/x-generic daftar.php ( PHP script, ASCII text, with CRLF line terminators )
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PendaftaranSidang */

$this->title = 'Form Pendaftaran Sidang';
$this->params['breadcrumbs'][] = $this->title;

// --- REGISTRASI SEMUA CSS (DEEP BLUE 3D & ANIMASI) ---
$this->registerCss("
    body { background-color: #f0f4f8; }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(0, 71, 171, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(0, 71, 171, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 71, 171, 0); }
    }

    .sidang-daftar-container {
        animation: fadeInUp 0.8s ease-out;
    }

    /* Identitas Card Style */
    .identity-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 35, 102, 0.15);
        padding: 25px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        border-left: 8px solid #ffcc00;
    }
    
    .profile-img-3d {
        width: 110px;
        height: 110px;
        border-radius: 15px;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        margin-right: 25px;
    }

    .profile-img-3d:hover { transform: scale(1.1) rotate(3deg); }

    .nim-badge {
        display: inline-block;
        background: #0047ab;
        color: white;
        padding: 2px 12px;
        border-radius: 10px;
        font-size: 13px;
        margin: 5px 0;
    }

    /* Form Card Style */
    .main-card {
        background: #ffffff;
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 35, 102, 0.1);
        overflow: hidden;
    }

    .deep-blue-header {
        background: linear-gradient(135deg, #002366 0%, #0047ab 100%);
        color: white;
        padding: 35px 20px;
        margin: -15px -15px 30px -15px;
        border-bottom: 5px solid #ffcc00;
        text-align: center;
    }

    .custom-well {
        background: #fdfdfd;
        border: 1px solid #eef2f7;
        border-radius: 15px;
        padding: 18px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        margin-bottom: 15px;
    }

    .custom-well:hover {
        transform: scale(1.02);
        border-color: #0047ab;
        box-shadow: 0 10px 25px rgba(0, 71, 171, 0.12);
        animation: pulse 1.5s infinite;
    }

    .section-title-alt {
        color: #002366;
        font-weight: bold;
        padding: 8px 0;
        margin: 20px 0 15px 0;
        border-bottom: 2px solid #ffcc00;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-size: 13px;
    }

    .btn-3d-anim {
        background: linear-gradient(to bottom, #28a745 0%, #218838 100%);
        border: none;
        border-bottom: 5px solid #19692c;
        border-radius: 12px;
        font-weight: bold;
        padding: 18px;
        color: white;
        transition: all 0.2s;
    }

    .btn-3d-anim:active { transform: translateY(3px); border-bottom-width: 1px; }
    
    .form-control { border-radius: 10px; border: 2px solid #eef2f7; }
");
?>

<div class="sidang-daftar-container">

    <h1 style="color: #002366; font-weight: bold; margin-bottom: 20px;"><?= Html::encode($this->title) ?></h1>

    <!-- START: IDENTITAS MAHASISWA -->
    <div class="identity-card">
        <?php 
            $foto = ($model->mahasiswa && $model->mahasiswa->foto) 
                    ? Yii::$app->request->baseUrl . '/uploads/profil/' . $model->mahasiswa->foto 
                    : Yii::$app->request->baseUrl . '/img/no-photo.png';
        ?>
        <?= Html::img($foto, ['class' => 'profile-img-3d']) ?>
        
        <div class="info-content">
            <small class="text-muted">Mahasiswa Terdaftar:</small>
            <h3 style="margin: 0; color: #002366; font-weight: bold;"><?= $model->mahasiswa ? Html::encode($model->mahasiswa->nama_lengkap) : 'Guest' ?></h3>
            <div class="nim-badge"><?= $model->mahasiswa ? Html::encode($model->mahasiswa->nim) : '-' ?></div>
            <div style="color: #555;"><span class="glyphicon glyphicon-education"></span> <?= $model->mahasiswa ? Html::encode($model->mahasiswa->program_studi) : '-' ?></div>
        </div>
    </div>

    <!-- PANDUAN SINGKAT -->
    <div class="alert alert-info" style="border-radius: 15px; border-left: 8px solid #0047ab;">
        <strong><span class="glyphicon glyphicon-info-sign"></span> Panduan:</strong> Dokumen wajib <strong>PDF</strong> (Maks. 5MB).
    </div>

    <!-- START: FORMULIR UTAMA -->
    <div class="panel main-card">
        <div class="panel-body">
            <div class="deep-blue-header">
                <h3 style="margin: 0; font-weight: bold; letter-spacing: 1px;">PORTAL UNGGAH BERKAS</h3>
                <p style="margin: 5px 0 0 0; opacity: 0.8;">Pastikan semua data sesuai dengan dokumen asli</p>
            </div>

            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <div style="margin-bottom: 25px;">
                <?= $form->field($model, 'judul_skripsi')->textarea([
                    'rows' => 3, 
                    'placeholder' => 'Masukkan Judul Lengkap Tugas Akhir Anda...',
                    'style' => 'font-weight: bold;'
                ])->label('<span class="glyphicon glyphicon-edit"></span> Judul Tugas Akhir') ?>
            </div>

            <div class="section-title-alt">Dokumen Akademik Utama</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-well"><?= $form->field($model, 'fileNilaiInstance')->fileInput(['accept' => 'application/pdf'])->label('Transkrip Nilai (Smt 1-8)') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="custom-well"><?= $form->field($model, 'fileSertifikatInstance')->fileInput(['accept' => 'application/pdf'])->label('Sertifikat (Min. 10)') ?></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-well"><?= $form->field($model, 'fileSeminarInstance')->fileInput(['accept' => 'application/pdf'])->label('Kartu Bukti Seminar') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="custom-well"><?= $form->field($model, 'fileBimbinganInstance')->fileInput(['accept' => 'application/pdf'])->label('Form Bimbingan Skripsi') ?></div>
                </div>
            </div>

            <div class="section-title-alt">Validasi & Perpustakaan</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-well"><?= $form->field($model, 'fileBukuBimbinganInstance')->fileInput(['accept' => 'application/pdf'])->label('Buku Bimbingan Akademik') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="custom-well"><?= $form->field($model, 'fileBebasPerpusInstance')->fileInput(['accept' => 'application/pdf'])->label('Surat Bebas Perpustakaan') ?></div>
                </div>
            </div>

            <div class="section-title-alt">Integritas & Kelengkapan</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="custom-well"><?= $form->field($model, 'fileTurnitinInstance')->fileInput(['accept' => 'application/pdf'])->label('Hasil Cek Turnitin') ?></div>
                </div>
                <div class="col-md-6">
                    <div class="custom-well">
                        <?= $form->field($model, 'fileIjazahInstance')->fileInput(['accept' => 'application/pdf'])->label('Form Pengajuan Tugas Akhir')
                            ->hint(Html::a('<span class="glyphicon glyphicon-file"></span> Download Template', 
                                'https://docs.google.com/document/d/1c3EstXwDmWpLy4VYwMk8iqSdIIRx2-S2/edit?usp=drive_link', 
                                ['target' => '_blank', 'class' => 'label label-primary', 'style' => 'padding: 5px 8px; margin-top: 8px; display: inline-block;']
                            )) ?>
                    </div>
                </div>
            </div>

            <div class="section-title-alt">Administrasi Keuangan</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="custom-well" style="border-left: 6px solid #28a745; background: #fafffa;">
                        <?= $form->field($model, 'fileBebasKeuanganInstance')->fileInput(['accept' => 'application/pdf'])->label('Bukti Pelunasan Administrasi (Bebas Keuangan)') ?>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 40px; padding-bottom: 10px;">
                <?= Html::submitButton('<span class="glyphicon glyphicon-send"></span> KIRIM PENDAFTARAN & UNGGAH BERKAS', [
                    'class' => 'btn btn-success btn-lg btn-block btn-3d-anim'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
