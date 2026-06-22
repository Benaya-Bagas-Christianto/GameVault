<?php
$files = glob(__DIR__ . '/resources/views/*.blade.php');
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Pattern to match toggleSettings function and the click listener for settingsDropdown
    $pattern = '/\s*\/\/ Mengembalikan fungsi klik profil\s*function toggleSettings\(\).*?\}\s*document\.addEventListener\(\'click\', function\(event\) \{\s*const dropdown = document\.getElementById\(\'settingsDropdown\'\).*?\}\s*\}\);\s*/s';
    
    // A more lenient pattern just in case
    $pattern2 = '/\s*function toggleSettings\(\).*?\}\s*document\.addEventListener\(\'click\', function\(event\) \{\s*const dropdown = document\.getElementById\(\'settingsDropdown\'\).*?\}\s*\}\);\s*/s';
    
    $new_content = preg_replace($pattern, "\n", $content);
    if ($new_content === $content) {
        $new_content = preg_replace($pattern2, "\n", $content);
    }
    
    if ($new_content !== null && $new_content !== $content) {
        file_put_contents($file, $new_content);
        echo "Removed toggleSettings from: " . basename($file) . "\n";
    }
}
echo "Done.\n";
