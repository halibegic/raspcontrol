<?php

namespace lib;

use lib\Uptime;
use lib\Memory;
use lib\CPU;
use lib\Storage;
use lib\Network;
use lib\Rbpi;
use lib\Users;
use lib\Temp;

$uptime = Uptime::uptime();
$ram = Memory::ram();
$swap = Memory::swap();
$cpu = CPU::cpu();
$cpu_heat = CPU::heat();
$hdd = Storage::hdd();
$hdd_alert = 'success';
for ($i = 0; $i < sizeof($hdd); $i++) {
    if ($hdd[$i]['alert'] == 'warning')
        $hdd_alert = 'warning';
}
$network = Network::connections();
$users = sizeof(Users::connected());
$temp = Temp::temp();

$external_ip = Rbpi::externalIp();

function icon_alert($alert) {
    echo '<i class="fa fa-';
    switch ($alert) {
        case 'success':
            echo 'check';
            break;
        case 'warning':
            echo 'exclamation-triangle text-warning';
            break;
        default:
            echo 'exclamation-triangle';
    }
    echo ' float-right" aria-hidden="true"></i>';
}
?>
<div class="stat mb-4">
    
    <div class="row">

        <div class="col-sm-3">
            <div class="tile bg-red">
                <div class="icon">
                    <i class="fa fa-home" aria-hidden="true"></i>
                </div>
                <p class="mb-4">Hostname</p>
                <h4 class="mb-0"><?php echo Rbpi::hostname(); ?></h4>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="tile bg-blue">
                <div class="icon">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                </div>
                <p class="mb-4">Internal IP</p>
                <h4 class="mb-0"><?php echo Rbpi::internalIp(); ?></h4>
            </div>            
        </div>

        <div class="col-sm-3">
            <div class="tile bg-green">
                <div class="icon">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                </div>
                <p class="mb-4">External IP</p>
                <h4 class="mb-0"><?php echo $external_ip; ?></h4>
            </div>            
        </div>

        <div class="col-sm-3">
            <div class="tile bg-orange">
                <div class="icon">
                    <i class="fa fa-play-circle" aria-hidden="true"></i>
                </div>
                <p class="mb-4">Server</p>
                <h4 class="mb-0"><?php echo Rbpi::webServer(); ?></h4>
            </div>
        </div>

    </div>

</div>

<div class="uptime mb-4">
    <a href="<?php echo DETAILS; ?>#check-uptime">
        <i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $uptime; ?>
    </a>
</div>

<div class="details">

    <div class="row">

        <div class="col-sm-6">

            <div class="pb-3">
                <i class="fa fa-asterisk" aria-hidden="true"></i> RAM
                <a href="<?php echo DETAILS; ?>#check-ram"><?php echo icon_alert($ram['alert']); ?></a>
            </div>

            <div class="pb-3">
                <i class="fa fa-refresh" aria-hidden="true"></i> Swap
                <a href="<?php echo DETAILS; ?>#check-swap"><?php echo icon_alert($swap['alert']); ?></a>
            </div>

            <div class="pb-3">
                <i class="fa fa-tasks" aria-hidden="true"></i> CPU
                <a href="<?php echo DETAILS; ?>#check-cpu"><?php echo icon_alert($cpu['alert']); ?></a>
            </div>

            <div class="pb-3">
                <i class="fa fa-fire" aria-hidden="true"></i> CPU
                <a href="<?php echo DETAILS; ?>#check-cpu-heat"><?php echo icon_alert($cpu_heat['alert']); ?></a>
            </div>

        </div>

        <div class="col-sm-6">

            <div class="pb-3">
                <i class="fa fa-archive" aria-hidden="true"></i> Storage
                <a href="<?php echo DETAILS; ?>#check-storage"><?php echo icon_alert($hdd_alert); ?></a>
            </div>

            <div class="pb-3">
                <i class="fa fa-globe" aria-hidden="true"></i> Network
                <a href="<?php echo DETAILS; ?>#check-network"><?php echo icon_alert($network['alert']); ?></a>
            </div>

            <div class="pb-3">
                <i class="fa fa-user" aria-hidden="true"></i> Users
                <a href="<?php echo DETAILS; ?>#check-users"><span class="badge badge-default float-right"><?php echo $users; ?></span></a>
            </div>

            <div class="pb-3">
                <i class="fa fa-fire" aria-hidden="true"></i> Temperature
                <a href="<?php echo DETAILS; ?>#check-temp"><?php echo icon_alert($temp['alert']); ?></a>
            </div>

        </div>

    </div>

</div>
