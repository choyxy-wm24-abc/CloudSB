<?php
if (extension_loaded('gd')) {
    $gd_info = gd_info();
    echo "GD Version: " . $gd_info['GD Version'] . "\n";
    echo "FreeType Support: " . ($gd_info['FreeType Support'] ? 'Yes' : 'No') . "\n";
    echo "JPEG Support: " . ($gd_info['JPEG Support'] ? 'Yes' : 'No') . "\n";
    echo "PNG Support: " . ($gd_info['PNG Support'] ? 'Yes' : 'No') . "\n";
    echo "GIF Support: " . ($gd_info['GIF Read Support'] ? 'Yes' : 'No') . "\n";
} else {
    echo "GD extension is not loaded\n";
}
?>