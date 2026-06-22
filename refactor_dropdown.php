<?php
$files = glob(__DIR__ . '/resources/views/*.blade.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    // Regex matches <div id="settingsDropdown" ... up to the closing </div> of the dropdown
    // The dropdown has an opening div, a div for user info, a div for links, and then it closes.
    // Let's use a non-greedy match that looks for "Keluar" followed by </a></div></div>
    $pattern = '/<div id="settingsDropdown".*?Keluar(?: Akun)?<\/a>\s*<\/div>\s*<\/div>/s';
    
    $new_content = preg_replace($pattern, "@include('components.settings-dropdown')", $content);
    if ($new_content !== null && $new_content !== $content) {
        file_put_contents($file, $new_content);
        echo "Updated: " . basename($file) . "\n";
    }
}
echo "Done.\n";
