Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead("C:\Users\Benaya\Downloads\MAKALAH WEB PROGRAMMING II.docx")
$entry = $zip.GetEntry("word/document.xml")
$stream = $entry.Open()
$reader = New-Object System.IO.StreamReader($stream)
$xmlText = $reader.ReadToEnd()
$reader.Close()
$stream.Close()
$zip.Dispose()
$cleanText = $xmlText -replace '<[^>]+>', ' ' -replace '\s+', ' '
Set-Content -Path "C:\Users\Benaya\Downloads\makalah_text.txt" -Value $cleanText
