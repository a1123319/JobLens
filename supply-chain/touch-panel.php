<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "觸控面板";
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
        .touch-node { transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease; }
        .touch-node:hover { transform: translateY(-3px); }
        .touch-node:focus-visible { outline: 3px solid #38bdf8; outline-offset: 3px; }
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
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-cyan-600 via-sky-600 to-indigo-600"></div>

                <div id="main_touch_panel_panel" class="grid grid-cols-1 gap-10 lg:grid-cols-3 lg:gap-12" aria-label="觸控面板產業供應鏈：上游、中游、下游">
                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 font-bold text-cyan-700 shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm font-normal text-slate-400">材料與零組件</span></h4>
                        </div>
                        <div class="relative flex flex-1 flex-col gap-5 rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm sm:p-5">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <div>
                                <p class="mb-3 text-sm font-bold tracking-wider text-cyan-800">基板與薄膜材料</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button" onclick="toggleCompanyList(companySectors, '玻璃基板', 'cyan')" class="touch-node rounded-lg border border-cyan-100 bg-white px-3 py-3 text-sm font-bold text-slate-700 shadow-sm hover:border-cyan-400">玻璃基板</button>
                                    <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-400 opacity-60">ITO導電玻璃 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                    <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-400 opacity-60">PET膜 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                    <button type="button" onclick="toggleCompanyList(companySectors, 'ITO導電薄膜', 'cyan')" class="touch-node rounded-lg border border-cyan-100 bg-white px-3 py-3 text-sm font-bold text-slate-700 shadow-sm hover:border-cyan-400">ITO導電薄膜</button>
                                    <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-400 opacity-60">ITO靶材 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                    <button type="button" onclick="toggleCompanyList(companySectors, '膠材', 'cyan')" class="touch-node rounded-lg border border-cyan-100 bg-white px-3 py-3 text-sm font-bold text-slate-700 shadow-sm hover:border-cyan-400">膠材</button>
                                </div>
                            </div>
                            <div class="border-t border-slate-200 pt-4">
                                <p class="mb-3 text-sm font-bold tracking-wider text-cyan-800">關鍵零組件</p>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-1">
                                    <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-400 opacity-60">印刷材料 <span class="font-normal">(油墨、無上市公司)</span></div>
                                    <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-3 text-center text-sm font-bold text-slate-400 opacity-60">軟性電路板 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                    <button type="button" onclick="toggleCompanyList(companySectors, '控制IC', 'cyan')" class="touch-node rounded-lg border border-slate-200 bg-white px-3 py-3 text-sm font-bold text-slate-700 shadow-sm hover:border-cyan-400">控制IC</button>
                                </div>
                            </div>
                        </div>
                    </article>

                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 font-bold text-sky-700 shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm font-normal text-slate-400">模組整合與製造</span></h4>
                        </div>
                        <div class="relative flex flex-1 rounded-xl border border-sky-200 bg-gradient-to-b from-sky-50 to-white p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <button type="button" onclick="toggleCompanyList(companySectors, '觸控面板', 'sky')" class="touch-node flex w-full flex-col items-center justify-center rounded-xl border border-sky-300 bg-white p-8 text-center shadow-md hover:border-sky-500 hover:shadow-lg">
                                <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-sky-100 text-3xl text-sky-700" aria-hidden="true"><i class="fa-solid fa-hand-pointer"></i></span>
                                <span class="text-2xl font-bold text-slate-800">觸控面板</span>
                                <span class="mt-2 text-sm font-normal text-slate-500">感測、貼合與模組製造</span>
                            </button>
                        </div>
                    </article>

                    <article class="flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700 shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm font-normal text-slate-400">終端應用</span></h4>
                        </div>
                        <div class="flex flex-1 flex-col rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm sm:p-5">
                            <p class="mb-3 text-sm font-bold tracking-wider text-indigo-800">終端設備與自助系統</p>
                            <div class="grid flex-1 grid-cols-2 content-start gap-3">
                                <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-4 text-center text-sm font-bold text-slate-400 opacity-60">行動電話 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-4 text-center text-sm font-bold text-slate-400 opacity-60">PDA <span class="block text-xs font-normal">(無上市公司)</span></div>
                                <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-4 text-center text-sm font-bold text-slate-400 opacity-60">衛星定位系統 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-4 text-center text-sm font-bold text-slate-400 opacity-60">工業用設備 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                <button type="button" onclick="toggleCompanyList(companySectors, '自動售票機', 'indigo')" class="touch-node rounded-lg border border-indigo-100 bg-white px-3 py-4 text-sm font-bold text-slate-700 shadow-sm hover:border-indigo-400">自動售票機</button>
                                <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-4 text-center text-sm font-bold text-slate-400 opacity-60">電子觸控白板 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                <div aria-disabled="true" class="cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-4 text-center text-sm font-bold text-slate-400 opacity-60">資訊收集設備 <span class="block text-xs font-normal">(無上市公司)</span></div>
                                <button type="button" onclick="toggleCompanyList(companySectors, '金融提款機(ATM)', 'indigo')" class="touch-node rounded-lg border border-indigo-100 bg-white px-3 py-4 text-sm font-bold text-slate-700 shadow-sm hover:border-indigo-400">金融提款機 <span class="block text-xs font-normal text-slate-500">(ATM)</span></button>
                                <div aria-disabled="true" class="col-span-2 cursor-not-allowed rounded-lg border border-slate-200 bg-slate-100 px-3 py-4 text-center text-sm font-bold text-slate-400 opacity-60">公共資訊查詢站 <span class="font-normal">(Kiosk、無上市公司)</span></div>
                            </div>
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
        banner("<?= $category ?>產業供應鏈", "掌握觸控面板從材料、模組製造到終端應用的產業脈絡");
    </script>
</body>
</html>
