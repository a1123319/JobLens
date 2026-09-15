<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "石化及塑橡膠";

// 產業鏈節點參考櫃買中心產業價值鏈 N000，Sector 名稱需與 companycategory 一致
$downstreamSectors = [
    "塑膠製品" => "fa-solid fa-bottle-water",
    "橡膠製品" => "fa-solid fa-ring",
    "清潔用品" => "fa-solid fa-pump-soap",
    "人造纖維" => "fa-solid fa-shirt",
    "顏染料" => "fa-solid fa-palette",
    "接著劑(合成樹脂)" => "fa-solid fa-droplet",
    "塑化劑(可塑劑)" => "fa-solid fa-flask",
    "農藥" => "fa-solid fa-seedling",
    "化妝品" => "fa-solid fa-spray-can-sparkles",
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
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>);

        function petroToggleCompanyList(sector, color) {
            if (!companySectors.has(sector)) {
                const companyListDiv = document.getElementById('company-list');
                companyListDiv.className = `bg-white p-6 rounded-xl border border-slate-200 shadow-sm ring-2 ring-${color}-200`;
                companyListDiv.innerHTML = `
                    <p class="font-bold text-slate-700 text-lg mb-2">${sector}</p>
                    <p class="text-sm text-slate-500">目前尚無可顯示的公司資料。</p>
                `;
                companyListDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }

            toggleCompanyList(companySectors, sector, color);
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');

        body { font-family: 'Noto Sans TC', sans-serif; }
        .petro-flow-card { min-height: 360px; }
        .petro-node { transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease; }
        .petro-node:hover { transform: translateY(-4px); }
        .petro-node:focus-visible { outline: 3px solid #8b5cf6; outline-offset: 3px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24" aria-labelledby="supply-chain-title">
            <h3 id="supply-chain-title" class="mb-8 flex items-center gap-2 text-2xl font-bold text-slate-800">
                <span class="h-8 w-2 rounded-full bg-violet-600"></span> 供應鏈結構圖
            </h3>

            <div class="relative overflow-visible rounded-2xl border border-slate-100 bg-white p-5 pb-12 shadow-xl sm:p-8">
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-indigo-500 via-violet-500 to-teal-500"></div>

                <div id="main_petrochemical_panel" class="grid grid-cols-1 gap-10 lg:grid-cols-[1fr_1fr_1.6fr] lg:gap-12" aria-label="石化及塑橡膠產業供應鏈：上游、中游、下游">
                    <!-- 上游：石化上游原料及相關鑽探設備 -->
                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700 shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm font-normal text-slate-400">原油與基礎原料</span></h4>
                        </div>
                        <div class="petro-flow-card relative flex flex-1 items-center rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <button type="button" onclick="petroToggleCompanyList('石化上游原料及相關鑽探設備', 'indigo')" class="petro-node relative z-10 flex w-full flex-col items-center justify-center gap-4 rounded-lg border border-indigo-200 bg-white p-8 text-center shadow-sm hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-md">
                                <i class="fa-solid fa-oil-well text-4xl text-indigo-600" aria-hidden="true"></i>
                                <span class="text-xl font-bold leading-snug text-slate-700">石化上游原料<br>及相關鑽探設備</span>
                                <span class="text-sm text-slate-400">原油、輕油裂解原料與探勘設備</span>
                            </button>
                        </div>
                    </article>

                    <!-- 中游：石化中間原料 -->
                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-100 font-bold text-violet-700 shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm font-normal text-slate-400">裂解與中間原料</span></h4>
                        </div>
                        <div class="petro-flow-card relative flex flex-1 items-center rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <button type="button" onclick="petroToggleCompanyList('石化中間原料(例如乙烯、丙烯、丁二烯、苯、酚等)', 'violet')" class="petro-node relative z-10 flex w-full flex-col items-center justify-center gap-4 rounded-lg bg-gradient-to-br from-violet-500 to-indigo-600 p-8 text-center text-white shadow-lg shadow-violet-100 ring-4 ring-white hover:shadow-xl">
                                <i class="fa-solid fa-flask-vial text-4xl" aria-hidden="true"></i>
                                <span class="text-xl font-bold">石化中間原料</span>
                                <span class="text-sm text-violet-100">例如乙烯、丙烯、丁二烯、苯、酚等</span>
                            </button>
                        </div>
                    </article>

                    <!-- 下游：九項終端化學製品 -->
                    <article class="flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 font-bold text-teal-700 shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm font-normal text-slate-400">塑橡膠與化學製品</span></h4>
                        </div>
                        <div class="petro-flow-card grid flex-1 grid-cols-1 content-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm sm:grid-cols-2">
                            <?php foreach ($downstreamSectors as $sector => $icon): ?>
                                <button type="button" onclick="petroToggleCompanyList('<?= $sector ?>', 'teal')" class="petro-node flex w-full items-center gap-3 rounded-lg border border-teal-200 bg-white p-3 text-left shadow-sm hover:border-teal-400 hover:bg-teal-50 hover:shadow-md">
                                    <span class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-teal-50 text-teal-600"><i class="<?= $icon ?>" aria-hidden="true"></i></span>
                                    <span class="text-sm font-bold text-slate-700"><?= $sector ?></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </article>
                </div>

                <div class="relative z-20 mt-16 space-y-6">
                    <h4 class="flex items-center gap-2 border-l-4 border-violet-600 pl-4 text-xl font-bold text-slate-700">點擊上方圖表查看公司列表</h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從原油、石化中間原料到塑橡膠與化學製品，掌握石化產業鏈脈絡");
    </script>
</body>
</html>
