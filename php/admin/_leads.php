<?php
declare(strict_types=1);

/** Filtros da lista de leads (usados na tela e na exportação CSV). */
function leads_filter(): array
{
    $q = get('q'); $st = get('status'); $orig = get('origem'); $de = get('de'); $ate = get('ate');
    $where = ['1=1']; $p = [];
    if ($q !== '') {
        $where[] = '(name LIKE :q1 OR email LIKE :q2 OR phone LIKE :q3 OR message LIKE :q4 OR company LIKE :q5)';
        foreach ([1, 2, 3, 4, 5] as $i) { $p[':q' . $i] = '%' . $q . '%'; }
    }
    if (array_key_exists($st, LEAD_STATUS)) { $where[] = 'status = :st'; $p[':st'] = $st; }
    if ($orig !== '') { $where[] = 'form_name = :orig'; $p[':orig'] = $orig; }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $de)) { $where[] = 'created_at >= :de'; $p[':de'] = $de . ' 00:00:00'; }
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $ate)) { $where[] = 'created_at <= :ate'; $p[':ate'] = $ate . ' 23:59:59'; }
    $keep = array_filter(['q' => $q, 'status' => array_key_exists($st, LEAD_STATUS) ? $st : '', 'origem' => $orig, 'de' => $de, 'ate' => $ate], fn($v) => $v !== '');
    return [implode(' AND ', $where), $p, $keep];
}
