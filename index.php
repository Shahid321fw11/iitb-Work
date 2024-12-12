<?php //error_reporting(0);
include('header.php');
include('dbconnect.php');
?>

<?php
if (isset($_POST['ldap_login'])) {
    $uname = mysqli_real_escape_string($link1, $_POST['uid']);
    $pass = $_POST['pass'];
    $success = "false";
    $q = mysqli_query($link1, "select * from login where email = '$uname'");
    if (empty($_SESSION['captcha_code']) || strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code']) != 0) {
        $msg = "<span style='color:red'>The Validation code does not match!</span>"; // Captcha verification is incorrect.		
    } else { // Captcha verification is Correct. Final Code Execute here!		
        $success = "true";
    }

    if (mysqli_num_rows($q) <= 0) {
        echo "<div class=error style=\"text-align:center;padding:5px;\">Invalid Email</div>";
    } else {
        $row = mysqli_fetch_assoc($q);
        if (md5($pass) != $row['password']) {
            echo "<div class=error style=\"text-align:center;padding:5px;\">Invalid Password</div>";
        } else {

            if ((strtotime($row['expiry_date']) < strtotime(date('m/d/Y'))) && ($row['is_admin'] == 0 && $row['expiry_date'] != "")) {
                echo "<div class=error style='text-align:center;'>Your Account Is Expired</div>";
            } else {
                if ($success == "true") {
                    session_start();
                    $_SESSION['uname'] = $row["fname"] . " " . $row["lname"];
                    $_SESSION['ldap'] = $row["memberid"];
                    $_SESSION['is_admin'] = $row['is_admin'];

                    header('Location:daily_reporting_entry.php');
                    exit();
                }
            } //exp dt if


        }
    }
}
?>

<script type='text/javascript'>
    function refreshCaptcha() {
        var img = document.images['captchaimg'];
        img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;
    }
</script>

<div id="main">
    <div class="midContent_about">



        <div id="midContent_bottom" style=" height:auto;">
            <!--<div id="about_left" style="height:auto;">
      <div class="about_tab">You are not Loged In</div>
                
      <div id="main-menu">
      
            <?php //include("includes/menu-guest.php"); 
            ?>
      
      </div>
    </div>-->



            <div style="padding-top:20px;">
                <form name="login" method="post" class="formular">
                    <table width="40%" align="center" border="0" cellpadding="0" cellspacing="0" class="manage_form">

                        <tr class="bluestrip">
                            <td colspan="2" align="center">Login</td>
                        </tr>

                        <tr>
                            <td colspan="2" height="5"></td>
                        </tr>

                        <tr>
                            <td width="40%">E-Mail Id<span class="error">*</span></td>
                            <td width="60%">
                                <input name="uid" type="text" class="paddingtop" value="" />
                            </td>
                        </tr>


                        <tr>
                            <td></td>
                            <td id="alt_email" class="error"></td>
                        </tr>

                        <tr>
                            <td>Password<span class="error">*</span></td>
                            <td>
                                <input name="pass" type="password" value="" />
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td id="alt_password" class="error"></td>
                        </tr>


                        <tr>
                            <td> Validation code<span class="error">*</span></td>
                            <td><img src="captcha.php?rand=<?php echo rand(); ?>" id='captchaimg'><br>
                                <label for='message'>Enter the code above here </label>
                                <br>
                                <input id="captcha_code" name="captcha_code" type="text" />
                                <br>
                                Can't read the image? click <a href='javascript: refreshCaptcha();'>here</a> to refresh.
                            </td>
                        </tr>
                        <?php if (isset($msg)) { ?>
                            <tr>
                                <td></td>
                                <td><?php echo $msg; ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td id="alt_captcha_code" class="error"></td>
                        </tr>


                        <tr>
                            <td colspan="2" align="center">
                                <input name="ldap_login" style=" width:100px; height:25px; font-weight:bold;" type="submit"
                                    value="Login >>" onclick="return validate_form();" />
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" align="center" style="font-family:Cambria; font-size:14px;">
                                <hr style="color:#2E64AE;" />
                                Please use the same login id and password as in the slotbooking module
                            </td>
                        </tr>
                    </table>
                </form>

                <div style="height:20px;"></div>
            </div>


        </div>

        <div style="clear:both;"></div>


    </div>
</div>
<?php include('footer.php'); ?>