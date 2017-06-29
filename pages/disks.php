<?php

namespace lib;

use lib\Disks;

$disks = Disks::disks();

function label_partition($status) {
    echo '<span class="badge badge-';
    switch ($status) {
        default:
            echo 'success';
            break;
        case '':
            echo 'danger';
            break;
    }
    echo '">';
    switch ($status) {
        default:
            echo 'Mounted';
            break;
        case '':
            echo 'Not Mounted';
            break;
    }
    echo '</span>';
}
?>

<div class="row">

    <div class="col-2">

        <strong class="text-bold">Disks</strong>
        
    </div>

    <div class="col-10">

        <table class="table table-clean table-responsive mb-0">

            <?php for ($i = 0; $i < sizeof($disks); $i++) : ?>

                <tr class="disks" id="check-disks">
                    
                    <?php if ($disks[$i]["type"] != "disk") : ?>

                        <?php if (strpos($disks[$i]['name'], "sda") !== false) : ?>
                        
                            <td>
                                <a data-rootaction="changepartitionstatus" data-partition-name="<?php echo $disks[$i]["name"]; ?>" data-curr-mountpoint="<?php echo $disks[$i]["mountpoint"]; ?>" class="rootaction" href="javascript:;">
                                    <?php echo label_partition($disks[$i]['mountpoint']); ?>
                                </a>
                            </td>
                        
                        <?php else : ?>
                    
                            <td>
                                <?php echo label_partition($disks[$i]['mountpoint']); ?>
                            </td>

                        <?php endif; ?>

                        <td>
                            <?php echo $disks[$i]['name'] . "<br>Size: " . $disks[$i]['size'] . "<br>Mountpoint: " . $disks[$i]['mountpoint']; ?>
                        </td>

                    <?php else : ?>
                      
                        <td class="icon"><?php echo $disks[$i]['name']; ?></td>

                    <?php endif; ?>

                </tr>
            
            <?php endfor; ?>

        </table>

    </div>

</div>
