<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "造紙";
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
        .paper-flow-card { min-height: 260px; }
        .paper-node { transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease; }
        .paper-node:hover { transform: translateY(-4px); }
        .paper-node:focus-visible { outline: 3px solid #38bdf8; outline-offset: 3px; }
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
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-emerald-600 via-sky-600 to-indigo-600"></div>

                <div id="main_papermaking_panel" class="grid grid-cols-1 gap-10 lg:grid-cols-3 lg:gap-12" aria-label="造紙產業供應鏈：上游、中游、下游">
                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700 shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm font-normal text-slate-400">原料供應</span></h4>
                        </div>
                        <div class="paper-flow-card relative flex flex-1 items-center rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <button type="button" onclick="toggleCompanyList(companySectors, '紙漿、紙類原料', 'emerald')" class="paper-node relative z-10 flex w-full items-center justify-center gap-4 rounded-lg border border-emerald-200 bg-white p-6 text-center shadow-sm hover:border-emerald-400 hover:shadow-md">
                                <i class="fa-solid fa-seedling text-2xl text-emerald-600" aria-hidden="true"></i>
                                <span class="text-lg font-bold text-slate-700">紙漿、紙類原料</span>
                            </button>
                        </div>
                    </article>

                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 font-bold text-sky-700 shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm font-normal text-slate-400">製造與加工</span></h4>
                        </div>
                        <div class="paper-flow-card relative flex flex-1 rounded-xl border border-slate-200 bg-slate-50 p-4 shadow-sm sm:p-5">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <div class="flex w-full flex-col justify-center gap-3 rounded-lg bg-sky-700/35 p-3 sm:p-4">
                                <h5 class="text-center text-lg font-bold leading-snug text-slate-800">紙器及紙類製造印刷、加工</h5>
                                <button type="button" onclick="toggleCompanyList(companySectors, '文化用紙', 'sky')" class="paper-node rounded-lg border border-slate-200 bg-white px-4 py-3 text-center text-lg font-bold text-slate-700 shadow-sm hover:border-sky-400 hover:shadow-md">文化用紙</button>
                                <button type="button" onclick="toggleCompanyList(companySectors, '工業用紙', 'sky')" class="paper-node rounded-lg border border-slate-200 bg-white px-4 py-3 text-center text-lg font-bold text-slate-700 shadow-sm hover:border-sky-400 hover:shadow-md">工業用紙</button>
                                <button type="button" onclick="toggleCompanyList(companySectors, '家庭用紙', 'sky')" class="paper-node rounded-lg border border-slate-200 bg-white px-4 py-3 text-center text-lg font-bold text-slate-700 shadow-sm hover:border-sky-400 hover:shadow-md">家庭用紙</button>
                            </div>
                        </div>
                    </article>

                    <article class="flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-100 font-bold text-indigo-700 shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm font-normal text-slate-400">市場通路</span></h4>
                        </div>
                        <div class="paper-flow-card flex flex-1 items-center rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <button type="button" onclick="toggleCompanyList(companySectors, '銷售、進出口業務', 'indigo')" class="paper-node flex w-full items-center justify-center gap-4 rounded-lg border border-indigo-200 bg-white p-6 text-center shadow-sm hover:border-indigo-400 hover:shadow-md">
                                <i class="fa-solid fa-ship text-2xl text-indigo-600" aria-hidden="true"></i>
                                <span class="text-lg font-bold text-slate-700">銷售、進出口業務</span>
                            </button>
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
        banner("<?= $category ?>產業供應鏈", "掌握造紙產業從紙漿原料、製造加工到市場通路的完整脈絡");
    </script>
</body>
</html>
