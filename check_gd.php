<?php
if (extension_loaded('gd')) {
    echo "✅ GD extension is loaded successfully!<br>";
    echo "<h3>GD Info:</h3>";
    print_r(gd_info());
} else {
    echo "❌ GD extension is still NOT loaded";
}
?>