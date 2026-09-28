<?php
// 外國公司（company.Id >= 1000000）的薪資與 ESG 資料，由 search.php 載入。
// 需要的變數：$pdo、$company。
// 資料表若尚未建立（例如組員的資料庫還沒匯入 foreign*），一律視為沒有資料，不讓頁面出錯。

function foreignQuery(PDO $pdo, string $sql, array $params = []): array {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

$foreignSourceLabels = [
    'SEC_PAYRATIO' => ['name' => '美國 SEC 股東會委託書', 'scope' => '全球員工（不含 CEO）'],
    'EDINET'       => ['name' => '日本有價證券報告書', 'scope' => '日本母公司員工'],
    'DART'         => ['name' => '韓國事業報告書', 'scope' => '韓國母公司員工'],
    'H1B_LCA'      => ['name' => '美國 H-1B 薪資申報（LCA）', 'scope' => '僅美國 H-1B 職缺'],
];

$foreignInfo = foreignQuery($pdo, "SELECT * FROM foreigncompany WHERE CompanyId = ?", [$company['Id']])[0] ?? null;

// 薪資：同一年若有多個來源，依來源分開呈現；這裡取該公司最新一筆資料所屬的來源作為主要來源。
$foreignSalaries = foreignQuery($pdo, "
    SELECT * FROM foreignsalary WHERE CompanyId = ? ORDER BY Year ASC
", [$company['Id']]);

$foreignLatestSalary = $foreignSalaries ? end($foreignSalaries) : null;
$foreignSalarySource = $foreignLatestSalary['Source'] ?? null;
$foreignSalaryTrend = array_values(array_filter(
    $foreignSalaries,
    fn($s) => $s['Source'] === $foreignSalarySource
));

// 頁首「資料年度｜資料來源」
$foreignHeaderYear = $foreignLatestSalary['Year'] ?? null;
$foreignHeaderSource = $foreignSourceLabels[$foreignSalarySource]['name'] ?? '尚無公開揭露資料';

// 排名：只和「同一資料來源」的外國公司比較（不同來源的口徑、幣別不同，不能混排）。
// 各公司會計年度結束月份不同，所以每家公司取自己最新一期的揭露。
// 有中位數就用中位數，否則用平均數。
$foreignRank = [];
$foreignRankKey = null;
if ($foreignLatestSalary) {
    $foreignRankKey = isset($foreignLatestSalary['MedianPay']) ? 'MedianPay' : 'AveragePay';
    $foreignRank = foreignQuery($pdo, "
        SELECT c.Id, c.Name, s.Year, s.Scope, s.{$foreignRankKey} AS Pay
        FROM foreignsalary s
        JOIN company c ON c.Id = s.CompanyId
        JOIN (
            SELECT CompanyId, MAX(Year) AS Year
            FROM foreignsalary
            WHERE Source = ? AND {$foreignRankKey} IS NOT NULL
            GROUP BY CompanyId
        ) latest ON latest.CompanyId = s.CompanyId AND latest.Year = s.Year
        WHERE s.Source = ?
        ORDER BY s.{$foreignRankKey} DESC
    ", [$foreignSalarySource, $foreignSalarySource]);
}

// ESG：資料表建立後自動生效。
$foreignEnvironment = foreignQuery($pdo, "
    SELECT * FROM foreignenvironment WHERE CompanyId = ? ORDER BY Year DESC LIMIT 1
", [$company['Id']])[0] ?? null;

$foreignWorkforce = foreignQuery($pdo, "
    SELECT * FROM foreignworkforce WHERE CompanyId = ? ORDER BY Year DESC LIMIT 1
", [$company['Id']])[0] ?? null;

$foreignSafety = foreignQuery($pdo, "
    SELECT * FROM foreignsafety WHERE CompanyId = ? ORDER BY Year ASC
", [$company['Id']]);

function foreignMoney(?float $amount, string $currency): string {
    if ($amount === null) return '未揭露';
    $symbols = ['USD' => '$', 'JPY' => '¥', 'KRW' => '₩', 'EUR' => '€'];
    return ($symbols[$currency] ?? $currency . ' ') . number_format($amount);
}
