--TEST--
pcnt_lwifcontinued(status); test assumes target status is 65535 (0xffff) IFF WCONTINUED is defined
--SKIPIF--
<?php

/* SKIP this test IF pcntl extension is not loaded */

if (!extension_loaded("pcntl")) die("skip ext/pcntl required\n");

?>
--FILE--
<?php

/***********************************************************************
 * Two cases:
 * 1) If WCONTINUED is a registered constant, then
 * 1.1) Set $notHaveWcon to false,
 * 1.2) Assume target status is 65535 (0xffff), per e.g.
 *      /usr/include/x86_64-linux-gnu/bits/waitstatus.h:
 *
 *        #define __W_CONTINUED           0xffff
 *
 * 1.3) So pcntl_wifcontinued(status) either will return true for status
 #      values of 65535, or will return false for all other status values
 *
 * 2) If WCONTINUED is not a registered constant
 * 2.1) Set $notHaevWcon to true
 * 2.2) pcntl_wifcontinued(status) will always return false
 * 2.2.1) See PHP_FUNCTION(pcntl_wifcontinued) in ext/pcntl/pcntl.h
 *
 *
 * 3) For status == 65535
 *
 * 3.1) Return XOR of pcntl_wifcontinued(status) with $notHaveWcon
 *
 * 3.2) Results should be true:
 *
 * 3.2.1) If WCONTINUED is a registered constant
 * 3.2.1.1) HAVE_WCONTINUED will be a defined macro, so
 *            pcntl_wifcontinued(status) returns true
 * 3.2.1.2) $notHaveWcon will be false
 * 3.2.1.3) true XOR false => true
 *
 * 3.2.2) If WCONTINUED is not a registered constant
 * 3.2.2.1) HAVE_WCONTINUED will not be a defined macro, so
 *            pcntl_wifcontinued(status) returns false
 * 3.2.2.2) $notHaveWcon will be true
 * 3.2.2.3) false XOR true => true
 *
 *
 * 4) For status != 65535
 *
 * 4.1) Return XOR of pcntl_wifcontinued(status) with true
 *
 * 4.2) Results should be true:
 *
 * 4.2.1) pcntl_wifcontinued(status) returns false
 * 4.2.2) false XOR true => true
 *
 **********************************************************************/

/* Set $notHaveWcon */
$notHaveWcon = !array_key_exists("WCONTINUED",get_defined_constants());

/* Create do_xor function to dump bool(true) or bool(false) based on XOR
 * of pcntl_wifcontinued(status) in bool argument
 */
function do_xor($iStatus,$bool2xor) {
    var_dump(pcntl_wifcontinued($iStatus) xor $bool2xor);
}

/* Perform XORs on several values, as described above */
do_xor(0xffff,$notHaveWcon);
do_xor(0xfffe,true);
do_xor(0x10000,true);
do_xor(0,true);
do_xor(65534,true);
do_xor(65536,true);
do_xor(65535,$notHaveWcon);

?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
