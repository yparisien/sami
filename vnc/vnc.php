<?php
/**
 * Launch the vnc viwer
 */
exec('vnc-viewer.exe -config trasys.vnc > /dev/null 2>&1 & ');
echo json_encode(array('status' => 'started'));