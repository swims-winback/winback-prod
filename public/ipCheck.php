<?php
$port = "5006";
$space = 21 - (strlen(gethostbyname("www.winback-assist.com")+strlen(",")+strlen($port)));
echo("IP:".gethostbyname("www.winback-assist.com").",".$port.str_repeat(" ", $space));