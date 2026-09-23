<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "通信網路";
$companies = getCompanies($category);
// 有上市公司的 Sector 才可點擊，其餘節點顯示為停用
$availableSectors = array_flip(array_column($companies, "Sector"));

// 產業鏈節點參考櫃買中心產業價值鏈 I000，Sector 名稱需與 companycategory 一致
$upstreamSectors = [
    "網路IC" => "fa-solid fa-microchip",
    "微處理器" => "fa-solid fa-microchip",
    "記憶體" => "fa-solid fa-memory",
    "主/被動元件" => "fa-solid fa-bolt",
    "印刷電路板" => "fa-solid fa-diagram-project",
    "塑膠/金屬機殼" => "fa-solid fa-box",
    "線材" => "fa-solid fa-ethernet",
    "其他零組件" => "fa-solid fa-gears",
];

$downstreamSectors = [
    "網路設備" => ["fa-solid fa-network-wired", "如數據機、網路卡、閘道器、路由器、網路電話"],
    "光通訊設備" => ["fa-solid fa-lightbulb", "如光纖電纜、光傳輸設備"],
    "無線通訊設備" => ["fa-solid fa-tower-broadcast", "如行動電話、衛星定位系統、衛星/微波通訊設備、數位機上盒"],
    "有線通訊設備" => ["fa-solid fa-phone", "如電話機、傳真機"],
    "電信服務業" => ["fa-solid fa-signal", "固網、行動寬頻與電信服務"],
];
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業供應鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode($companies, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');

        body { font-family: 'Noto Sans TC', sans-serif; }
        .comm-flow-card { min-height: 360px; }
        .comm-node { transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease; }
        .comm-node:hover { transform: translateY(-4px); }
        .comm-node:focus-visible { outline: 3px solid #0ea5e9; outline-offset: 3px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24" aria-labelledby="supply-chain-title">
            <h3 id="supply-chain-title" class="mb-8 flex items-center gap-2 text-2xl font-bold text-slate-800">
                <span class="h-8 w-2 rounded-full bg-sky-600"></span> 供應鏈結構圖
            </h3>

            <div class="relative overflow-visible rounded-2xl border border-slate-100 bg-white p-5 pb-12 shadow-xl sm:p-8">
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-cyan-500 via-sky-500 to-blue-600"></div>

                <div id="main_communication_panel" class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-12" aria-label="通信網路產業供應鏈：上游、下游">
                    <!-- 上游：通訊零組件 -->
                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 font-bold text-cyan-700 shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm font-normal text-slate-400">晶片與通訊零組件</span></h4>
                        </div>
                        <div class="comm-flow-card relative grid flex-1 grid-cols-1 content-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm sm:grid-cols-2">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <?php foreach ($upstreamSectors as $sector => $icon): ?>
                                <?php if (isset($availableSectors[$sector])): ?>
                                <button type="button" onclick="toggleCompanyList(companySectors, '<?= $sector ?>', 'cyan')" class="comm-node relative z-10 flex w-full items-center gap-3 rounded-lg border border-cyan-200 bg-white p-4 text-left shadow-sm hover:border-cyan-400 hover:bg-cyan-50 hover:shadow-md">
                                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-cyan-50 text-cyan-600"><i class="<?= $icon ?>" aria-hidden="true"></i></span>
                                    <span class="text-base font-bold text-slate-700"><?= $sector ?></span>
                                </button>
                                <?php else: ?>
                                <div aria-disabled="true" class="relative z-10 flex w-full items-center gap-3 rounded-lg border border-slate-200 bg-slate-100 p-4 opacity-60 shadow-sm cursor-not-allowed">
                                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-400"><i class="<?= $icon ?>" aria-hidden="true"></i></span>
                                    <span class="text-base font-bold text-slate-400"><?= $sector ?></span>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </article>

                    <!-- 下游：通訊設備與電信服務 -->
                    <article class="flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700 shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm font-normal text-slate-400">通訊設備與電信服務</span></h4>
                        </div>
                        <div class="comm-flow-card flex flex-1 flex-col justify-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <?php foreach ($downstreamSectors as $sector => [$icon, $desc]): ?>
                                <?php if (isset($availableSectors[$sector])): ?>
                                <button type="button" onclick="toggleCompanyList(companySectors, '<?= $sector ?>', 'blue')" class="comm-node flex w-full items-center gap-4 rounded-lg border border-blue-200 bg-white p-4 text-left shadow-sm hover:border-blue-400 hover:bg-blue-50 hover:shadow-md">
                                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-50 text-blue-600"><i class="<?= $icon ?>" aria-hidden="true"></i></span>
                                    <span class="flex flex-col">
                                        <span class="text-base font-bold text-slate-700"><?= $sector ?></span>
                                        <span class="text-xs text-slate-400"><?= $desc ?></span>
                                    </span>
                                </button>
                                <?php else: ?>
                                <div aria-disabled="true" class="flex w-full items-center gap-4 rounded-lg border border-slate-200 bg-slate-100 p-4 opacity-60 shadow-sm cursor-not-allowed">
                                    <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-400"><i class="<?= $icon ?>" aria-hidden="true"></i></span>
                                    <span class="flex flex-col">
                                        <span class="text-base font-bold text-slate-400"><?= $sector ?></span>
                                        <span class="text-xs text-slate-400"><?= $desc ?></span>
                                    </span>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </div>

                <div class="relative z-20 mt-16 space-y-6">
                    <h4 class="flex items-center gap-2 border-l-4 border-sky-600 pl-4 text-xl font-bold text-slate-700">點擊上方圖表查看公司列表</h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從網路晶片、通訊零組件到網通設備與電信服務，掌握通信網路產業鏈脈絡");
    </script>
</body>
</html>
