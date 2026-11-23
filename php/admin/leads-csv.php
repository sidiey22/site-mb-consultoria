<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
require __DIR__ . '/_leads.php';
$u = require_login(true);

[$W, $P] = leads_filter();
$rows = rows("SELECT * FROM leads WHERE $W ORDER BY created_at DESC, id DESC", $P);

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="leads-mb-' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-store');
echo "\xEF\xBB\xBF"; // BOM: o Excel abre com acentos certos
$out = fopen('php://output', 'w');
$cell = function ($v): string {
    $v = (string)$v;
    // evita que o Excel execute fórmulas escondidas em textos digitados por visitantes
    return ($v !== '' && strpos("=+-@\t\r", $v[0]) !== false) ? "'" . $v : $v;
};
$head = ['ID', 'Data', 'Hora', 'Nome', 'E-mail', 'Telefone/WhatsApp', 'Instituição/Projeto', 'Cidade/Estado', 'Assunto', 'Mensagem', 'Status', 'Formulário', 'Página', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'Chegou por', 'Anotações'];
fputcsv($out, $head, ';', '"', '\\', "\r\n");
foreach ($rows as $r) {
    fputcsv($out, array_map($cell, [
        $r['id'], fmt_date($r['created_at'], 'd/m/Y'), fmt_date($r['created_at'], 'H:i'), $r['name'], $r['email'], $r['phone'], $r['company'], $r['city'], $r['subject'],
        preg_replace('/\s*[\r\n]+\s*/', ' ', (string)$r['message']), LEAD_STATUS[$r['status']] ?? $r['status'], $r['form_name'], $r['page_url'],
        $r['utm_source'], $r['utm_medium'], $r['utm_campaign'], $r['utm_term'], $r['utm_content'], $r['referrer'], preg_replace('/\s*[\r\n]+\s*/', ' ', (string)$r['notes']),
    ]), ';', '"', '\\', "\r\n");
}
fclose($out);
