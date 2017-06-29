<?php

namespace lib;

spl_autoload_extensions('.php');
spl_autoload_register();

session_start();

require 'config.php';

require 'lib/phpseclib/Net/SSH2.php';

$ssh = new \Net_SSH2('localhost');

// authentification
if (isset($_SESSION['authentificated']) && $_SESSION['authentificated']) {
    if (empty($_GET['page']))
        $_GET['page'] = 'home';
    $_GET['page'] = htmlspecialchars($_GET['page']);
    $_GET['page'] = str_replace("\0", '', $_GET['page']);
    $_GET['page'] = str_replace(DIRECTORY_SEPARATOR, '', $_GET['page']);
    $display = true;

    function is_active($page) {
        if ($page == $_GET['page'])
            echo 'active';
    }

} else {
    $_GET['page'] = 'login';
    $display = false;
}

$page = 'pages' . DIRECTORY_SEPARATOR . $_GET['page'] . '.php';
$page = file_exists($page) ? $page : 'pages' . DIRECTORY_SEPARATOR . '404.php';

if (isset($_GET['action']) && isset($_GET['username']) && isset($_GET['password'])) {
    if ($ssh->login($_GET['username'], $_GET['password'])) {
        $action = $_GET['action'];
        if ($action == 'reboot') {
            echo "Action: " . $_GET["action"] . "\\nSuccessfully perfomed ";           
            $ssh->exec("sudo shutdown -r now");
        } else if ($action == 'shutdown') {
            echo "Action: " . $_GET["action"] . "\\nSuccessfully perfomed ";
            $ssh->exec("sudo shutdown -h now");
        } else if ($action == 'changeservicestatus') {
            $services = Services::services();
            for ($i = 0; $i < sizeof($services); $i++) {
                if ($services[$i]['name'] == $_GET['servicename']) {
                    if ($services[$i]['status'] == '+') {
                        $ssh->exec("sudo service " . $services[$i]['name'] . " stop");
                        echo "Service: " . $services[$i]['name'] . " stopped";
                    } else {
                        $ssh->exec("sudo service " . $services[$i]['name'] . " start");
                        echo "Service: " . $services[$i]['name'] . " started";
                    }
                    break;
                }
            }
        } else if ($action == 'changepartitionstatus') {
            $disks = Disks::disks();
            for ($i = 0; $i < sizeof($disks); $i++) {
                if ($disks[$i]['name'] == $_GET['partitionname']) {
                    if ($disks[$i]['mountpoint'] == '') {
                        if (isset($_GET['mountpoint'])) {
                            $ssh->exec("sudo mount /dev/" . $disks[$i]['name'] . " '" . str_replace("%20", " ", $_GET['mountpoint']) . "'");
                            echo "Partition: " . $disks[$i]['name'] . "\nMounted on: " . str_replace("%20", " ", $_GET['mountpoint']);
                        }
                    } else {
                        $ssh->exec("sudo umount /dev/" . $disks[$i]['name']);
                        echo "Partition: " . $disks[$i]['name'] . " unmounted";
                    }
                    break;
                }
            }
        }
    } else {
        echo 'Can\'t perform ' . $_GET["action"] . '\\nError: Login failed';
    }

    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="author" content="harmoN" />
        <meta name="robots" content="noindex, nofollow, noarchive" />
        <link rel="icon" href="img/favicon.ico">
        <title>Raspcontrol</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="css/font-awesome.min.css" rel="stylesheet">
        <link href="css/raspcontrol.css" rel="stylesheet">
        <link href="css/jquery-ui.css" rel="stylesheet">    
    </head>

    <body>

        <nav class="navbar navbar-toggleable-md navbar-inverse bg-inverse">

            <div class="container">

                <div class="d-flex justify-content-between">

                    <a class="navbar-brand" href="<?php echo INDEX; ?>">
                        <img src="img/raspcontrol.png" alt="rbpi" height="32" />
                    </a>
                
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                    </button>

                </div>

                <?php if ($display) : ?>

                <div class="collapse navbar-collapse" id="nav">

                    <ul class="navbar-nav mr-auto">

                        <li class="nav-item <?php is_active('home'); ?>">
                            <a class="nav-link" href="<?php echo INDEX; ?>">
                                <i class="fa fa-home" aria-hidden="true"></i> Home
                            </a>
                        </li>

                        <li class="nav-item <?php is_active('details'); ?>">
                            <a class="nav-link" href="<?php echo DETAILS; ?>">
                                <i class="fa fa-search" aria-hidden="true"></i> Details
                            </a>
                        </li>

                        <li class="nav-item <?php is_active('services'); ?>">
                            <a class="nav-link" href="<?php echo SERVICES; ?>">
                                <i class="fa fa-cog" aria-hidden="true"></i> Services
                            </a>
                        </li>

                        <li class="nav-item <?php is_active('disks'); ?>">
                            <a class="nav-link" href="<?php echo DISKS; ?>">
                                <i class="fa fa-archive" aria-hidden="true"></i> Disks
                            </a>
                        </li>

                        <li class="nav-item <?php is_active('gpio'); ?>">
                            <a class="nav-link" href="<?php echo GPIO; ?>">
                                <i class="fa fa-random" aria-hidden="true"></i> GPIO
                            </a>
                        </li>

                    </ul>

                    <ul class="navbar-nav my-2 my-lg-0">

                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo LOGOUT; ?>">
                                <i class="fa fa-power-off" aria-hidden="true"></i> Logout
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-rootaction="reboot" class="rootaction" href="#">
                                <i class="fa fa-repeat" aria-hidden="true"></i> Reboot
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-rootaction="shutdown" class="rootaction" href="#">
                                <i class="fa fa-stop" aria-hidden="true"></i> Shutdown
                            </a>
                        </li>                        

                    </ul>

                </div>

                <?php endif; ?>

            </div>

        </nav>

        <div id="login-form" title="Login to perform root actions">
            <center>
                <p class="validateTips">All form fields are required.</p>
                <form>    
                    Login with a user having root permission to perform this action<br>
                    <fieldset>
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" class="text ui-widget-content ui-corner-all" />    
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
                    </fieldset>
                </form>
            </center>
        </div>

        <div class="container">

            <div class="content p-4">

            <?php if (isset($_SESSION['message'])) : ?>

                <div class="alert alert-danger text-center mb-4">
                    <strong>Oups!</strong> <?php echo $_SESSION['message']; ?>
                </div>

                <?php unset($_SESSION['message']); ?>

            <?php endif; ?>

                <?php include $page; ?>

                <footer class="footer text-center mt-4">

                    <span>
                        Powered by <a href="https://github.com/harmon25/raspcontrol">Raspcontrol</a>.
                    </span>

                    <span>
                        Sources are available on <a href="https://github.com/harmon25/raspcontrol">Github</a>.
                    </span>                    

                </footer>

            </div>

        </div>

        <script src="js/jquery.min.js"></script>
        <script src="js/tether.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery-ui.js"></script>
        
        <?php
        // load specific scripts
        if ('details' === $_GET['page']) {
            echo '   <script src="js/details.js"></script>';
        }
        ?>

    <!-- General scripts -->
    <script>
        $(function() {

            var username = $("#username"),
                    password = $("#password"),
                    allFields = $([]).add(name).add(password),
                    tips = $(".validateTips");

            function updateTips(t) {
                tips
                        .text(t)
                        .addClass("ui-state-highlight");
                setTimeout(function() {
                    tips.removeClass("ui-state-highlight", 1500);
                }, 500);
            }

            function checkLength(o, n, min, max) {
                if (o.val().length > max || o.val().length < min) {
                    o.addClass("ui-state-error");
                    updateTips("Length of " + n + " must be between " +
                            min + " and " + max + ".");
                    return false;
                } else {
                    return true;
                }
            }

            $("#login-form").dialog({
                autoOpen: false,
                height: 400,
                width: 350,
                modal: true,
                buttons: {
                    "Login and perform action": function() {
                        var lValid = true;
                        allFields.removeClass("ui-state-error");

                        lValid = lValid && checkLength(username, "username", 1, 50);
                        lValid = lValid && checkLength(password, "password", 1, 50);

                        var action = $(this).data('rootaction');

                        if (lValid) {
                            var Url;
                            if (action == 'reboot' || action == 'shutdown')
                                Url = "?action=" + action + "&username=" + username.val() + "&password=" + password.val();
                            else if (action == 'changeservicestatus') {
                                var servicename = $(this).data('servicename');
                                Url = "?action=" + action + "&servicename=" + servicename + "&username=" + username.val() + "&password=" + password.val();
                            } else if (action == 'changepartitionstatus') {
                                var partitionname = $(this).data('partitionname');
                                var currmountpoint = $(this).data('currmountpoint');
                                var mountpoint;
                                if (currmountpoint == null || currmountpoint == "") {
                                    mountpoint = prompt("Specify mount point", "");
                                    if (mountpoint == null || mountpoint == "") {
                                        alert("You need to specify a mount point");
                                        return false;
                                    }
                                } else
                                    mountpoint = currmountpoint;
                                Url = "?action=" + action + "&partitionname=" + partitionname + "&mountpoint=" + mountpoint + "&username=" + username.val() + "&password=" + password.val();
                            }

                            $.ajax({
                                url: Url,
                                type: "GET",
                                success: function(result) {
                                    alert(result.replace(/\\n/g, "\n"));
                                    if (action == 'changeservicestatus' || action == 'changepartitionstatus') {
                                        location.reload(true);
                                    }
                                }
                            });
                            $(this).dialog("close");
                        }
                    },
                    Cancel: function() {
                        $(this).dialog("close");
                    }
                },
                close: function() {
                    allFields.val("").removeClass("ui-state-error");
                }
            });

            $(".rootaction")
                    .click(function() {
                $("#login-form")
                        .data('rootaction', $(this).attr("data-rootaction"))
                        .data('servicename', $(this).attr("data-service-name"))
                        .data('partitionname', $(this).attr("data-partition-name"))
                        .data('currmountpoint', $(this).attr("data-curr-mountpoint"))
                        .dialog("open");
            });
        });
    </script>
</body>
</html>
