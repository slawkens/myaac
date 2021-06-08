<?php
/**
 * 405 error page
 *
 * @package   MyAAC
 * @author    Slawkens <slawkens@gmail.com>
 * @copyright 2021 MyAAC
 * @link      https://my-aac.org
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = '405 Method Not Allowed';

header('HTTP/1.0 405 Method Not Allowed');
?>
<h1>Method not allowed</h1>
<p>The requested method: <?php echo $_SERVER['REQUEST_METHOD']; ?> for URL <?php echo $_SERVER['REQUEST_URI']; ?> was not found on this server.</p>
