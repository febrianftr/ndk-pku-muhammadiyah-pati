<?php
require 'viewer-all.php';
require 'default-value.php';
require 'model/query-base-workload.php';
require 'model/query-base-order.php';
require 'model/query-base-study.php';
require 'model/query-base-patient.php';
require 'model/query-base-dokter-radiology.php';
require 'model/query-base-workload-fill.php';
require 'model/query-base-series.php';
require 'model/query-base-series-req.php';
require 'js/proses/function.php';
include "bahasa.php";

require __DIR__ . '/radiographer/vendor/autoload.php';

use GuzzleHttp\Client;

$pat_id = $_GET['pat_id'];
$username = $_SESSION['username'];

// delete study
if (isset($_POST["delete_study"])) {
    $study_iuid = $_POST['study_iuid'];
    try {
        // API HTML
        $clientHtml = new Client([
            'base_uri' => "http://$_SERVER[SERVER_ADDR]:19898",
        ]);

        $responseHtml = $clientHtml->request('POST', '/jmx-console/HtmlAdaptor', [
            'auth' => ['admin', 'efotoadmin'],
            'timeout' => 200,
            'read_timeout' => 200,
            'form_params' => [
                "methodIndex" => $_POST["methodIndex"],
                "action" => "invokeOp",
                "name" => "dcm4chee.archive:service=ContentEditService",
                "arg0" => $_POST["pk"]
            ],
            'http_errors' => false
        ]);
        $bodyHtml = $responseHtml->getBody();
        $dataHtml = json_decode($bodyHtml, true);
        // $code = $responseHtml->getStatusCode(); //200

        // API OHIF
        $clientOhif = new Client([
            'base_uri' => "http://$_SERVER[SERVER_ADDR]:9090",
        ]);

        $responseOhif = $clientOhif->request('POST', "/dcm4chee-arc/aets/DCM4CHEE/rs/studies/$study_iuid/reject/113039%5EDCM", [
            'headers' => [
                'Accept' => 'Application/json',
                'Content-Type' => 'Application/json'
            ],
            'http_errors' => false
        ]);
        $bodyOhif = $responseOhif->getBody();
        $dataOhif = json_decode($bodyOhif, true);
        // $code = $responseHtml->getStatusCode(); //200

        echo "<script>
                alert('Success');
                window.location.replace('workload-patient-detail.php?pat_id=$pat_id');
            </script>";
    } catch (GuzzleHttp\Exception\GuzzleException $th) {
        $message = $th->getMessage();
        echo $message;
        echo "<script>
                alert('$message');
                window.location.replace('workload-patient-detail.php?pat_id=$pat_id');
            </script>";
    }
}

// delete series
if (isset($_POST["delete_series"])) {
    $study_iuid = $_POST['study_iuid'];
    $series_iuid = $_POST['series_iuid'];
    try {
        // API HTML
        $clientHtml = new Client([
            'base_uri' => "http://$_SERVER[SERVER_ADDR]:19898",
        ]);

        $responseHtml = $clientHtml->request('POST', '/jmx-console/HtmlAdaptor', [
            'auth' => ['admin', 'efotoadmin'],
            'timeout' => 200,
            'read_timeout' => 200,
            'form_params' => [
                "methodIndex" => $_POST["methodIndex"],
                "action" => "invokeOp",
                "name" => "dcm4chee.archive:service=ContentEditService",
                "arg0" => $_POST["pk"]
            ],
            'http_errors' => false
        ]);
        $bodyHtml = $responseHtml->getBody();
        $dataHtml = json_decode($bodyHtml, true);
        // $code = $responseHtml->getStatusCode(); //200

        // API OHIF
        $clientOhif = new Client([
            'base_uri' => "http://$_SERVER[SERVER_ADDR]:9090",
        ]);

        $responseOhif = $clientOhif->request('POST', "/dcm4chee-arc/aets/DCM4CHEE/rs/studies/$study_iuid/series/$series_iuid/reject/113039%5EDCM", [
            'headers' => [
                'Accept' => 'Application/json',
                'Content-Type' => 'Application/json'
            ],
            'http_errors' => false
        ]);
        $bodyOhif = $responseOhif->getBody();
        $dataOhif = json_decode($bodyOhif, true);
        // $code = $responseHtml->getStatusCode(); //200

        echo "<script>
                alert('Success');
                window.location.replace('workload-patient-detail.php?pat_id=$pat_id');
            </script>";
    } catch (GuzzleHttp\Exception\GuzzleException $th) {
        $message = $th->getMessage();
        echo $message;
        echo "<script>
                alert('$message');
                window.location.replace('workload-patient-detail.php?pat_id=$pat_id');
            </script>";
    }
}

$query = mysqli_query(
    $conn_pacsio,
    "SELECT 
    $select_patient,
    $select_study,
    $select_order,
    $select_workload
    FROM $table_patient
    JOIN $table_study
    ON patient.pk = study.patient_fk
    JOIN $table_workload
    ON study.study_iuid = xray_workload.uid
    LEFT JOIN $table_order
    ON xray_order.uid = study.study_iuid
    WHERE patient.pat_id = '$pat_id'
    ORDER BY study.study_datetime DESC"
);


// if (isset($_POST["save_move_study"])) {
//     global $conn_pacsio;

//     $study_iuid_current = $_POST['study_iuid_current'];
//     $study_iuid_move = $_POST['study_iuid_move'];
//     $move = $_POST['move'];
//     $current = $_POST['current'];

//     // HAPUS STUDY SAAT INI
//     try {
//         // API OHIF HAPUS PASIEN SAAT INI
//         $clientOhif = new Client([
//             'base_uri' => "http://$_SERVER[SERVER_ADDR]:9090",
//         ]);

//         $responseOhifDelete = $clientOhif->request('POST', "/dcm4chee-arc/aets/DCM4CHEE/rs/studies/$study_iuid_current/reject/113039%5EDCM", [
//             'headers' => [
//                 'Accept' => 'Application/json',
//                 'Content-Type' => 'Application/json'
//             ],
//             'http_errors' => false
//         ]);
//         $bodyOhifDelete = $responseOhifDelete->getBody();
//         $dataOhifDelete = json_decode($bodyOhifDelete, true);
//         $codeOhifDelete = $responseOhifDelete->getStatusCode();

//         // jika di ohif terhapus lakukan
//         if ($codeOhifDelete == 200) {
//             // PINDAHKAN SERIES KE PASIEN LAIN

//             $query_move = mysqli_query($conn_pacsio, "SELECT pk AS pk_series FROM series WHERE study_fk = '$current'");
//             while ($row_query_move = mysqli_fetch_assoc($query_move)) {
//                 $pk_series = $row_query_move['pk_series'];
//                 mysqli_query($conn_pacsio, "UPDATE series SET study_fk = '$move' WHERE pk = '$pk_series'");
//             }

//             // API OHIF SEND KE PASIEN LAIN
//             $responseOhifSend = $clientOhif->request('POST', "/dcm4chee-arc/aets/DCM4CHEE/dimse/dcmPACS/studies/$study_iuid_move/export/dicom:DCM4CHEE?priority=0", [
//                 'headers' => [
//                     'Accept' => 'Application/json',
//                     'Content-Type' => 'Application/json'
//                 ],
//                 'http_errors' => false
//             ]);
//             $bodyOhifSend = $responseOhifSend->getBody();
//             $dataOhifSend = json_decode($bodyOhifSend, true);
//             $codeOhifSend = $responseOhifSend->getStatusCode();
//             echo "<script type='text/javascript'>
//                 alert('Berhasil move study') 
//                 window.setTimeout(function(){ 
//                     history.back();
//                 }); 
//             </script>";
//         } else {
//             echo "<script type='text/javascript'>
//                 alert('Gagal move study') 
//                 window.setTimeout(function(){ 
//                 history.back();
//                 });  
//             </script>";
//         }
//     } catch (GuzzleHttp\Exception\GuzzleException $th) {
//         echo "<script type='text/javascript'>
//                 alert('Gagal move study, Tidak konek ke API') 
//                 window.setTimeout(function(){ 
//                 history.back();
//                 });  
//             </script>";
//     }
// }

if (isset($_POST["save_move_series"])) {
    global $conn_pacsio;

    $study_iuid_current = $_POST['study_iuid_current'];
    $study_iuid_move = $_POST['study_iuid_move'];
    $move = $_POST['move'];
    $current = $_POST['current'];
    $row_series_current = mysqli_fetch_assoc(mysqli_query($conn_pacsio, "SELECT series_iuid FROM series WHERE pk = '$current'"));
    $series_iuid_current = $row_series_current['series_iuid'];

    // HAPUS SERIES SAAT INI
    try {
        // API OHIF HAPUS PASIEN SAAT INI
        $clientOhif = new Client([
            'base_uri' => "http://$_SERVER[SERVER_ADDR]:9090",
        ]);

        $responseOhifDelete = $clientOhif->request('POST', "/dcm4chee-arc/aets/DCM4CHEE/rs/studies/$study_iuid_current/series/$series_iuid_current/reject/113039%5EDCM", [
            'headers' => [
                'Accept' => 'Application/json',
                'Content-Type' => 'Application/json'
            ],
            'http_errors' => false
        ]);
        $bodyOhifDelete = $responseOhifDelete->getBody();
        $dataOhifDelete = json_decode($bodyOhifDelete, true);
        $codeOhifDelete = $responseOhifDelete->getStatusCode();

        // jika di ohif terhapus lakukan
        if ($codeOhifDelete == 200) {
            // PINDAHKAN SERIES KE PASIEN LAIN
            mysqli_query($conn_pacsio, "UPDATE series SET study_fk = '$move' WHERE pk = '$current'");

            // API OHIF SEND KE PASIEN LAIN
            $responseOhifSend = $clientOhif->request('POST', "/dcm4chee-arc/aets/DCM4CHEE/dimse/dcmPACS/studies/$study_iuid_move/series/$series_iuid_current/export/dicom:DCM4CHEE?priority=0", [
                'headers' => [
                    'Accept' => 'Application/json',
                    'Content-Type' => 'Application/json'
                ],
                'http_errors' => false
            ]);
            $bodyOhifSend = $responseOhifSend->getBody();
            $dataOhifSend = json_decode($bodyOhifSend, true);
            $codeOhifSend = $responseOhifSend->getStatusCode();
            echo "<script type='text/javascript'>
                alert('Berhasil move series') 
                window.setTimeout(function(){ 
                    history.back();
                }); 
            </script>";
        } else {
            echo "<script type='text/javascript'>
                alert('Gagal move series') 
                window.setTimeout(function(){ 
                history.back();
                });  
            </script>";
        }
    } catch (GuzzleHttp\Exception\GuzzleException $th) {
        echo "<script type='text/javascript'>
                alert('Gagal move series, Tidak konek ke API') 
                window.setTimeout(function(){ 
                history.back();
                });  
            </script>";
    }
}

if (isset($_POST["save_copy_series"])) {
    global $conn_pacsio;

    $study_iuid_current = $_POST['study_iuid_current'];
    $study_iuid_copy = $_POST['study_iuid_copy'];
    $copy = $_POST['copy'];
    $current = $_POST['current'];
    $row_series_current = mysqli_fetch_assoc(mysqli_query($conn_pacsio, "SELECT series_iuid FROM series WHERE pk = '$current'"));
    $series_iuid_current = $row_series_current['series_iuid'];

    try {
        $clientOhif = new Client([
            'base_uri' => "http://$_SERVER[SERVER_ADDR]:9090",
        ]);

        // API OHIF COPY KE PASIEN LAIN
        $responseOhifCopy = $clientOhif->request('POST', "dcm4chee-arc/aets/DCM4CHEE/rs/studies/$study_iuid_copy/copy", [
            'headers' => [
                'Accept' => 'Application/json',
                'Content-Type' => 'Application/json'
            ],
            'json' => [
                "StudyInstanceUID" => $study_iuid_current,
                "ReferencedSeriesSequence" => [
                    [
                        "SeriesInstanceUID" => $series_iuid_current
                    ]
                ]
            ],
            'http_errors' => false
        ]);
        $bodyOhifCopy = $responseOhifCopy->getBody();
        $dataOhifCopy = json_decode($bodyOhifCopy, true);

        // API OHIF SEND KE PASIEN LAIN
        $responseOhifSend = $clientOhif->request('POST', "/dcm4chee-arc/aets/DCM4CHEE/dimse/DCM4CHEE/studies/$study_iuid_copy/export/dicom:dcmPACS?priority=0", [
            'headers' => [
                'Accept' => 'Application/json',
                'Content-Type' => 'Application/json'
            ],
            'http_errors' => false
        ]);
        $bodyOhifSend = $responseOhifSend->getBody();
        $dataOhifSend = json_decode($bodyOhifSend, true);
        $codeOhifSend = $responseOhifSend->getStatusCode();
        echo "<script type='text/javascript'>
                alert('Berhasil copy') 
                window.setTimeout(function(){ 
                    history.back();
                }); 
            </script>";
    } catch (GuzzleHttp\Exception\GuzzleException $th) {
        echo "<script type='text/javascript'>
                alert('Gagal copy series, Tidak konek ke API') 
                window.setTimeout(function(){ 
                history.back();
                });  
            </script>";
    }
}

?>
<style>
    .icon-fill:hover {
        background-color: deepskyblue;
    }
</style>
<div class="col-12" style="padding: 0;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item"><a href="workload.php">Workload</a></li>
            <li class="breadcrumb-item active">Patient Detail</li>
        </ol>
    </nav>
</div>
<div class="container-fluid">
    <h3 class="text-center">Patient Detail</h3>
    <?php require '../modal.php'; ?>
    <table class="table-dicom" id="example" style="margin-top: 3px;" cellpadding="8" cellspacing="0">
        <tbody class="thead1">
            <?php
            if (mysqli_num_rows($query) > 0) {
                while ($row = mysqli_fetch_assoc($query)) {
                    $pk_study = $row['pk_study'];
                    $pat_name = defaultValue(removeCharacter($row['pat_name']));
                    $pat_sex = styleSex($row['pat_sex']);
                    $pat_birthdate = defaultValueDate($row['pat_birthdate']);
                    $age = diffDate($row['pat_birthdate']);
                    $study_iuid = defaultValue($row['study_iuid']);
                    $study_datetime = defaultValueDateTime($row['study_datetime']);
                    $accession_no = defaultValue($row['accession_no']);
                    $ref_physician = defaultValue($row['ref_physician']);
                    $study_desc_pacsio = defaultValue($row['study_desc_pacsio']);
                    $mods_in_study = defaultValue($row['mods_in_study']);
                    $num_series = defaultValue($row['num_series']);
                    $num_instances = defaultValue($row['num_instances']);
                    $created_time = defaultValueDateTime($row['created_time']);
                    $updated_time = defaultValueDateTime($row['updated_time']);
                    $pat_id = defaultValue($row['pat_id']);
                    $weight = defaultValue($row['weight']);
                    $no_foto = defaultValue($row['no_foto']);
                    $address = defaultValue($row['address']);
                    $name_dep = defaultValue($row['name_dep']);
                    $named = defaultValue($row['named']);
                    $contrast = defaultValue($row['contrast']);
                    $priority = defaultValue($row['priority']);
                    $priority_doctor = defaultValue($row['priority_doctor']);
                    $radiographer_name = defaultValue($row['radiographer_name']);
                    $create_time = defaultValueDateTime($row['create_time']);
                    $examed_at = defaultValueDateTime($row['examed_at']);
                    $pat_state = defaultValue($row['pat_state']);
                    $spc_needs = defaultValue($row['spc_needs']);
                    $payment = defaultValue($row['payment']);
                    $status = styleStatus($row['status'], $study_iuid);
                    $approved_at = defaultValueDateTime($row['approved_at']);
                    $spendtime = spendTime($study_datetime, $approved_at, $row['status']);
                    $fromorder = $row['fromorder'];
                    $pk_dokter_radiology = $row['pk_dokter_radiology'];
            ?>
                    <div class="card text-light" style="background: unset; box-shadow: none;">
                        <div class="card-header card-header-nd">
                            <?= $mods_in_study ?> - <?= $study_datetime ?> - <b><?= $study_desc_pacsio ?></b><br />
                            Study Iuid : <?= $study_iuid ?>
                        </div>
                        <div class="card-body card-body-nd">
                            <div class="row">
                                <div class="col">
                                    <div style="padding: 0 6px;">
                                        <label class="card-title-patient-detail"><?= $pat_name ?> / <?= $age ?></label>
                                    </div>
                                    <div>
                                        <?php
                                        if ($_SESSION['level'] == "radiographer") { ?>
                                            <!-- form delete by study -->
                                            <form method="POST" action="#" id="form">
                                                <input type="hidden" name="pk" id="pk" value="<?= $pk_study; ?>">
                                                <input type="hidden" name="methodIndex" id="methodIndex" value="14">
                                                <input type="hidden" name="study_iuid" id="study_iuid" value="<?= $study_iuid; ?>">
                                                <button class="ahref-edit btn btn-danger btn-sm btn-delete-study" style="font-size: 15px;" name="delete_study" style="text-decoration:none;" id="delete_study" onclick="return confirm('Are you sure delete study?');">
                                                    <span><i class="fas fa-trash-alt" data-toggle="tooltip" title="Delete Study"></i>&nbsp; Delete Study</span>
                                                </button>
                                            </form>
                                            <!-- <a href="#" style="background-color: grey; color:white;" class="btn btn-sm btn-gen hasil-move-image" data-parent="<?= $pk_study; ?>" data-child="<?= $pk_study; ?>" data-study-iuid-parent="<?= $study_iuid; ?>" data-is-move="study" title="Detail">MOVE STUDY</a> -->
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row row-cols-1 row-cols-lg-4 row-cols-xl-6">
                                <?php

                                $query1 = mysqli_query(
                                    $conn_pacsio,
                                    "SELECT
                                    $select_series,
                                    $select_series_req,
                                    sop_iuid
                                    FROM $table_series
                                    JOIN instance
                                    ON series.pk = instance.series_fk
                                    LEFT JOIN $table_series_req
                                    ON series.pk = series_req.series_fk
                                    WHERE study_fk = '$pk_study'
                                    GROUP BY series.pk"
                                );
                                while ($row1 = mysqli_fetch_assoc($query1)) {
                                    $pk_series = $row1["pk_series"];
                                    $series_iuid = $row1["series_iuid"];
                                    $series_desc = $row1["series_desc"];
                                    $src_aet = $row1["src_aet"];
                                    $num_instances = $row1["num_instances"];
                                    $sop_iuid = $row1["sop_iuid"];
                                    $updated_time_series = $row1["updated_time"];
                                ?>
                                    <div class="col mb-4">
                                        <div class="card card-nd">
                                            <?php
                                            if ($_SERVER['SERVER_NAME'] == $hostname['ip_publik']) {
                                                $port = '19898';
                                            } else {
                                                $port = '19898';
                                            }
                                            $link_study_image = "http://$_SERVER[SERVER_NAME]:$port/wado?requestType=WADO&studyUID=$study_iuid&seriesUID=$series_iuid&objectUID=$sop_iuid";
                                            ?>
                                            <img src="<?= $link_study_image ?>" class="card-img-top" alt="image series">
                                            <div class="card-body">
                                                <label class="card-title-patient-detail-sub"><?= $series_desc . " - " . $src_aet . " - " . $num_instances ?></label>
                                                <p class="card-text mb-0"><small class="text-muted"><?= defaultValueDateTime($updated_time_series) ?></small></p>
                                                <p class="card-text mb-0">
                                                    <?php if ($_SESSION['level'] == "radiographer") { ?>
                                                        <!-- form delete by series -->
                                                <form method="POST" action="#" id="form">
                                                    <input type="hidden" name="pk" value="<?= $pk_series; ?>">
                                                    <input type="hidden" name="methodIndex" value="16">
                                                    <input type="hidden" name="study_iuid" id="study_iuid" value="<?= $study_iuid; ?>">
                                                    <input type="hidden" name="series_iuid" id="series_iuid" value="<?= $series_iuid; ?>">
                                                    <button class="ahref-edit btn btn-warning btn-sm w-100 text-dark btn-delete-study" name="delete_series" style="text-decoration:none;" onclick="return confirm('Are you sure delete series?');">
                                                        <span data-toggle="tooltip" title="Delete Series"> &nbsp; Delete Series</span>
                                                    </button>
                                                </form>
                                                </p>
                                                <p class="card-text mb-0">
                                                    <a href="#" class="ahref-edit btn btn-sm btn-info w-100 btn-delete-study hasil-move-image" data-parent="<?= $pk_study; ?>" data-child="<?= $pk_series; ?>" data-study-iuid-parent="<?= $study_iuid; ?>" data-is-move="series" title="Move">Move Series</a>
                                                </p>
                                                <p class="card-text mb-0">
                                                    <a href="#" class="ahref-edit btn btn-sm btn-info w-100 btn-delete-study hasil-copy-image" data-parent="<?= $pk_study; ?>" data-child="<?= $pk_series; ?>" data-study-iuid-parent="<?= $study_iuid; ?>" data-is-copy="series" title="Copy">Copy Series</a>
                                                </p>
                                            <?php }
                                            ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <hr />
                <?php
                }
            } else {
                ?>
                <tr>
                    <td colspan="10">Data Tidak Ada</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="js/3.1.1/jquery.min.js"></script>
<script src="../js/proses/workload-fill-detail.js?v=<?= $random; ?>"></script>