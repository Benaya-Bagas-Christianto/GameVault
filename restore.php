<?php
$content = file_get_contents('d:\Laragon\laragon\www\gamevault\storage\framework\views\ba8109045b7460ad1c0985fd1ea9fcde.php');

$content = preg_replace('/<\?php \/\*\*PATH .* ENDPATH\*\*\/ \?>/', '', $content);
$content = preg_replace('/<\?php echo e\((.*?)\); \?>/s', '{{ $1 }}', $content);
$content = preg_replace('/<\?php echo \((.*?)\); \?>/s', '{!! $1 !!}', $content);
$content = preg_replace('/<\?php if\((.*?)\): \?>/s', '@if($1)', $content);
$content = preg_replace('/<\?php elseif\((.*?)\): \?>/s', '@elseif($1)', $content);
$content = preg_replace('/<\?php else: \?>/s', '@else', $content);
$content = preg_replace('/<\?php endif; \?>/s', '@endif', $content);
$content = preg_replace('/<\?php foreach\((.*?) as (.*?)\): \?>/s', '@foreach($1 as $2)', $content);
$content = preg_replace('/<\?php endforeach; \?>/s', '@endforeach', $content);
$content = preg_replace('/<\?php if\(auth\(\)->guard\(\)->check\(\)\): \?>/s', '@auth', $content);
$content = preg_replace('/<\?php if\(auth\(\)->guard\(\)->guest\(\)\): \?>/s', '@guest', $content);
$content = preg_replace('/<\?php echo \$__env->make\((.*?), \\\Illuminate\\\Support\\\Arr::except\(get_defined_vars\(\), \[\'__data\', \'__path\'\]\)\)->render\(\); \?>/s', '@include($1)', $content);

file_put_contents('d:\Laragon\laragon\www\gamevault\resources\views\index.blade.php', $content);
echo "Restored from compiled view!";
