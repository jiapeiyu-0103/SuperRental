<html>

<style>
    body {
        background-image: url("img.jpg");
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-position: center;
        background-size: cover;
    }
    .textSize {
        width: 150px;
        height: 30px;
        font-size: 15px;
    }
    .head {
        background-color: whitesmoke;
        opacity: 0.75;
        text-align: center;

    }
    .header {

        padding: 10px;
        text-align: center;

    }
    .fromBox {

        margin: 10px auto;
    }
    .button {
        border-radius: 8px;
        background-color: hsla(30,100%,50%,0.5);
        border: none;
        color: #FFFFFF;
        text-align: center;
        font-size: 20px;
        padding: 8px;
        display: block;
        width: 250px;
        height: 50px;
        transition: all 0.5s;
        cursor: pointer;
        margin: 30px auto;
    }
    .button span {
        cursor: pointer;
        display: inline-block;
        position: relative;
        transition: 0.5s;
    }
    .button span:after {
        content: '\00bb';
        position: absolute;
        opacity: 0;
        top: 0;
        right: -20px;
        transition: 0.5s;
    }
    .button:hover span {
        padding-right: 25px;
    }

    .button:hover span:after {
        opacity: 1;
        right: 0;
    }
    .box {
        background-color: whitesmoke;
        width: 600px;
        padding: 10px;
        margin: 20px auto;
    }
    .headerText {
        background-color: hsla(30,100%,50%,0.5);
        color: whitesmoke;
        letter-spacing: 3px;
    }
    .echo {
        color: whitesmoke;
    }
</style>

<body>
<div class="header">
    <div class = "head">
        <h1>Manage the Vehicle of the Company</h1>
    </div>>

    <form action = "manageVehicle.php" method="POST">
        <fieldset class="box">
            <legend class="headerText">Add New Vehicle</legend>
            Vehicle License: <input class="textSize" type="text" name="vlicense"> <br />
            Vehicle Type: <input class="textSize" type="text" name="vtName"> <br />
            Location: <input class="textSize" type="text" name="Location"> <br />
            City: <input class="textSize" type="text" name="City"> <br />
            Status: <input class="textSize" type="text" name="Status"> <br />
            <input type="hidden" id="insertV" name="insertV">
            <button class="button" name = "insert" style="vertical-align:middle"><span>Add</span></button>
        </fieldset>
    </form>

    <form action = "manageVehicle.php" method="POST">
        <fieldset class="box">
            <legend class="headerText">Delete a Vehicle</legend>
            Vehicle License: <input class="textSize" type="text" name="v_license"> <br />
            <input type="hidden" id="dropV" name="dropV">
            <button class="button" name = "delete" style="vertical-align:middle"><span>Delete</span></button>
        </fieldset>
    </form>

    <form action = "manageVehicle.php" method="POST">
        <fieldset class="box">
            <legend class="headerText">Update a Vehicle:</legend>
            Vehicle License to Update: <input class="textSize" type="text" name="update_vlicense"> <br />
            <br><br><label for="From">choose an attribute to update</label><br />
            <div class="fromBox">
                <select name = "attribute">
                    <option value="make"> make </option>
                    <option value="model"> model </option>
                    <option value="year"> year </option>
                    <option value="color"> color </option>
                    <option value="odometer"> odometer </option>
                    <option value="vtname"> vehicle type name </option>
                    <option value="location"> location </option>
                    <option value="city"> city </option>
                    <option value="status"> status </option>
                </select>
            </div>
            <br>New value for this attribute: <input class="textSize" type="text" name="new_attribute"> <br />
            <input type="hidden" id="updateV" name="updateV">
            <button class="button" name = "update" style="vertical-align:middle"><span>Update</span></button>
        </fieldset>
    </form>
    <form action = "clerk.php" method="GET">
        <button class="button" name="back" style="vertical-align: middle"><span>BACK</span></button>
    </form>
</div>


<?php
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;
    // echo "<br>begin to read the sql!!!!!!!!!!!!!!!!!!!!!!<br>";

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

//            echo "<br>begin to execute the sql!!!!!!!!!!!!!!!!!!!!!!<br>";
//            echo "<br> here's the sql: " . $cmdstr ."<br>";
    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        //  echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }
//            echo "<br>after executing the sql!!!!!!!!!!!!!!!!!!!!!!<br>";
//            echo "<br> here's the result: " . var_dump(oci_fetch_row($statement)) ."<br>";
    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
In this case you don't need to create the statement several times. Bound variables cause a statement to only be
parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
See the sample code below for how this function is used */

    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            OCIBindByName($statement, $bind, $val);
            unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_yuxinwan", "a23838436", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}


function insertRequest() {
    global $db_conn;
    $newV = $_POST["vlicense"];
    $newVT = $_POST["vtName"];
    $newL = $_POST["Location"];
    $newC = $_POST["City"];
    $newS = $_POST["Status"];
    $result = executePlainSQL("SELECT * FROM vehicles WHERE vlicense = '$newV'");
    $row = oci_fetch_row($result);

    if ($row != false) {
        echo "<p style= 'color: whitesmoke; text-align: center'>This vehicle type already exists! No need to add!</p>";
    } else {
        executePlainSQL("INSERT INTO vehicles VALUES ('$newV', '', '', 0, '', 0, '$newVT', '$newL', '$newC', '$newS')");
        echo "<p style= 'color: whitesmoke; text-align: center'>Vehicle " . $newV . " added!</p>";
    }
    OCICommit($db_conn);

}

function dropRequest() {
    global $db_conn;
    $deleteV = $_POST["v_license"];
    $result = executePlainSQL("SELECT * FROM vehicles WHERE vlicense = '$deleteV'");
    $row = oci_fetch_row($result);

    if ($row != false) {
        executePlainSQL("DELETE FROM vehicles WHERE vlicense = '$deleteV'");
        echo "<p style= 'color: whitesmoke; text-align: center'>Vehicle " . $deleteV . " deleted!</p>";
    } else {
        echo "<p style= 'color: whitesmoke; text-align: center'>This vehicle " . $deleteV . " doesn't exist! Can't delete!</p>";
    }
    OCICommit($db_conn);
}

function updateRequest() {
    global $db_conn;
    $VLicense = $_POST["update_vlicense"];
    $att = $_POST["attribute"];
    $newsVal = $_POST["new_attribute"];
    $result = executePlainSQL("SELECT * FROM vehicles WHERE vlicense = '$VLicense'");
    $row = oci_fetch_row($result);

    if ($row != false) {
        executePlainSQL("UPDATE vehicles SET $att = '$newsVal' WHERE vlicense = '$VLicense'");
        echo "<p style= 'color: whitesmoke; text-align: center'>Vehicle " . $VLicense . " updated!</p>";
    } else {
        echo "<p style= 'color: whitesmoke; text-align: center'>This vehicle " . $VLicense . " doesn't exist! Can't update a non-exist attribute!</p>";
    }
    OCICommit($db_conn);
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

// HANDLE ALL GET ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handleManageRequest() {
    if (connectToDB()) {
        if (array_key_exists('insertV', $_POST)) {
            insertRequest();
        } else if (array_key_exists('dropV', $_POST)) {
            dropRequest();
        } else if (array_key_exists('updateV', $_POST)) {
            updateRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['insert']) || isset($_POST['delete']) || isset($_POST['update'])) {
    handleManageRequest();
}
?>
</body>
</html>

