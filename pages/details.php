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
$net_connections = Network::connections();
$net_eth = Network::ethernet();
$users = Users::connected();
$temp = Temp::temp();

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
    echo '" aria-hidden="true"></i>';
}

function shell_to_html_table_result($shellExecOutput) {
    $shellExecOutput = preg_split('/[\r\n]+/', $shellExecOutput);

    // remove double (or more) spaces for all items
    foreach ($shellExecOutput as &$item) {
        $item = preg_replace('/[[:blank:]]+/', ' ', $item);
        $item = trim($item);
    }

    // remove empty lines
    $shellExecOutput = array_filter($shellExecOutput);

    // the first line contains titles
    $columnCount = preg_match_all('/\s+/', $shellExecOutput[0]);
    $shellExecOutput[0] = '<tr><th>' . preg_replace('/\s+/', '</th><th>', $shellExecOutput[0], $columnCount) . '</th></tr>';
    $tableHead = $shellExecOutput[0];
    unset($shellExecOutput[0]);

    // others lines contains table lines
    foreach ($shellExecOutput as &$item) {
        $item = '<tr><td>' . preg_replace('/\s+/', '</td><td>', $item, $columnCount) . '</td></tr>';
    }

    // return the build table
    return '<table class=\'table table-striped\'>'
            . '<thead>' . $tableHead . '</thead>'
            . '<tbody>' . implode($shellExecOutput) . '</tbody>'
            . '</table>';
}
?>

<table class="table table-clean table-responsive mb-0">

    <tr id="check-system">
        
        <td class="title">
            <strong><i class="fa fa-cog"></i> System</strong>
        </td>

        <td></td>
        <td>
            hostname: <span class="text-info"><?php echo Rbpi::hostname(true); ?></span>
            <br />distribution: <span class="text-info"><?php echo Rbpi::distribution(); ?></span>
            <br />kernel: <?php echo Rbpi::kernel(); ?>
            <br />firmware: <?php echo Rbpi::firmware(); ?>
        </td>
    </tr>

    <tr id="check-uptime">
        
        <td class="title">
            <strong><i class="fa fa-clock-o"></i> Uptime</strong>
        </td>

        <td></td>
        <td><?php echo $uptime; ?></td>
    </tr>

    <tr id="check-ram">
        
        <td class="title">
            <strong><i class="fa fa-asterisk"></i> RAM</strong>
        </td>

        <td><?php echo icon_alert($ram['alert']); ?></td>
        <td>
            <div class="progress mt-1 mb-1" id="popover-ram">
                <div class="progress-bar bg-<?php echo $ram['alert']; ?>" style="width: <?php echo $ram['percentage']; ?>%;"><?php echo $ram['percentage']; ?>%</div>
            </div>
            <div id="popover-ram-head" class="hide">Top RAM eaters</div>
            <div id="popover-ram-body" class="hide"><?php echo shell_to_html_table_result($ram['detail']); ?></div>
            free: <span class="text-success"><?php echo $ram['free']; ?>Mb</span>  &middot; used: <span class="text-warning"><?php echo $ram['used']; ?>Mb</span> &middot; total: <?php echo $ram['total']; ?>Mb
        </td>
    </tr>

    <tr id="check-swap">
        
        <td class="title">
            <strong><i class="fa fa-refresh"></i> Swap</strong>
        </td>

        <td><?php echo icon_alert($swap['alert']); ?></td>
        <td>
            <div class="progress mt-1 mb-1">
                <div class="progress-bar bg-<?php echo $swap['alert']; ?>" style="width: <?php echo $swap['percentage']; ?>%;"><?php echo $swap['percentage']; ?>%</div>
            </div>
            free: <span class="text-success"><?php echo $swap['free']; ?>Mb</span>  &middot; used: <span class="text-warning"><?php echo $swap['used']; ?>Mb</span> &middot; total: <?php echo $swap['total']; ?>Mb
        </td>
    </tr>

    <tr id="check-cpu">
        
        <td class="title">
            <strong><i class="fa fa-tasks"></i> CPU</strong>
        </td>

        <td><?php echo icon_alert($cpu['alert']); ?></td>
        <td>
            loads: <?php echo $cpu['loads']; ?> [1 min] &middot; <?php echo $cpu['loads5']; ?> [5 min] &middot; <?php echo $cpu['loads15']; ?> [15 min]
            <br />running at <span class="text-info"><?php echo $cpu['current']; ?></span> (min: <?php echo $cpu['min']; ?>  &middot;  max: <?php echo $cpu['max']; ?>)
            <br />governor: <strong><?php echo $cpu['governor']; ?></strong>
        </td>
    </tr>

    <tr id="check-cpu-heat">
        
        <td class="title">
            <strong><i class="fa fa-fire"></i> CPU</strong>
        </td>

        <td><?php echo icon_alert($cpu_heat['alert']); ?></td>
        <td>
            <div class="progress mt-1 mb-1" id="popover-cpu">
                <div class="progress-bar bg-<?php echo $cpu_heat['alert']; ?>" style="width: <?php echo $cpu_heat['percentage']; ?>%;"><?php echo $cpu_heat['percentage']; ?>%</div>
            </div>
            <div id="popover-cpu-head" class="hide">Top CPU eaters</div>
            <div id="popover-cpu-body" class="hide"><?php echo shell_to_html_table_result($cpu_heat['detail']); ?></div>
            heat: <span class="text-info"><?php echo $cpu_heat['degrees']['celsius']; ?>°C</span> / <span class="text-info"><?php echo $cpu_heat['degrees']['fahrenheit']; ?>°F</span>
        </td>
    </tr>

    <tr class="storage" id="check-storage">

        <td class="title" rowspan="<?php echo sizeof($hdd); ?>">
            <strong><i class="fa fa-archive"></i> Storage</strong>
        </td>

        <?php
        for ($i = 0; $i < sizeof($hdd); $i++) {
            echo '<td style="padding-left: 10px;">', icon_alert($hdd[$i]['alert']), '</td>
        <td>
          <i class="fa fa-folder-open"></i> ', $hdd[$i]['name'], '
          <div class="progress mt-1 mb-1">
            <div class="progress-bar bg-', $hdd[$i]['alert'], '" style="width: ', $hdd[$i]['percentage'], '%;">', $hdd[$i]['percentage'], '%</div>
          </div>
          free: <span class="text-success">', $hdd[$i]['free'], 'b</span> &middot; used: <span class="text-warning">', $hdd[$i]['used'], 'b</span> &middot; total: ', $hdd[$i]['total'], 'b &middot; format: ', $hdd[$i]['format'], '
        </td>
      </tr>
      ', ($i == sizeof($hdd) - 1) ? null : '<tr class="storage">';
        }
        ?>

    <tr id="check-network">
        
        <td class="title">
            <strong><i class="fa fa-globe"></i> Network</strong>
        </td>

        <td><?php echo icon_alert($net_connections['alert']); ?></td>
        <td>
            IP: <span class="text-info"><?php echo Rbpi::internalIp(); ?></span> [internal] &middot;
            <span class="text-info"><?php echo Rbpi::externalIp(); ?></span> [external]
            <br />received: <strong><?php echo $net_eth['down']; ?>Mb</strong> &middot; sent: <strong><?php echo $net_eth['up']; ?>Mb</strong> &middot; total: <?php echo $net_eth['total']; ?>Mb
            <br />connections: <?php echo $net_connections['connections']; ?>
        </td>
    </tr>

    <tr id="check-users">
        
        <td class="title">
            <strong><i class="fa fa-user"></i> Users</strong>
        </td>

        <td><span class="badge badge-default"><?php echo sizeof($users); ?></span></td>
        <td>
            <ul class="list-unstyled">
                <?php
                if (sizeof($users) > 0) {
                    for ($i = 0; $i < sizeof($users); $i++)
                        echo '<li><span class="text-info">', $users[$i]['user'], '</span> since ', $users[$i]['date'], ' at ', $users[$i]['hour'], ' from <strong>', $users[$i]['ip'], '</strong> ', $users[$i]['dns'], '</li>', "\n";
                }
                else
                    echo '<li>no user logged in</li>';
                ?>
            </ul>
        </td>
    </tr>

    <?php
    if ($temp['degrees'] != "N/A") {
        ?>
        <tr id="check-temp">
            
            <td class="title">
                <strong><i class="fa fa-fire"></i> DS18B20</strong>
            </td>

            <td><?php echo icon_alert($temp['alert']); ?></td>
            <td>
                <span class="text-info"><?php echo $temp['degrees']; ?></span>
            </td>
        </tr>
        <?php
    }
    ?>

</table>
