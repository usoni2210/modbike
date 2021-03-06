<?php
    require_once "../includes/config.php";
    include "../includes/Connection.php";
    session_start();
?>
<html lang="en">
    <head>
        <title><?php echo WEBSITE_NAME." - Admin Dashboard"; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="../images/logo.png" type="image/png" sizes="32x32">

        <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="../assets/bootstrap-social/bootstrap-social.css" rel="stylesheet" type="text/css">
        <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="../assets/animate/animate.min.css" rel="stylesheet" type="text/css">
        <link href="assets/main.css" rel="stylesheet" type="text/css">

        <style type="text/css">
            .dropdown-toggle::after{
                display: none; !important;
            }

            .form-group {
                height: 40px;
            }

            fieldset.scheduler-border {
                border: 3px groove #ddd !important;
                padding: 0 1.4em 1.4em 1.4em !important;
                margin: 0 0 1.5em 0 !important;
                -webkit-box-shadow:  0 0 0 0 #000;
                box-shadow:  0 0 0 0 #000;
            }

            legend.scheduler-border {
                width:inherit; /* Or auto */
                padding:0 10px; /* To give a bit of padding on the left and right */
                border-bottom:none;
            }
        </style>
    </head>
    <body>
        <?php include "includes/leftbar.php"; ?>

        <div id="right-panel" class="right-panel">
            <?php include "includes/header.php"; ?>
            <div class="container-fluid pl-5 pr-5 pt-2">
                <?php include "functions/responses.php"; ?>
                <form style="border:3px solid black" method="post" action="functions/saveShopSales.php">
                    <div class="container-fluid text-center bg-black text-white mb-3 p-2">
                        Shop Sales Part
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-4 form-group">
                                <select name="shop" class="form-control" id="shop">
                                    <?php
                                        $conn = new Connection();
                                        /** @var Shop[] $shoppes */
                                        $shoppes = $conn->getAllShop();
                                        if($shoppes != null){
                                            foreach($shoppes as $shop){
                                                echo "<option value='".$shop->getId()."'>".$shop->getName()."</option>";
                                            }
                                            echo "<option value='' selected>-- Select Shop --</option>";
                                        } else {
                                            echo "<option>Add Shop First</option>";
                                        }
                                    ?>
                                </select>
                                <label for="shop"></label>
                            </div>
                            <div class="col-lg-4 form-group">
                                <select name="part" class="form-control" id="part">
                                    <option value="">-- Select Shop First --</option>
                                </select>
                                <label for="part"></label>
                            </div>
                            <div class="col-lg-4 form-group text-center">
                                <input type="hidden" name="q" value="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <button type="submit" class="btn btn-dark bg-black w-50" onclick="return validate()">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
                <br><br>

                <?php
                    if($conn->isShopSales()) {
                        ?>
                        <div>
                        <fieldset class="scheduler-border">
                            <legend class="scheduler-border font-weight-bold">shop Have Part</legend>
                            <table class="table table-responsive-sm table-hover mt-3 bg-white" cellspacing="5" cellpadding="10">
                                <thead>
                                <tr class="text-light bg-black text-center">
                                    <th>No</th>
                                    <th>Shop</th>
                                    <th>Parts</th>
                                    <th>Operation</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $shoppes = $conn->getAllshop();
                                    $count = 1;
                                    foreach($shoppes as $shop) {
                                        $flag = true; ?>
                                        <tr class="text-center">
                                        <td class="align-middle"><?php echo $count++; ?></td>
                                        <td class="align-middle"><?php echo $shop->getName(); ?></td>
                                        <td>
                                            <?php
                                                $parts = $conn->getSellingPart($shop->getId());
                                                if($parts != null){
                                                    echo "<div>";
                                                    foreach ($parts as $part){
                                                        echo "<spam>".$part['cat_name'] . " - " . $part['name'] . "</spam><br>";
                                                    }
                                                    echo "</div>";
                                                } else {
                                                    echo "<div class='text-grey'>None</div>";
                                                    $flag = false;
                                                }
                                            ?>
                                        </td>
                                        <td class="align-middle">
                                            <div class="row justify-content-center text-grey">
                                                <!--div class="col-md-3">
                                                    <a href="">
                                                        <i class="fas fa-edit text-success" title="Edit"></i>
                                                    </a>
                                                </div-->
                                                <div class="col-md-3">
                                                    <a <?php if($flag){ echo "href=\"functions/deleteShopSales.php?q=".$_SERVER['PHP_SELF']."&id=".$shop->getId()."\"";} ?>>
                                                        <i class="fas fa-trash-alt<?php if($flag){ echo " text-danger"; } ?>" title="Delete"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        </tr><?php
                                    }
                                ?>
                                </tbody>
                            </table>
                        </fieldset>
                        </div><?php
                    }
                ?>
            </div>
        </div>

        <script src="../assets/jquery.min.js" type="text/javascript"></script>
        <script src="../assets/popper/popper.min.js" type="text/javascript"></script>
        <script src="../assets/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../assets/fontawesome/js/fontawesome.min.js" type="text/javascript"></script>
        <script src="assets/main.js" type="text/javascript"></script>
        <script type="text/javascript" language="JavaScript1.5">
            $(document).ready(function($) {
                let target = 'part';
                let select = 'shop';
                let initial_target = '<option value="">-- Select Bike First --</option>';

                $('#'+select).change(function(e) {
                    let bid = $(this).val();
                    $('#'+target).html('<option value="">Loading...</option>');

                    if (bid === "") {
                        $('#'+target).html(initial_target);
                    } else {
                        $.ajax({url: 'functions/listPartShop.php?id='+bid,
                            success: function(output) {
                                //alert(output);
                                $('#'+target).html(output);
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                alert(xhr.status + " "+ thrownError);
                            }
                        });
                    }
                });
            });
            function validate(){
                let shop = document.getElementById("shop");
                let part = document.getElementById("part");
                if (shop.value === "") {
                    alert("Please select a Shop!");
                    return false;
                }
                else if (part.value === "") {
                    alert("Please select a Part!");
                    return false;
                }
                return true;
            }

        </script>
    </body>
</html>

