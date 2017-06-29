<?php

namespace lib;

use lib\Services;

$services = Services::services();

function label_service($status) {
    echo '<span class="badge badge-';
    switch ($status) {
        case '+':
            echo 'success';
            break;
        case '?':
            echo 'warning';
            break;
        default:
            echo 'danger';
    }
    echo '">';
    switch ($status) {
        case '+':
            echo 'Running';
            break;
        case '?':
            echo 'Unknown';
            break;
        default:
            echo 'Stopped';
    }
    echo '</span>';
}
?>
<div class="row">

    <div class="col-2">

        <strong class="text-bold">Services</strong>

    </div>

    <div class="col-10">

        <table class="table table-clean table-responsive mb-0">

            <?php for ($i = 0; $i < sizeof($services); $i++) : ?>

                <tr class="services" id="check-services">
                    <td>
                        <a data-rootaction="changeservicestatus" data-service-name="<?php echo $services[$i]["name"] ?>" class="rootaction" href="javascript:;">
                            <?php echo label_service($services[$i]['status']); ?>        
                        </a>
                    </td>
                    <td><?php echo $services[$i]['name']; ?></td>
                </tr>

            <?php endfor; ?>

        </table>

    </div>

</div>
