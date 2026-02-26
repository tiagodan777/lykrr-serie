<?php
function html_escape($text) {
    $text = $text ?? '';
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8', false);
}

function pdo($pdo, $sql, $arguments = null) {
    if (!$arguments) {
        return $pdo->query($sql);
    }
    $statement = $pdo->prepare($sql);
    $statement->execute($arguments);
    return $statement;
}

function create_filename($original) {
    $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
    $unique = bin2hex(random_bytes(16)); // 32 chars únicos
    return $unique . '.' . $ext;
}

function is_number($number, $min = 0, $max = 100) {
    return ($number >= $min && $number <= $max);
}

function is_text($text, $min = 0, $max = 100) {
    $length = mb_strlen($text);
    return ($length >= $min && $length <= $max);
}

function is_member_id($member_id, $member_id_list) {
    foreach ($member_id_list as $member) {
        if ($member_id == $member['id']) {
            return true;
        }
    }
    return false;
}

function is_category_id($category_id, $category_id_list) {
    foreach ($category_id_list as $category) {
        if ($category_id == $category['id']) {
            return true;
        }
    }
    return false;
}

function create_seo_name($string) {
    $text = mb_strtolower($string);
    $text = trim($text);
    if (function_exists('transliterator_transliterate')) {
        $text = transliterator_transliterate('Latin-ASCII', $text);
    }
    $text = preg_replace('/ /', '-', $text);
    $text = preg_replace('/[^A-z0-9]/', '', $text);
    return $text;
}