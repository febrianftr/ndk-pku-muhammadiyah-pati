<?php
require '../koneksi/koneksi.php';
require '../model/query-base-workload.php';
require '../model/query-base-order.php';
require '../model/query-base-study.php';
require '../model/query-base-patient.php';
require '../model/query-base-dokter-radiology.php';

// untuk download PDF
function expertise($uidInput)
{
    global $conn, $conn_pacsio, $select_patient, $select_study, $select_order, $select_workload, $select_dokter_radiology, $table_patient, $table_study, $table_workload, $table_order, $table_dokter_radiology;

    $uid = $uidInput;

    $row = mysqli_fetch_assoc(mysqli_query(
        $conn_pacsio,
        "SELECT 
		$select_patient,
		$select_study,
		$select_order,
		$select_workload
		FROM $table_patient
		JOIN $table_study
		ON patient.pk = study.patient_fk
		LEFT JOIN $table_order
		ON xray_order.uid = study.study_iuid
		LEFT JOIN $table_workload
		ON study.study_iuid = xray_workload.uid
		WHERE study.study_iuid = '$uid'"
    ));

    $pat_name = substr(removeCharacter(ucwords(strtolower(defaultValue($row['pat_name'])))), 0, 23);
    $pat_sex = $row['pat_sex'];
    $pat_birthdate = $row['pat_birthdate'];
    $age = diffDate($pat_birthdate);
    $study_datetime = defaultValueDateTime($row['study_datetime']);
    $study_desc_one = substr(ucwords(strtolower(defaultValue($row['prosedur']))), 0, 32);
    $study_desc_two = substr(ucwords(strtolower(defaultValue($row['prosedur']))), 32, 32);
    $pat_id = defaultValue($row['pat_id']);
    $no_foto = defaultValue($row['no_foto']);
    $address = ucwords(strtolower(defaultValue($row['address'])));
    $name_dep = substr(defaultValue($row['name_dep']), 0, 29);
    $named = substr(defaultValue($row['named']), 0, 29);
    $spc_needs_one = substr(defaultValue($row['spc_needs']), 0, 23);
    $spc_needs_two = substr(defaultValue($row['spc_needs']), 23, 23);
    $fill = $row['fill'];
    $signature = $row['signature'];
    $status = $row['status'];
    $pk_dokter_radiology = $row['pk_dokter_radiology'];
    $approved_at = $row['approved_at'];
    $acc = $row['acc'];

    // kondisi mencari ditabel dokter radiology
    $row_dokrad = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT CONCAT(xray_dokter_radiology.dokrad_name,' ',xray_dokter_radiology.dokrad_lastname) AS dokrad_fullname,
        xray_dokter_radiology.pk,
        xray_dokter_radiology.dokrad_email,
        xray_dokter_radiology.dokradid,
        xray_dokter_radiology.username,
        xray_dokter_radiology.dokrad_img,
        xray_dokter_radiology.nip,
        xray_dokter_radiology.idtele
		FROM xray_dokter_radiology AS xray_dokter_radiology
		WHERE pk = '$pk_dokter_radiology'"
    ));

    $dokrad_name = defaultValue($row_dokrad['dokrad_fullname']);
    $nip = $row_dokrad['nip'];
    $link_dokrad_img = "http://" . $_SERVER['SERVER_NAME'] . ":9000/storage/" . $row_dokrad['dokrad_img'];
    // kondisi ketika dokrad_img null & ketika server laravel error
    $dokrad_img =  $row_dokrad['dokrad_img'] == null ? '../../radiology/pdf/scan-ttd-default.PNG' : (@file_get_contents($link_dokrad_img) === false ? '../../radiology/pdf/scan-ttd-default.PNG' : $link_dokrad_img);

    // kop surat
    $kopSurat = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kop_surat LIMIT 1"));
    $link_surat_image = "http://" . $_SERVER['SERVER_NAME'] . ":9000/storage/" . $kopSurat['image'];
    // kondisi ketika gambar kop surat null & ketika server laravel error
    $kop_surat_image = $kopSurat['image'] == null ? '../radiology/pdf/header-rs.jpg' : (@file_get_contents($link_surat_image) === false ? '../radiology/pdf/header-rs.jpg' : $link_surat_image);

    // qr code ttd dokter radiologi
    $link_qr_code_ttd = 'phpqrcode/ttddokter/' . $signature;
    $qr_code_ttd = @file_get_contents($link_qr_code_ttd) === false ? '../radiology/pdf/barcode-default.PNG' : $link_qr_code_ttd;

    // qr code hasil pasien
    $link_qr_code_pasien = 'phpqrcode/hasil-pasien/' . $uid . '.png';
    $qr_code_pasien = @file_get_contents($link_qr_code_pasien) === false ? '../radiology/pdf/barcode-default.PNG' : $link_qr_code_pasien;

    // tabel expertise menampilkan qr code hasil pasien dan signature dokter
    $expertise = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM xray_expertise LIMIT 1"));

    //Based on HTML2PDF by Clément Lavoillotte
    // memanggil library FPDF
    require('../radiology/pdf/fpdf.php');
    require('../radiology/pdf/hex.php');
    require('pdf/html-parser.php');

    // intance object dan memberikan pengaturan halaman PDF
    $pdf = new PDF('P', 'mm', 'A4');

    // membuat halaman baru
    $pdf->SetMargins(15, 23, 15);
    $pdf->SetAutoPageBreak(true, 40);
    $pdf->AddPage();
    // setting jenis font yang akan digunakan
    $pdf->SetFont('Arial', '', 10);
    // mencetak string 

    $pdf->SetTitle('Hasil expertise');

    $pdf->image($kop_surat_image, 13, 10, 185);
    $pdf->MultiCell(0, 18, '', 0, "J", false);


    // ------------------------------------------------------------

    $pdf->Cell(28, 5, 'No RM', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(55, 5, $pat_id, 0, 0, 'L');
    // ------------------
    $pdf->Cell(35, 5, 'No Foto', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(55, 5, $no_foto, 0, 1, 'L');
    // -----------------
    $pdf->Cell(28, 5, 'Nama', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(55, 5, $pat_name, 0, 0, 'L');
    // ------------------
    $pdf->Cell(35, 5, 'Ruang', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(65, 5, $name_dep, 0, 1, 'L');
    // -----------------
    $pdf->Cell(28, 5, 'Tgl Lahir', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(55, 5, defaultValueDate($pat_birthdate), 0, 0, 'L');
    // ------------------
    $pdf->Cell(35, 5, 'Dokter Pengirim', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(65, 5, $named, 0, 1, 'L');
    // -----------------
    $pdf->Cell(28, 5, 'Umur', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(55, 5, $age, 0, 0, 'L');
    // -----------------
    $pdf->Cell(35, 5, 'Dokter Radiologi', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(65, 5, substr($dokrad_name, 0, 35), 0, 1, 'L');
    //-------------------
    $pdf->Cell(28, 5, 'Jenis Kelamin', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(55, 5, $pat_sex, 0, 0, 'L');
    // -------------------
    $pdf->Cell(35, 5, 'Tanggal Pemeriksaan', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(65, 5, defaultValueDate($study_datetime), 0, 1, 'L');
    // -----------------
    $pdf->Cell(28, 5, 'Klinis', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(55, 5, $spc_needs_one, 0, 0, 'L');
    // -----------------
    $pdf->Cell(35, 5, 'Jenis Pemeriksaan', 0, 0, 'L');
    $pdf->Cell(3, 5, ':', 0, 0, 'L');
    $pdf->Cell(65, 5, $study_desc_one, 0, 1, 'L');
    // -----------------
    $pdf->Cell(28, 5, '', 0, 0, 'L');
    $pdf->Cell(3, 5, '', 0, 0, 'L');
    $pdf->Cell(55, 5, $spc_needs_two, 0, 0, 'L');
    // -----------------
    $pdf->Cell(35, 5, '', 0, 0, 'L');
    $pdf->Cell(3, 5, '', 0, 0, 'L');
    $pdf->Cell(65, 5, $study_desc_two, 0, 1, 'L');
    $pdf->Line(16, 83, 198, 83);
    $fill = str_replace("&nbsp;", " ", $fill);
    $fill = str_replace("&ndash;", "-", $fill);
    $fill = str_replace("&agrave;", "->", $fill);
    $fill = str_replace("&hellip;", "..", $fill);
    $fill = str_replace("&plusmn;", chr(177), $fill);
    $fill = str_replace("&deg;", chr(176), $fill);
    $fill = str_replace("&bull;", "-", $fill);
    $fill = str_replace("&ldquo;", '"', $fill);
    $fill = str_replace("&rdquo;", '"', $fill);
    $fill = str_replace(chr(225), chr(186), $fill);
    $fill = str_replace("&rsquo;", "'", $fill);
    $fill = str_replace("&lsquo;", "'", $fill);
    $fill = str_replace("&amp;", "&", $fill);
    $fill = str_replace("&quot;", "\"", $fill);
    $fill = str_replace("&#39;", "'", $fill);
    $fill = str_replace("&middot;", "-", $fill);
    $fill = str_replace("<ul>", "<br />", $fill);
    $fill = str_replace("<li>", "   " . chr(149) . " ", $fill);
    $fill = str_replace("</li>", "<br />", $fill);
    $fill = str_replace("</ul>", "<br />", $fill);
    $fill = str_replace('<div style="text-align: center;">', '<br /><p align="center">', $fill);
    $fill = str_replace('<div style="text-align: left;">', '<br /><p align="left">', $fill);
    $fill = str_replace('<div style="text-align: right;">', '<br /><p align="right">', $fill);
    $fill = str_replace('<div style="text-align:center;">', '<br /><p align="center">', $fill);
    $fill = str_replace('<div style="text-align:left;">', '<br /><p align="left">', $fill);
    $fill = str_replace('<div style="text-align:right;">', '<br /><p align="right">', $fill);

    $pdf->WriteHTML("<strong><u><p align='center'>Bismillahirrahmanirrahim</p></u></strong>");
    $pdf->WriteHTML("<br>");
    $pdf->WriteHtml($fill);
    $pdf->WriteHTML("<br>");
    $pdf->WriteHTML("<br>");

    $pdf->WriteHTML(
        "<p align='right'>Terimakasih atas kepercayaan TS</p>
		<p align='right'>Salam sejawat</p>"
    );

    if ($expertise['signature_dokter_radiologi'] == 'qr_code') {
        // jika ttd menggunakan signature QR CODE
        $pdf->Ln(2);
        $sign = $pdf->image($qr_code_ttd, 170, $pdf->GetY(), 25);
    } else if ($expertise['signature_dokter_radiologi'] == 'signature_scan') {
        // jika ttd menggunakan signature scan image
        $pdf->Ln(2);
        $pdf->image($dokrad_img, 140, $pdf->GetY(), 55);
    } else if ($expertise['signature_dokter_radiologi'] == 'signature_empty') {
        // jika ttd signature empty
    } else {
        // jika ttd tidak menggunakan signature dan image
    }

    if ($expertise['qr_code_pasien'] == 1) {
        // jika menggunakan qr code hasil pasien
        $hasilPasien = $pdf->image($qr_code_pasien, $pdf->GetX(), $pdf->GetY(), 25);
        $pdf->Ln(27);
        $pdf->Cell(0, 0, 'Hasil bisa diakses maximal 30 hari dari tanggal', 0, 0, 'L');
        $pdf->Cell(0, 0, $dokrad_name, 0, 1, 'R');
        $pdf->Cell(0, 9, 'dokter radiologi melakukan expertise ', 0, 0, 'L');
        $pdf->Cell(0, 9, $nip, 0, 0, 'R');
    } else {
        $pdf->Ln(27);
        $pdf->Cell(0, 0, $dokrad_name, 0, 1, 'R');
        $pdf->Cell(0, 9, $nip, 0, 0, 'R');
    }

    // untuk download PDF otomatis
    $tahun = date("Y", strtotime($approved_at));
    $bulan = date("m", strtotime($approved_at));
    $hari = date("d", strtotime($approved_at));
    $dir = "../radiology/pdf/expertise/$tahun/$bulan/$hari";

    // jika tidak ada foldernya, membuat folder berdasarkan hasil expertise pertama kali.
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    // download PDF
    $pdf->Output('F', "$dir/$uid.pdf");
}

function ubahdokterworklist($post)
{
    global $conn;

    $uid = $post['uid'];
    $dokradid = $post['dokradid'];
    // $status = $post['status'];
    if ($dokradid == '0') {
        $queryinsert = " INSERT INTO xray_order (uid, dokradid, dokrad_name) VALUES ('$uid',NULL,NULL)
	ON DUPLICATE KEY UPDATE dokradid = NULL, dokrad_name = NULL";
        mysqli_query($conn, $queryinsert);

        $query1 = "UPDATE xray_workload SET 
	pk_dokter_radiology = NULL
	WHERE uid = '$uid'
	";
        mysqli_query($conn, $query1);
    } else {
        $query4 = "SELECT * FROM xray_dokter_radiology WHERE dokradid = '$dokradid'";
        $data_exam1 = mysqli_query($conn, $query4);
        $row4 = mysqli_fetch_assoc($data_exam1);

        $pk = $row4['pk'];
        $dokradid1 = $row4['dokradid'];
        $dokradname1 = $row4['dokrad_name'] . ' ' . $row4['dokrad_lastname'];
        // echo $pk . ' ' . $dokradid1 . ' ' . $dokradname1 . ' ' . $dokradlastname1;
        // die();
        $queryinsert = " INSERT INTO xray_order (uid, dokradid, dokrad_name) VALUES ('$uid','$dokradid1','$dokradname1')
        ON DUPLICATE KEY UPDATE dokradid = '$dokradid1', dokrad_name = '$dokradname1'";
        mysqli_query($conn, $queryinsert);

        $query1 = "UPDATE xray_workload SET 
        pk_dokter_radiology = '$pk'
        WHERE uid = '$uid'
        ";
        mysqli_query($conn, $query1);
    }

    return mysqli_affected_rows($conn);
}

function ubahdokterworkload($post)
{
    global $conn;

    $uid = $post['uid'];
    $dokradid = $post['dokradid'];


    $query4 = "SELECT * FROM xray_dokter_radiology WHERE dokradid = '$dokradid'";
    $data_exam1 = mysqli_query($conn, $query4);
    $row4 = mysqli_fetch_assoc($data_exam1);

    $pk = $row4['pk'];
    $dokradid1 = $row4['dokradid'];
    $dokradname1 = $row4['dokrad_name'] . ' ' . $row4['dokrad_lastname'];

    $query = "UPDATE xray_order SET 
	dokradid = '$dokradid1',
	dokrad_name = '$dokradname1'
	WHERE uid = '$uid'
";
    mysqli_query($conn, $query);

    $query1 = "UPDATE xray_workload SET 
	pk_dokter_radiology = '$pk',
	status = 'waiting',
	fill = NULL,
	approved_at = NULL,
	approve_updated_at = NULL,
	signature = NULL,
	signature_datetime = NULL,
    priority_doctor = 'normal' 
	WHERE uid = '$uid'
	";
    mysqli_query($conn, $query1);

    // UPDATE XRAY_WORKLOAD_FILL is_default menjadi 0 berdasarkan uid dan changedoctor 1
    mysqli_query(
        $conn,
        "UPDATE xray_workload_fill SET 
        is_default = 0
        WHERE uid = '$uid'"
    );

    // INSERT XRAY_WORKLOAD_FILL
    mysqli_query(
        $conn,
        "INSERT INTO xray_workload_fill (uid, is_default, change_doctor_approved, created_at) 
		VALUES ('$uid', 0, 1, NOW())"
    );

    return mysqli_affected_rows($conn);
}


function new_template($new_template)
{
    global $conn;
    $title = $new_template['title'];
    $fill = $new_template['fill'];
    $username = $new_template['username'];

    mysqli_query(
        $conn,
        "INSERT INTO xray_template (title, fill, username) 
		VALUES ('$title','$fill','$username')"
    );

    return mysqli_affected_rows($conn);
}

function ubahDefaultExpertise($uid, $pk)
{
    global $conn;

    // UPDATE XRAY_WORKLOAD_FILL is_default menjadi 0 berdasarkan uid
    mysqli_query(
        $conn,
        "UPDATE xray_workload_fill SET 
		is_default = 0 
		WHERE uid = '$uid'"
    );

    // UPDATE XRAY_WORKLOAD_FILL is_default menjadi 1 berdasarkan pk
    mysqli_query(
        $conn,
        "UPDATE xray_workload_fill SET 
        is_default = 1 
        WHERE pk = '$pk'"
    );
}
