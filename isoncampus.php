<?php
/*
 * Web service to provide information whether a user is on campus or off campus.
 * Run for instructions.
 *
 * MIT-License.
 *
 * @author Godmar Back, 2012 <godmar@gmail.com>
 */

require_once('isoncampuslib.php');

/* True if on campus, false otherwise. */
$isoncampus = isOnCampus();

/* 
 * If fmt=javascript given, output a JavaScript variable and set it to
 * true or false.
 * varname can be used to describe the name of the variable.
 */
if (@$_REQUEST['fmt'] == 'javascript') {
    $varname = isset($_REQUEST['varname']) ? $_REQUEST['varname'] : 'onCampus';
    header("Content-Type: text/javascript");
    echo 'var ' . $varname . ' = ' . ($isoncampus ? 'true' : 'false') . ";\n";
    exit;
}

/* 
 * If fmt=json given, output JSON; if callback is set also, JSON-P.
 */
if (@$_REQUEST['fmt'] == 'json') {
    $callback = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : null;
    
    $reply = new stdClass();
    $reply->ipaddr = $clientip;
    $reply->oncampus = $isoncampus;
    if ($callback) {
        header("Content-Type: text/javascript");
        echo $callback . '(' . json_encode($reply) . ')';
    } else {
        header("Content-Type: application/json");
        echo json_encode($reply);
    }
    exit;
}

$myUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
?>
<title>Is OnCampus Service</title>

<body>
<p>
Your IP address is <?php echo $_SERVER['REMOTE_ADDR']; ?>. 
<?
/* Otherwise, output text. */
if ($isoncampus) {
    echo 'You are on campus.';
} else {
    echo 'You are off campus.';
}
?>
</p>
<p>
There are multiple ways of using this service.
Click on the links below to see what output is produced by each.
<p>For inclusion using a &lt;script&gt; tag so that a JavaScript variable is set:
<?
    $jsService = $myUrl . '?fmt=javascript';
    echo '<a href="' . $jsService . '">' . $jsService . '</a>';
?>
</p>
<p>You would use
<tt>
    &lt;script type="text/javascript" src="<? echo $jsService; ?>"&gt;&lt;/script&gt;
</tt>
</p>
<p>Same as above, but you can choose the name of the variable:
<?
    $jsService = $myUrl . '?fmt=javascript&varname=iChooseTheVariableName';
    echo '<a href="' . $jsService . '">' . $jsService . '</a>';
?>
</p>
<p>As a JSON service
<?
    $jsService = $myUrl . '?fmt=json';
    echo '<a href="' . $jsService . '">' . $jsService . '</a>';
?>
</p>
<p>As a JSON-P service
<?
    $jsService = $myUrl . '?fmt=json&callback=myCallback';
    echo '<a href="' . $jsService . '">' . $jsService . '</a>';
?>
</p>
The code can also be included server-side from other PhP scripts. Consult the PhP code for details.
To keep the IP ranges up-to-date, update 'ipaddr.txt' accordingly.
</body>
