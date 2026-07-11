<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "油電燃氣";
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
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category)) ?>);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');

        body { font-family: 'Noto Sans TC', sans-serif; }
        .energy-node { min-height: 150px; transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease; }
        .energy-node:hover { transform: translateY(-4px); }
        .energy-node:active { transform: translateY(-1px); }
        .energy-node:focus-visible { outline: 3px solid #38bdf8; outline-offset: 4px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24" aria-labelledby="supply-chain-title">
            <h3 id="supply-chain-title" class="mb-8 flex items-center gap-2 text-2xl font-bold text-slate-800">
                <span class="h-8 w-2 rounded-full bg-teal-600"></span> 供應鏈結構圖
            </h3>

            <div class="relative overflow-visible rounded-2xl border border-slate-100 bg-white p-5 pb-12 shadow-xl sm:p-8">
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-cyan-700 via-teal-600 to-emerald-700"></div>

                <div class="mb-6 text-center">
                    <p class="text-sm font-medium tracking-widest text-teal-700">主要供應與零售通路</p>
                </div>
                <div id="main_oil_gas_electricity_panel" class="relative grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-16" aria-label="油電燃氣產業供應鏈">
                    <div class="pointer-events-none absolute left-1/2 top-1/2 z-10 hidden h-1 w-16 -translate-x-1/2 -translate-y-1/2 bg-slate-300 md:block"></div>
                    <div class="pointer-events-none absolute left-1/2 top-1/2 z-20 hidden h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border-2 border-slate-200 bg-white text-teal-600 md:flex" aria-hidden="true">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>

                    <button type="button" onclick="toggleCompanyList(companySectors, '天然瓦斯供應', 'cyan')" class="energy-node group relative z-0 flex items-center gap-5 rounded-xl border border-cyan-200 bg-white p-6 text-left shadow-sm hover:border-cyan-500 hover:shadow-lg">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-cyan-50 text-2xl text-cyan-700 transition-colors group-hover:bg-cyan-100" aria-hidden="true">
                            <i class="fa-solid fa-fire-flame-simple"></i>
                        </span>
                        <span>
                            <span class="block text-xl font-bold text-slate-800">天然瓦斯供應</span>
                            <span class="mt-1 block text-sm font-normal text-slate-500">天然氣輸配與供應</span>
                        </span>
                    </button>

                    <button type="button" onclick="toggleCompanyList(companySectors, '加油站', 'emerald')" class="energy-node group relative z-0 flex items-center gap-5 rounded-xl border border-emerald-200 bg-white p-6 text-left shadow-sm hover:border-emerald-500 hover:shadow-lg">
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-2xl text-emerald-700 transition-colors group-hover:bg-emerald-100" aria-hidden="true">
                            <i class="fa-solid fa-gas-pump"></i>
                        </span>
                        <span>
                            <span class="block text-xl font-bold text-slate-800">加油站</span>
                            <span class="mt-1 block text-sm font-normal text-slate-500">零售燃料通路</span>
                        </span>
                    </button>
                </div>

                <div class="relative z-20 mt-16 space-y-6">
                    <h4 class="flex items-center gap-2 border-l-4 border-teal-600 pl-4 text-xl font-bold text-slate-700">點擊上方圖表查看公司列表</h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "掌握天然瓦斯供應與加油站通路的產業脈絡");
    </script>
</body>
</html>
