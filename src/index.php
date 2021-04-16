<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queues Workshop</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body class="bg-light">

<?php

include "SimpleXLSX.php";
$AF_xlsx = SimpleXLSX::parse('files/AF_sync.xlsx');
$QR_xlsx = SimpleXLSX::parse('files/QR_sync.xlsx');

?>

<main role="main" class="container">

    <div class="d-flex align-items-center p-3 my-3 text-dark-50 bg-purple rounded box-shadow">
        <div class="lh-100">
            <h5 class="mb-0 lh-100">Syncs dashboard</h5>
            <small>SpyderWeb - Since 2021</small>
        </div>
    </div>

    <?php if ($AF_xlsx): ?>
        <div class="my-3 p-3 bg-white rounded box-shadow">
            <div class="border-bottom border-gray d-flex justify-content-between align-items-center pb-2 mb-3">
                <h4 class="pb-2 mb-0 float-left">AF file</h4>
                <button style="width:100px;"
                        class="btn btn-primary"
                        onclick='sendQueue(3583,<?= json_encode($AF_xlsx->rows()) ?>)'>
                    Send
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm">

                    <?php
                    $i = 0;
                    foreach ($AF_xlsx->rows() as $elt):
                        if ($i == 0): ?>
                            <tr>
                                <?php for ($j = 0; $j <= count($elt); $j++): ?>
                                    <th><?= $elt[$j] ?></th>
                                <?php endfor; ?>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <?php for ($j = 0; $j <= count($elt); $j++): ?>
                                    <td><?= $elt[$j] ?></td>
                                <?php endfor; ?>
                            </tr>
                        <?php endif;
                        $i++;
                    endforeach; ?>

                </table>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($QR_xlsx): ?>
        <div class="my-3 p-3 bg-white rounded box-shadow">
            <div class="border-bottom border-gray d-flex justify-content-between align-items-center pb-2 mb-3">
                <h4 class="pb-2 mb-0 float-left">QR file</h4>
                <button style="width:100px;"
                        class="btn btn-primary"
                        onclick='sendQueue(3566,<?= json_encode($QR_xlsx->rows()) ?>)'>
                    Send
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-sm">

                    <?php
                    $i = 0;
                    foreach ($QR_xlsx->rows() as $elt):
                        if ($i == 0): ?>
                            <tr>
                                <?php for ($j = 0; $j <= count($elt); $j++): ?>
                                    <th><?= $elt[$j] ?></th>
                                <?php endfor; ?>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <?php for ($j = 0; $j <= count($elt); $j++): ?>
                                    <td><?= $elt[$j] ?></td>
                                <?php endfor; ?>
                            </tr>
                        <?php endif;
                        $i++;
                    endforeach; ?>

                </table>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function sendQueue(airlineId, data) {
            console.log(data);
            $.ajax({
                type: "POST",
                url: "producer.php",
                data: {airline_id: airlineId, file_data: JSON.stringify(data)},
                success: function (data) {
                    console.log(data);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    alert(thrownError);
                }
            });

        }
    </script>

</main>
</html>

