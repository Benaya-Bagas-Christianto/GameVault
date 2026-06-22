<?php
$content = file_get_contents('d:\Laragon\laragon\www\gamevault\resources\views\kategori.blade.php');
preg_match('/@if\(\$kat\[0\] == \'Action\'\).*?@endif/s', $content, $matches);
if (!empty($matches)) {
    $svgBlock = $matches[0];
    // Replace $kat[0] with $genre
    $svgBlock = str_replace('$kat[0]', '$genre', $svgBlock);
    
    // Replace classes
    $svgBlock = str_replace('text-[#a78bfa]', 'text-white', $svgBlock);
    $svgBlock = str_replace('text-gray-400 opacity-60', 'text-white/80', $svgBlock);
    $svgBlock = str_replace('w-10 h-10', 'w-8 h-8', $svgBlock);

    // Add fallback for others
    $fallback = "
                        @else
                            <span class='text-2xl'>{{ \$fallbackIcon ?? '' }}</span>
                        @endif";
    // Carefully replace the last @endif
    $svgBlock = preg_replace('/@endif\s*$/', $fallback, $svgBlock);

    if (!is_dir('d:\Laragon\laragon\www\gamevault\resources\views\components')) {
        mkdir('d:\Laragon\laragon\www\gamevault\resources\views\components', 0777, true);
    }
    file_put_contents('d:\Laragon\laragon\www\gamevault\resources\views\components\genre-svg.blade.php', $svgBlock);
    echo "Component created successfully.\n";
} else {
    echo "Failed to find SVG block.\n";
}
