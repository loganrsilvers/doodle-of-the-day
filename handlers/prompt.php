<?php
// handlers/prompt.php - Generates daily prompt from text files

function get_daily_prompt() {
    $adjectiveFile = __DIR__ . '/../adjectives.txt';
    $nounFile = __DIR__ . '/../nouns.txt';
    
    // Read adjectives
    $adjectives = file($adjectiveFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    // Read nouns
    $nouns = file($nounFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    if (empty($adjectives) || empty($nouns)) {
        return ['adjective' => 'Mystery', 'noun' => 'Thing'];
    }
    
    // Use day of year as seed for consistent daily prompt
    $dayOfYear = date('z'); // 0-365
    $adjectiveIndex = $dayOfYear % count($adjectives);
    $nounIndex = ($dayOfYear + 7) % count($nouns); // Offset to mix it up
    
    return [
        'adjective' => $adjectives[$adjectiveIndex],
        'noun' => $nouns[$nounIndex],
        'full_prompt' => $adjectives[$adjectiveIndex] . ' ' . $nouns[$nounIndex]
    ];
}

function get_daily_theme() {
    // Different layout/theme based on day of week
    $dayOfWeek = date('N'); // 1 (Monday) - 7 (Sunday)
    
    $themes = [
        1 => 'purple',    // Monday
        2 => 'blue',      // Tuesday
        3 => 'green',     // Wednesday
        4 => 'orange',    // Thursday
        5 => 'red',       // Friday
        6 => 'pink',      // Saturday
        7 => 'gold'       // Sunday
    ];
    
    return $themes[$dayOfWeek] ?? 'purple';
}
?>