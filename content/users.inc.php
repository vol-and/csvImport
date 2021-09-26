<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user.class.php';
session_start();

$userClass = new User($mysqli);
$userArray = $userClass->getAllUsers();
$table_data = null;
foreach ($userArray as $key => $user) {
    $disabled = (isset($user['welcome_mail'])) ? 'disabled' : '';
    if ($user['active'] == 0) {
        $active = 'disabled';
        $buttonName = 'Activate User';
        $activationClass = 'activate';
        $icon = '<i class="bi bi-person-plus-fill fs-6 me-3"></i>';
    } else {
        $active = '';
        $buttonName = 'Deactivate User';
        $activationClass = 'deactivate';
        $icon = '<i class="bi bi-person-dash-fill fs-6 me-3"></i>';
    }

    $table_data .= '<tr>';

    $table_data .= '<td class="nname" data-nname = "' . $user['nachname'] . '">
                <input type="text" name="nname" value="' . $user['nachname'] . '"/></td>';

    $table_data .= '<td class="vname" data-vname = "' . $user['vorname'] . '">
                <input type="text" name="vname" value="' . $user['vorname'] . '"/></td>';

    $table_data .= '<td class="email" data-email = "' . $user['email'] . '">
                <input type="text" name="email" value="' . $user['email'] . '"/></td>';

    $table_data .= '<td class="company" data-company = "' . $user['company'] . '">
                <input type="text" name="company" value="' . $user['company'] . '"/></td>';


    $table_data .= '<td style="width: 7%"><form class="float-end" method="post">
                        <input type="hidden" name="id" value="' . $user['id'] . '"/>
                        <button class="btn btn-warning btn-sm update" 
                        name="update" type="button">
                        <i class="bi bi-arrow-clockwise"></i>Update</button></form></td>';

    $table_data .= '<td style="width: 8%"><form class="float-end" method="post">
                        <input type="hidden" name="id" value="' . $user['id'] . '"/>
                        <button class="btn btn-success btn-sm ' . $disabled . ' sendInvite ' . $active . '" 
                        name="sendInvite" type="button">
                        <i class="bi bi-reply fs-6 me-3"></i>Send Invite</button></form></td>';

    $table_data .= '<td style="width: 9%"><form class="float-end" method="post">
                        <input type="hidden" name="id" value="' . $user['id'] . '"/>
                        <button class="btn btn-info btn-sm resendInvite ' . $active . '" name="resendInvite" type="button">
                        <i class="bi bi-reply-all-fill fs-6 me-3"></i> Resend Invite</button></form></td>';

    $table_data .= '<td style="width: 10%"><form class="float-end" method="post">
                        <input type="hidden" name="id" value="' . $user['id'] . '"/>
                        <button class="btn btn-outline-danger btn-sm ' . $activationClass . '" type="button">'
                                    . $icon . $buttonName . '</button></form></td>';

    $table_data .= '<td style="width: 8%"><form class="float-end" method="post">
                        <input type="hidden" name="id" value="' . $user['id'] . '"/>
                        <button class="btn btn-danger btn-sm delete" name="delete" type="button">
                        <i class="bi bi-person-x fs-6 me-3"></i>Delete user</button></form></td>';
    $table_data .= '</tr>';
}
?>
<div class="row">
    <?php
    if ( ! empty($_SESSION['response'])) {
        echo $_SESSION['response'];
        unset($_SESSION['response']);
    }
    ?>
</div>
<div class="justify-content-between row">
    <div class="col-6 my-4">
        <form action="../handler/usersUploadHandler.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>
                    <h5>
                        Upload your CSV File
                    </h5>
                    <!--        <div class="col-4 pl-0">-->
                    <!--            <a href="-->
                    <?php //echo $csv_example_dlpath ?><!--" style="text-decoration: none"-->
                    <!--               target="_blank" download="user_import_template.csv">-->
                    <!--                <h6 class="hover-text">-->
                    <!--                    <i class="fa fa-download mr-2"></i>Click to download CSV example-->
                    <!--                </h6>-->
                    <!--            </a>-->
                    <!--        </div>-->
                </legend>
                <div class="row">
                    <div class="col-4">
                        <input id="file" type="file" name="file" accept=".csv"/>
                    </div>
                    <div class="col-4">
                        <button class='btn btn-outline-dark' type="submit" value="Upload" name="upload-file">
                            <i class="bi bi-upload me-3"></i> Upload
                        </button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
    <div class="col-1 float-end">
        <button class="btn btn-outline-danger reload float-end">
            <i class="bi bi-arrow-repeat"></i>
            Refresh
        </button>
    </div>
</div>

<div class="justify-content-between row">
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead>
            <tr>
                <th class="text-center text-black-50">Name</th>
                <th class="text-center text-black-50">Surname</th>
                <th class="text-center text-black-50">E-Mail</th>
                <th class="text-center text-black-50">Company</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php echo (isset($table_data)) ? $table_data : '' ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "onCloseClick" : function() {location.reload()},
            "onHidden" : function() {location.reload()},
            "preventOpenDuplicates": true
        }

        function ajaxCall(obj) {
            let sendData = JSON.stringify(obj);
            $.ajax({
                type: "post",
                url: '../handler/userHandler.php',
                data: {data: sendData},
                success: function (data) {
                    let obj = JSON.parse(data);
                    if (obj.result === 'success') {
                        if (obj.hasOwnProperty('email')) {
                            toastr.success(obj.email);
                        } else {
                            toastr.success('Updated!');
                        }
                    } else if (obj.result === 'error') {
                        toastr.error("There is some error occurred. Please try again.");
                    }
                },
                error: function (data) {
                    alert("Couldn't send ajax call");
                    console.log(data)
                }
            })
        }

        $('.update').click(function (e) {
            e.preventDefault();
            let nname = $(this).parents('tr:first').find('td input[name=nname]').val();
            let vname = $(this).parents('tr:first').find('td input[name=vname]').val();
            let email = $(this).parents('tr:first').find('td input[name=email]').val();
            let company = $(this).parents('tr:first').find('td input[name=company]').val();

            let obj = {
                'activity': 'update',
                'nname': nname,
                'vname': vname,
                'email': email,
                'company': company,
                'uid': $(this).parents('tr:first').find('td input[name=id]').val()
            }
            ajaxCall(obj);
        })

        $('.sendInvite').click(function (e) {
            e.preventDefault();
            let obj = {
                'activity': 'sendinvite',
                'uid': $(this).parents('tr:first').find('td input[name=id]').val()
            }
            $(this).parents('tr:first').find('td button[name=sendInvite]').addClass('disabled');
            console.log(obj)
            ajaxCall(obj);
        })

        $('.resendInvite').click(function (e) {
            e.preventDefault();
            let obj = {
                'activity': 'resendinvite',
                'uid': $(this).parents('tr:first').find('td input[name=id]').val()
            }
            ajaxCall(obj);
        })

        $('.deactivate').click(function (e) {
            e.preventDefault();
            let obj = {
                'activity': 'deactivate',
                'uid': $(this).parents('tr:first').find('td input[name=id]').val()
            }
            $(this).parents('tr:first').find('td button[name=sendInvite]').addClass('disabled');
            $(this).parents('tr:first').find('td button[name=resendInvite]').addClass('disabled');
            $(this).removeClass('deactivate');
            $(this).addClass('activate');
            $(this).html('');
            $(this).append('<i class="bi bi-person-plus-fill fs-6 me-3"></i>Activate User');
            ajaxCall(obj);
        })

        $('.activate').click(function (e) {
            e.preventDefault();
            let obj = {
                'activity': 'activate',
                'uid': $(this).parents('tr:first').find('td input[name=id]').val()
            }
            console.log(obj)

            $(this).parents('tr:first').find('td button[name=sendInvite]').removeClass('disabled');
            $(this).parents('tr:first').find('td button[name=resendInvite]').removeClass('disabled');
            $(this).removeClass('activate');
            $(this).addClass('deactivate');
            $(this).html('');
            $(this).append('<i class="bi bi-person-dash-fill fs-6 me-3"></i>Deactivate User');
            ajaxCall(obj);
        })

        $('.delete').click(function (e) {
            e.preventDefault();
            let del_id = $(this).parents('tr:first').find('td input[name=id]').val();
            let nname = $(this).parents('tr:first').find('td.nname').data('nname');
            let vname = $(this).parents('tr:first').find('td.vname').data('vname');

            if (confirm(nname + ' ' + vname + ' löschen?')) {
                let obj = {
                    'activity': 'delete',
                    'uid': del_id
                }
                let sendData = JSON.stringify(obj);
                $.ajax({
                    type: "POST",
                    url: '../handler/userHandler.php',
                    data: {data: sendData},
                    success: function () {
                    },
                    error: function () {
                        alert('error');
                    }
                });
                $(this).parents('tr:first').animate({backgroundColor: "#fbc7c7"}, "fast")
                    .animate({opacity: "hide"}, "slow");
            }
            return false;
        })

        $('.reload').click(function (e) {
            e.preventDefault();
            location.reload();
        })
    });
</script>