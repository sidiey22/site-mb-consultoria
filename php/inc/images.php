<?php
declare(strict_types=1);

/**
 * Recebe um upload de imagem com segurança:
 * valida tamanho e tipo real (jpg, png, webp), corrige a rotação, reduz para no máximo 1600px de largura,
 * regrava a imagem (descartando qualquer conteúdo escondido) e salva com nome aleatório.
 * Retorna ['ok'=>true,'path'=>'uploads/2026/11/abc.jpg','url'=>'/uploads/...'] ou ['ok'=>false,'error'=>'...'].
 */
function save_upload(array $file, int $maxWidth = 1600): array
{
    $err = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($err === UPLOAD_ERR_NO_FILE) { return ['ok' => false, 'error' => 'Nenhum arquivo enviado.']; }
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) { return ['ok' => false, 'error' => 'A imagem é grande demais. O máximo é ' . round(UPLOAD_MAX_BYTES / 1048576) . ' MB.']; }
    if ($err !== UPLOAD_ERR_OK) { return ['ok' => false, 'error' => 'Não foi possível enviar a imagem. Tente novamente.']; }
    $tmp = (string)($file['tmp_name'] ?? '');
    if (!is_uploaded_file($tmp) && !(defined('MB_TESTING') && is_file($tmp))) { return ['ok' => false, 'error' => 'Envio inválido.']; }
    if (($file['size'] ?? 0) > UPLOAD_MAX_BYTES) { return ['ok' => false, 'error' => 'A imagem é grande demais. O máximo é ' . round(UPLOAD_MAX_BYTES / 1048576) . ' MB.']; }

    $fi = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$fi->file($tmp);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if ($ext === null) { return ['ok' => false, 'error' => 'Formato não aceito. Envie uma imagem JPG, PNG ou WEBP.']; }

    $info = @getimagesize($tmp);
    if (!$info || $info[0] < 1 || $info[1] < 1) { return ['ok' => false, 'error' => 'A imagem parece estar corrompida.']; }
    if ($info[0] * $info[1] > 25000000) { return ['ok' => false, 'error' => 'A imagem tem resolução alta demais. Reduza e envie de novo.']; }
    if (!function_exists('imagecreatetruecolor')) { return ['ok' => false, 'error' => 'O servidor não tem suporte a imagens (GD).']; }

    $img = match ($ext) {
        'jpg' => @imagecreatefromjpeg($tmp),
        'png' => @imagecreatefrompng($tmp),
        'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($tmp) : false,
    };
    if (!$img) { return ['ok' => false, 'error' => 'Não foi possível ler a imagem.']; }

    if ($ext === 'jpg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($tmp);
        $o = (int)($exif['Orientation'] ?? 1);
        $rot = [3 => 180, 6 => -90, 8 => 90][$o] ?? 0;
        if ($rot) { $r = imagerotate($img, $rot, 0); if ($r) { $img = $r; } }
    }

    $w = imagesx($img); $h = imagesy($img);
    if ($w > $maxWidth) {
        $nw = $maxWidth; $nh = (int)round($h * ($maxWidth / $w));
        $dst = imagecreatetruecolor($nw, $nh);
        if ($ext !== 'jpg') { imagealphablending($dst, false); imagesavealpha($dst, true); }
        imagecopyresampled($dst, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        $img = $dst;
    } elseif ($ext !== 'jpg') {
        imagealphablending($img, false); imagesavealpha($img, true);
    }

    $dir = 'uploads/' . date('Y') . '/' . date('m');
    $abs = MB_ROOT . '/' . $dir;
    if (!is_dir($abs) && !@mkdir($abs, 0755, true) && !is_dir($abs)) { return ['ok' => false, 'error' => 'Não foi possível gravar na pasta uploads. Verifique as permissões.']; }
    $name = date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $ext;
    $target = $abs . '/' . $name;

    $saved = match ($ext) {
        'jpg' => imagejpeg($img, $target, 84),
        'png' => imagepng($img, $target, 6),
        'webp' => imagewebp($img, $target, 82),
    };
    if (!$saved) { return ['ok' => false, 'error' => 'Falha ao salvar a imagem.']; }
    @chmod($target, 0644);
    return ['ok' => true, 'path' => $dir . '/' . $name, 'url' => url($dir . '/' . $name)];
}

/** URL pública de uma imagem guardada (aceita caminho "uploads/..." ou URL completa). */
function media_url(?string $path): string
{
    $path = (string)$path;
    if ($path === '') { return ''; }
    if (preg_match('~^https?://~i', $path) || str_starts_with($path, '/')) { return $path; }
    return url($path);
}
