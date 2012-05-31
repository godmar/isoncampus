<?php
/*
 * MIT-License.
 *
 * @author Godmar Back, 2012 <godmar@gmail.com>
 * 
 * Include this lib using require_once('isoncampuslib.php') then
 * a function 'isOnCampus' becomes available for your PhP code.
 */

/* File that contains address ranges, in canonical form. See isInRange below. */
define ('IPADDRFILENAME', "ipaddr.txt");

/* The ip address to be examined is either given via ip= or the client's address
 * is used. IPv4 only.
 */
$clientip = isset($_REQUEST['ip']) ?  $_REQUEST['ip'] : $_SERVER['REMOTE_ADDR'];

/* Convert IP address in form a.b.c.d to 32-bit number */
function ipaddr2value($ip) {
    $ipaddr = explode('.', $ip);
    return $ipaddr[0] * (1<<24) + $ipaddr[1] * (1<<16) + $ipaddr[2] * (1<<8) + $ipaddr[3];
}

/* Return true if $clientip is in any of the ranges listed in file $ipaddrfilename 
   which must contains lines of the form:

    008.015.252.000 - 008.015.252.255
    128.173.000.000 - 128.173.240.064
    128.173.240.115 - 128.173.255.255
    etc.

 */
function isInRange($ipaddrfilename, $clientipaddr) {
    $clientipvalue = ipaddr2value($clientipaddr);
    $ipranges = file_get_contents($ipaddrfilename);
    foreach(preg_split("/(\r?\n)/", $ipranges) as $line){
        if (substr($line, 0, 1) == '#') {
            continue;
        }
        $iprange = explode('-', $line);
        $lowerip = ipaddr2value($iprange[0]);
        $upperip = ipaddr2value($iprange[1]);
        if ($lowerip <= $clientipvalue && $clientipvalue <= $upperip)
            return true;
    }
    return false;
}

/* True if on campus, false otherwise. */
function isOnCampus() {
    global $clientip;
    return isInRange(IPADDRFILENAME, $clientip);
}

?>
