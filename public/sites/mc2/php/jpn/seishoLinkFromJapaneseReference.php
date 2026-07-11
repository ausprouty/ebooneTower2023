<?php

/**
 * Convert a Japanese Bible reference to a seisho.or.jp Bible 3 chapter link.
 *
 * Example:
 *   seishoLinkFromJapaneseReference('ヨハネ1章１〜14節');
 *   => https://www.seisho.or.jp/bible_3/joh/#joh-1
 */
function seishoLinkFromJapaneseReference(string $reference): ?string
{
    $baseUrl = 'https://www.seisho.or.jp/bible_3/';

    // Convert full-width numbers/letters/spaces to half-width where practical.
    // Example: １ becomes 1.
    if (function_exists('mb_convert_kana')) {
        $reference = mb_convert_kana($reference, 'asKV', 'UTF-8');
    }

    // Normalize spacing and common range marks.
    $reference = trim($reference);
    $reference = str_replace(['〜', '～', '－', '–', '—'], '-', $reference);
    $reference = preg_replace('/\s+/u', '', $reference);

    // Match: [book name] [chapter] 章 ...
    // Example: ヨハネ1章1-14節
    if (!preg_match('/^(.+?)(\d+)章/u', $reference, $matches)) {
        return null;
    }

    $bookName = normalizeJapaneseBibleBookName($matches[1]);
    $chapter = (int) $matches[2];

    $bookMap = seishoJapaneseBookMap();

    if (!isset($bookMap[$bookName])) {
        return null;
    }

    $code = $bookMap[$bookName];

    // Psalms are split into five pages on this site:
    // 詩篇 第一巻, 第二巻, etc.
    if ($code === 'psa') {
        $code = seishoPsalmCode($chapter);
    }

    if ($code === null || $chapter < 1) {
        return null;
    }

    return $baseUrl . $code . '/#' . $code . '-' . $chapter;
}

/**
 * Normalize Japanese book names to a predictable form.
 */
function normalizeJapaneseBibleBookName(string $bookName): string
{
    $bookName = trim($bookName);
    $bookName = preg_replace('/\s+/u', '', $bookName);

    // Handle common shorthand.
    $bookName = str_replace('福音書', 'の福音書', $bookName);

    // Avoid accidental double の.
    $bookName = str_replace('のの福音書', 'の福音書', $bookName);

    return $bookName;
}

/**
 * Choose correct Psalms page.
 */
function seishoPsalmCode(int $chapter): ?string
{
    if ($chapter >= 1 && $chapter <= 41) {
        return 'psa1';
    }

    if ($chapter >= 42 && $chapter <= 72) {
        return 'psa2';
    }

    if ($chapter >= 73 && $chapter <= 89) {
        return 'psa3';
    }

    if ($chapter >= 90 && $chapter <= 106) {
        return 'psa4';
    }

    if ($chapter >= 107 && $chapter <= 150) {
        return 'psa5';
    }

    return null;
}

/**
 * Japanese Bible book names and common aliases.
 */
function seishoJapaneseBookMap(): array
{
    return [
        // Old Testament
        '創世記' => 'gen',
        '出エジプト記' => 'exo',
        'レビ記' => 'lev',
        '民数記' => 'num',
        '申命記' => 'deu',
        'ヨシュア記' => 'jos',
        '士師記' => 'jdg',
        'ルツ記' => 'rut',
        'サムエル記第一' => '1sa',
        'サムエル記第1' => '1sa',
        '第一サムエル記' => '1sa',
        '第1サムエル記' => '1sa',
        'サムエル記第二' => '2sa',
        'サムエル記第2' => '2sa',
        '第二サムエル記' => '2sa',
        '第2サムエル記' => '2sa',
        '列王記第一' => '1ki',
        '列王記第1' => '1ki',
        '第一列王記' => '1ki',
        '第1列王記' => '1ki',
        '列王記第二' => '2ki',
        '列王記第2' => '2ki',
        '第二列王記' => '2ki',
        '第2列王記' => '2ki',
        '歴代誌第一' => '1ch',
        '歴代誌第1' => '1ch',
        '第一歴代誌' => '1ch',
        '第1歴代誌' => '1ch',
        '歴代誌第二' => '2ch',
        '歴代誌第2' => '2ch',
        '第二歴代誌' => '2ch',
        '第2歴代誌' => '2ch',
        'エズラ記' => 'ezr',
        'ネヘミヤ記' => 'neh',
        'エステル記' => 'est',
        'ヨブ記' => 'job',
        '詩篇' => 'psa',
        '詩編' => 'psa',
        '箴言' => 'pro',
        '伝道者の書' => 'ecc',
        'コヘレトの言葉' => 'ecc',
        '雅歌' => 'sng',
        'イザヤ書' => 'isa',
        'エレミヤ書' => 'jer',
        '哀歌' => 'lam',
        'エゼキエル書' => 'ezk',
        'ダニエル書' => 'dan',
        'ホセア書' => 'hos',
        'ヨエル書' => 'jol',
        'アモス書' => 'amo',
        'オバデヤ書' => 'oba',
        'ヨナ書' => 'jon',
        'ミカ書' => 'mic',
        'ナホム書' => 'nam',
        'ハバクク書' => 'hab',
        'ゼパニヤ書' => 'zep',
        'ハガイ書' => 'hag',
        'ゼカリヤ書' => 'zec',
        'マラキ書' => 'mal',

        // New Testament
        'マタイの福音書' => 'mat',
        'マタイ' => 'mat',
        'マルコの福音書' => 'mar',
        'マルコ' => 'mar',
        'ルカの福音書' => 'luk',
        'ルカ' => 'luk',
        'ヨハネの福音書' => 'joh',
        'ヨハネ' => 'joh',
        '使徒の働き' => 'act',
        '使徒' => 'act',
        '使徒行伝' => 'act',
        'ローマ人への手紙' => 'rom',
        'ローマ' => 'rom',
        'コリント人への手紙第一' => '1co',
        'コリント人への手紙第1' => '1co',
        '第一コリント人への手紙' => '1co',
        '第1コリント人への手紙' => '1co',
        'コリント第一' => '1co',
        '第一コリント' => '1co',
        '1コリント' => '1co',
        'コリント人への手紙第二' => '2co',
        'コリント人への手紙第2' => '2co',
        '第二コリント人への手紙' => '2co',
        '第2コリント人への手紙' => '2co',
        'コリント第二' => '2co',
        '第二コリント' => '2co',
        '2コリント' => '2co',
        'ガラテヤ人への手紙' => 'gal',
        'ガラテヤ' => 'gal',
        'エペソ人への手紙' => 'eph',
        'エペソ' => 'eph',
        'ピリピ人への手紙' => 'php',
        'ピリピ' => 'php',
        'コロサイ人への手紙' => 'col',
        'コロサイ' => 'col',
        'テサロニケ人への手紙第一' => '1th',
        'テサロニケ人への手紙第1' => '1th',
        '第一テサロニケ人への手紙' => '1th',
        '第1テサロニケ人への手紙' => '1th',
        '1テサロニケ' => '1th',
        'テサロニケ人への手紙第二' => '2th',
        'テサロニケ人への手紙第2' => '2th',
        '第二テサロニケ人への手紙' => '2th',
        '第2テサロニケ人への手紙' => '2th',
        '2テサロニケ' => '2th',
        'テモテへの手紙第一' => '1ti',
        'テモテへの手紙第1' => '1ti',
        '第一テモテへの手紙' => '1ti',
        '第1テモテへの手紙' => '1ti',
        '1テモテ' => '1ti',
        'テモテへの手紙第二' => '2ti',
        'テモテへの手紙第2' => '2ti',
        '第二テモテへの手紙' => '2ti',
        '第2テモテへの手紙' => '2ti',
        '2テモテ' => '2ti',
        'テトスへの手紙' => 'tit',
        'テトス' => 'tit',
        'ピレモンへの手紙' => 'phm',
        'ピレモン' => 'phm',
        'ヘブル人への手紙' => 'heb',
        'ヘブル' => 'heb',
        'ヘブライ人への手紙' => 'heb',
        'ヘブライ' => 'heb',
        'ヤコブの手紙' => 'jas',
        'ヤコブ' => 'jas',
        'ペテロの手紙第一' => '1pe',
        'ペテロの手紙第1' => '1pe',
        '第一ペテロの手紙' => '1pe',
        '第1ペテロの手紙' => '1pe',
        '1ペテロ' => '1pe',
        'ペテロの手紙第二' => '2pe',
        'ペテロの手紙第2' => '2pe',
        '第二ペテロの手紙' => '2pe',
        '第2ペテロの手紙' => '2pe',
        '2ペテロ' => '2pe',
        'ヨハネの手紙第一' => '1jo',
        'ヨハネの手紙第1' => '1jo',
        '第一ヨハネの手紙' => '1jo',
        '第1ヨハネの手紙' => '1jo',
        '1ヨハネ' => '1jo',
        'ヨハネの手紙第二' => '2jo',
        'ヨハネの手紙第2' => '2jo',
        '第二ヨハネの手紙' => '2jo',
        '第2ヨハネの手紙' => '2jo',
        '2ヨハネ' => '2jo',
        'ヨハネの手紙第三' => '3jo',
        'ヨハネの手紙第3' => '3jo',
        '第三ヨハネの手紙' => '3jo',
        '第3ヨハネの手紙' => '3jo',
        '3ヨハネ' => '3jo',
        'ユダの手紙' => 'jud',
        'ユダ' => 'jud',
        'ヨハネの黙示録' => 'rev',
        '黙示録' => 'rev',
    ];
}
