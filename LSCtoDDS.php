<?php

function q($arrByte) {
    $length = count($arrByte);
    for ($i = 0; $i < $length; $i++) {
        $byte = $arrByte[$i];
        $temp = ($byte & 0x7) << 0x5;
        $byte >>= 0x3;
        $byte |= $temp;
        $byte ^= 0xff;
        $arrByte[$i] = $byte;
    }
    return $arrByte;
}

if (count($argv) < 3) {
    echo "Berikan opsi awalan (-d atau -s) dan jalur folder sebagai argumen baris perintah.\n";
    exit;
}

$pref = $argv[1];
$dir = $argv[2];

if ($pref === '-d') {
    $match = '/\.lsc$/';
    $files = array_diff(scandir($dir), array('..', '.'));

    foreach ($files as $file) {
        if (preg_match($match, $file)) {
            try {
                $data = file_get_contents($dir . '/' . $file);
                $arrByte = array_values(unpack('C*', $data));
                $filePath = $dir . '/' . str_replace('.lsc', '.dds', $file);

                file_put_contents($filePath, pack('C*', ...q($arrByte)));

                echo "Written File: $filePath\n";
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage() . "\n";
            }
        }
    }
} elseif ($pref === '-s') {
    if (count($argv) < 4) {
        echo "Berikan opsi awalan (-d atau -s) dan jalur folder sebagai argumen baris perintah.\n";
        exit;
    }

    $next = $argv[3];

    try {
        $data = file_get_contents($dir);
        $arrByte = array_values(unpack('C*', $data));
        $lastIndex = strrpos($dir, "/");
        $requiredPath = substr($dir, 0, $lastIndex + 1);

        $filePath = strpos($next, '.dds') !== false
            ? $requiredPath . $next
            : $requiredPath . $next . '.dds';

        file_put_contents($filePath, pack('C*', ...q($arrByte)));

        echo "Berhasil Di Convert!: $filePath\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
} else {
    echo "Invalid Prefix Command\n";
}