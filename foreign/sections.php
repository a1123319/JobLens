<?php
// 外國公司的薪資、排名、職安、ESG 區塊，取代 search.php 對應的四個 section。
// section id 與原頁面相同，側邊導覽不需修改；canvas id 則不同，原頁面的圖表程式會自動略過。
$fSrc = $foreignSourceLabels[$foreignSalarySource] ?? null;
$fCur = $foreignLatestSalary['Currency'] ?? 'USD';
$fYear = $foreignLatestSalary['Year'] ?? null;
?>
        <section id="section-salary" class="scroll-mt-24 relative">
            <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
                <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 薪資與福利透視
            </h3>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-bold flex items-center gap-2 text-slate-700">
                            <img src="assets/money.png" class="w-6 h-6 object-contain">
                            近年薪資趨勢
                        </h4>
                        <?php if ($fSrc): ?>
                        <span class="text-xs text-slate-400">資料來源：<?= $fSrc['name'] ?></span>
                        <?php endif ?>
                    </div>
                    <div class="w-full flex-1">
                        <?php if (!empty($foreignSalaryTrend)): ?>
                        <canvas id="foreign-salary-trend-chart"></canvas>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center h-full box-border py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <i class="fa-solid fa-dollar-sign text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold">「<?= $company["Name"]?>」未揭露薪資資訊</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex-1 flex flex-col justify-center">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-bold text-slate-700"><?= $fYear ? "{$fYear}年" : '' ?>薪資結構</h4>
                            <?php if ($fSrc): ?>
                            <span class="text-[10px] bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-bold"><?= $fSrc['scope'] ?></span>
                            <?php endif ?>
                        </div>
                        <div class="flex flex-col gap-3">
                            <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                                <p class="text-slate-500 text-xs mb-1">平均數 (Mean)</p>
                                <p class="text-2xl font-bold text-slate-700">
                                <?php if (isset($foreignLatestSalary['AveragePay'])): ?>
                                    <?= foreignMoney($foreignLatestSalary['AveragePay'], $fCur) ?><span class="text-sm font-normal text-slate-500"> / 年</span>
                                <?php else: ?>
                                    <span class="text-xl font-bold text-slant-600">未揭露</span>
                                <?php endif; ?>
                                </p>
                            </div>
                            <div class="bg-cyan-50 p-4 rounded-lg border border-cyan-200">
                                <p class="text-cyan-800 text-xs mb-1"> 中位數 (Median)</p>
                                <p class="text-2xl font-bold text-cyan-800">
                                <?php if (isset($foreignLatestSalary['MedianPay'])): ?>
                                    <?= foreignMoney($foreignLatestSalary['MedianPay'], $fCur) ?><span class="text-sm font-normal text-cyan-700"> / 年</span>
                                <?php else: ?>
                                    <span class="text-xl font-bold text-cyan-800">未揭露</span>
                                <?php endif; ?>
                                </p>
                                <?php if (($foreignLatestSalary['PayRatio'] ?? 0) >= 1):
                                    $fRatio = (float)$foreignLatestSalary['PayRatio']; ?>
                                <p class="text-xs text-cyan-700 mt-2">CEO 薪酬為員工中位數的 <b><?= number_format($fRatio, floor($fRatio) == $fRatio ? 0 : 1) ?></b> 倍</p>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm p-6 border border-slate-100 flex-1 flex flex-col justify-center">
                        <h4 class="text-md font-bold flex items-center gap-2 mb-4 text-slate-700">
                            <img src="assets/people.png" class="w-5 h-5 object-contain"> 職場環境
                        </h4>
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-600">女性主管佔比</span>
                                <?php if (isset($foreignWorkforce["FemaleManagerRatio"])): ?>
                                <span class="text-xl font-bold text-purple-600"><?= formatPercentage($foreignWorkforce["FemaleManagerRatio"], 1) ?></span>
                                <?php else: ?>
                                <span class="text-xl font-bold text-slant-600">未揭露</span>
                                <?php endif ?>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full relative" style="width: <?= formatPercentage($foreignWorkforce["FemaleManagerRatio"] ?? 0, 2) ?>"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="section-rank" class="scroll-mt-24 relative">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 前 10 名薪資排名<?php if (!empty($foreignRank)) { echo "【外國企業・{$fSrc['name']}】"; } ?>
                </h3>
            </div>
            <?php if (!empty($foreignRank)): ?>
            <div class="bg-white border border-slate-100 rounded-xl shadow-lg p-6 relative overflow-hidden">
                <p class="text-xs text-slate-400 mb-4 text-right">
                    <?= $foreignRankKey === 'MedianPay' ? '員工薪資中位數' : '員工平均薪資' ?>｜單位：<?= $fCur ?> / 年｜各公司最新一期揭露（共 <?= count($foreignRank) ?> 家）｜僅與同一資料來源的外國企業比較，不與台灣企業混排
                </p>
                <div class="w-full h-[450px]">
                    <canvas id="foreign-salary-rank-chart"></canvas>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8">
                <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                        <i class="fa-solid fa-dollar-sign text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」未揭露薪資資訊</p>
                </div>
            </div>
            <?php endif ?>
        </section>

        <section id="section-safety" class="scroll-mt-24 space-y-6 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                    <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 職業安全
                </h3>
            </div>
            <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-8">
                <div class="mb-6 pb-4 border-b border-slate-100">
                    <h4 class="text-lg font-bold text-slate-700">
                        <span class="flex items-center gap-3"><i class="fa-solid fa-fire text-red-500"></i>職業災害指標</span>
                    </h4>
                </div>
                <?php if (!empty($foreignSafety)):
                    $fs = end($foreignSafety); ?>
                <div class="flex flex-col md:flex-row gap-6 w-full">
                    <div class="flex-1 bg-orange-50 p-6 rounded-xl border border-orange-100 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center mb-3 p-2.5">
                            <img src="assets/injury.png" class="w-full h-full object-contain">
                        </div>
                        <h5 class="text-slate-500 text-xs font-bold mb-1 uppercase">可記錄職業傷害件數</h5>
                        <p class="text-3xl font-bold text-slate-800"><?= isset($fs['RecordableCases']) ? number_format($fs['RecordableCases']) : '-' ?></p>
                        <p class="text-xs text-slate-400 mt-2"><?= $fs['Year'] ?>年・美國據點</p>
                    </div>
                    <div class="flex-1 bg-blue-50 p-6 rounded-xl border border-blue-100 flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3 p-2.5">
                            <img src="assets/ratio.png" class="w-full h-full object-contain">
                        </div>
                        <h5 class="text-slate-500 text-xs font-bold mb-1 uppercase">可記錄傷害率 (TRIR)</h5>
                        <p class="text-3xl font-bold text-slate-800"><?= isset($fs['Trir']) ? number_format($fs['Trir'], 2) : '-' ?></p>
                        <p class="text-xs text-slate-400 mt-2">每 20 萬工時</p>
                    </div>
                </div>
                <?php else: ?>
                <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                        <i class="fa-solid fa-fire text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-bold">「<?= $company["Name"]?>」尚無職業安全資料</p>
                    <p class="text-slate-400 text-sm mt-2">台灣職災通報資料不涵蓋外國企業</p>
                </div>
                <?php endif ?>
            </div>
        </section>

        <section id="section-esg" class="scroll-mt-24 space-y-6 relative mt-16">
            <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2 mb-4">
                <span class="bg-cyan-600 w-1.5 h-6 rounded-full"></span> 環境永續 (ESG)
            </h3>
            <div class="bg-white rounded-xl shadow-lg border border-slate-100 p-8">
                <div class="space-y-12">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center p-2">
                                <img src="assets/air-pollution.png" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">溫室氣體排放量分析</h3>
                                <p class="text-xs text-slate-500">單位：公噸 CO₂e<?= $foreignEnvironment ? "｜{$foreignEnvironment['Year']}年" : '' ?></p>
                            </div>
                        </div>
                        <?php
                        $fScopes = [
                            ['直接排放 (範疇一)', $foreignEnvironment['Scope1EmissionTonCO2e'] ?? null],
                            ['能源間接排放 (範疇二)', $foreignEnvironment['Scope2EmissionTonCO2e'] ?? null],
                            ['其他間接排放 (範疇三)', $foreignEnvironment['Scope3EmissionTonCO2e'] ?? null],
                        ];
                        $fHasGhg = count(array_filter($fScopes, fn($s) => $s[1] !== null)) > 0;
                        ?>
                        <?php if ($fHasGhg): ?>
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                            <div class="lg:col-span-2 h-[280px]">
                                <canvas id="foreign-ghg-chart"></canvas>
                            </div>
                            <div class="space-y-3">
                                <?php foreach ($fScopes as [$label, $value]): ?>
                                <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-100">
                                    <h4 class="text-sm font-bold text-emerald-700 mb-1"><?= $label ?></h4>
                                    <?php if ($value !== null): ?>
                                    <p class="text-xl font-bold text-emerald-800 font-mono"><?= htmlspecialchars(formatNumber($value)) ?><span class="text-xl font-bold text-emerald-800 font-sans"> 公噸 CO₂e</span></p>
                                    <?php else: ?>
                                    <p class="text-xl font-bold text-emerald-800">未揭露</p>
                                    <?php endif ?>
                                </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <i class="fa-solid fa-leaf text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」未揭露溫室氣體排放量資料</p>
                        </div>
                        <?php endif ?>
                    </div>
                    <div class="border-t border-slate-100"></div>

                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center p-2">
                                <img src="assets/renewable-energy.png" class="w-full h-full object-contain">
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">再生能源使用概況</h3>
                                <p class="text-xs text-slate-500">Renewable Energy Usage</p>
                            </div>
                        </div>
                        <?php if (isset($foreignEnvironment['RenewEnergyUsageRate'])):
                            $fRate = (float)$foreignEnvironment['RenewEnergyUsageRate']; ?>
                        <div class="max-w-md mx-auto bg-slate-50 p-5 rounded-xl border border-slate-100">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-bold text-emerald-700"><i class="fa-solid fa-leaf mr-1"></i>再生能源</span>
                                <span class="font-bold text-emerald-700"><?= htmlspecialchars(formatPercentage($fRate, 2)) ?></span>
                            </div>
                            <div class="w-full bg-emerald-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= $fRate * 100 ?>%"></div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-8 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                <i class="fa-solid fa-leaf text-2xl"></i>
                            </div>
                            <p class="text-slate-500 font-bold">「<?= $company['Name'] ?>」未揭露再生能源使用資料</p>
                        </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </section>

        <script>
        (() => {
            const currency = <?= json_encode($fCur) ?>;
            const fmt = v => v === null ? '未揭露' : currency + ' ' + Math.round(v).toLocaleString();
            const trend = <?= json_encode(array_map(fn($s) => [
                'year' => (int)$s['Year'],
                'median' => $s['MedianPay'] !== null ? (float)$s['MedianPay'] : null,
                'average' => $s['AveragePay'] !== null ? (float)$s['AveragePay'] : null,
            ], $foreignSalaryTrend)) ?>;
            const rank = <?= json_encode($foreignRank, JSON_UNESCAPED_UNICODE) ?>;
            const targetId = <?= (int)$company['Id'] ?>;
            const scopes = <?= json_encode(array_map(fn($s) => $s[1] !== null ? (float)$s[1] : null, $fScopes)) ?>;

            document.addEventListener('DOMContentLoaded', () => {
                const trendCanvas = document.getElementById('foreign-salary-trend-chart');
                if (trendCanvas) {
                    const datasets = [];
                    if (trend.some(t => t.average !== null)) datasets.push({
                        label: '平均數', data: trend.map(t => t.average),
                        borderColor: '#94a3b8', backgroundColor: 'rgba(148, 163, 184, 0.1)',
                        borderWidth: 3, tension: 0, pointBackgroundColor: '#94a3b8', pointRadius: 4, pointHoverRadius: 6
                    });
                    if (trend.some(t => t.median !== null)) datasets.push({
                        label: '中位數', data: trend.map(t => t.median),
                        borderColor: '#0891b2', backgroundColor: 'rgba(8, 145, 178, 0.1)',
                        borderWidth: 3, tension: 0, pointBackgroundColor: '#0891b2', pointRadius: 4, pointHoverRadius: 6
                    });
                    new Chart(trendCanvas.getContext('2d'), {
                        type: 'line',
                        data: { labels: trend.map(t => t.year + '年'), datasets },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top', labels: { font: { weight: 'bold' } } },
                                tooltip: { callbacks: { label: c => c.dataset.label + ': ' + fmt(c.raw) } }
                            },
                            scales: {
                                y: { beginAtZero: false, grid: { color: '#f1f5f9' }, ticks: { callback: v => v.toLocaleString() } },
                                x: { grid: { display: false } }
                            }
                        }
                    });
                }

                // 與原頁面相同：顯示前 10 名，若本公司不在前 10 名則附加在最後
                const rankCanvas = document.getElementById('foreign-salary-rank-chart');
                if (rankCanvas && rank.length) {
                    const ranked = rank.map((d, i) => ({ ...d, pos: i + 1 }));
                    const shown = ranked.slice(0, 10);
                    if (!shown.some(d => +d.Id === targetId)) {
                        const self = ranked.find(d => +d.Id === targetId);
                        if (self) shown.push(self);
                    }
                    const isSelf = d => +d.Id === targetId;
                    const tickColors = shown.map(d => isSelf(d) ? '#0891b2' : '#64748b');
                    new Chart(rankCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: shown.map(d => `No.${d.pos} ${d.Name}`),
                            datasets: [{
                                data: shown.map(d => +d.Pay),
                                backgroundColor: shown.map(d => isSelf(d) ? 'rgba(8, 145, 178, 0.8)' : 'rgba(203, 213, 225, 0.6)'),
                                borderColor: shown.map(d => isSelf(d) ? 'rgb(8, 145, 178)' : 'rgb(148, 163, 184)'),
                                borderWidth: 1, borderRadius: 4, barThickness: 28
                            }]
                        },
                        options: {
                            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => `${fmt(c.raw)}（${shown[c.dataIndex].Year}年）` } } },
                            scales: {
                                y: { ticks: { color: c => tickColors[c.index], font: c => ({ weight: tickColors[c.index] === '#0891b2' ? 'bold' : 'normal' }) } },
                                x: { display: false, grid: { color: '#f1f5f9' } }
                            }
                        }
                    });
                }

                const ghgCanvas = document.getElementById('foreign-ghg-chart');
                if (ghgCanvas) {
                    new Chart(ghgCanvas.getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: ['直接排放', '能源間接排放', '其他間接排放'],
                            datasets: [{ label: '排放量', data: scopes.map(v => v ?? 0),
                                backgroundColor: ['#34d399', '#10b981', '#047857'], borderRadius: 4, barPercentage: 0.5 }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: { legend: { display: false },
                                tooltip: { callbacks: { label: c => scopes[c.dataIndex] === null ? '未揭露' : c.raw.toLocaleString() } } },
                            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' } }, x: { grid: { display: false } } }
                        }
                    });
                }
            });
        })();
        </script>
