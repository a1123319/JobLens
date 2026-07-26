<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "汽電共生";
$companies = getCompanies($category);
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業鏈</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js?v=<?= filemtime(__DIR__ . '/script.js') ?>"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode($companies, JSON_UNESCAPED_UNICODE) ?>);
        const companyChainNodes = new Map();
        const chainLabels = ['配電系統', '變壓器', '電纜', '營造設置', '系統整合', '設備維護', '發電營運'];
        const categoryCompanies = Array.from(companySectors.values()).flat();

        for (const label of chainLabels) {
            const sectorMatches = categoryCompanies.filter((company) => company.Sector === label);
            const subsectorMatches = categoryCompanies.filter((company) => company.Subsector === label);
            companyChainNodes.set(label, sectorMatches.length > 0 ? sectorMatches : subsectorMatches);
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');

        body { font-family: 'Noto Sans TC', sans-serif; }
        .chain-node { transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease; }
        .chain-node:not([aria-disabled="true"]):hover { transform: translateY(-4px); }
        .chain-node:focus-visible { outline: 3px solid #0ea5e9; outline-offset: 3px; }
        .disabled-node { cursor: not-allowed; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto space-y-12 px-4 py-8">
        <section id="supply-chain-overview" class="scroll-mt-24" aria-labelledby="supply-chain-title">
            <h1 id="supply-chain-title" class="mb-8 flex items-center gap-2 text-2xl font-bold text-slate-800">
                <span class="h-8 w-2 rounded-full bg-cyan-500"></span> 供應鏈結構圖
            </h1>

            <div class="relative overflow-visible rounded-2xl border border-slate-100 bg-white p-5 pb-12 shadow-xl sm:p-8">
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-cyan-600 via-sky-500 to-emerald-500"></div>

                <div id="main_cogeneration_panel" class="grid grid-cols-1 gap-10 lg:grid-cols-3 lg:gap-12" aria-label="汽電共生產業鏈">
                    <section class="relative flex flex-col" aria-labelledby="upstream-title">
                        <div class="mb-6 flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 font-bold text-cyan-700 shadow-sm">1</span>
                            <h3 id="upstream-title" class="text-xl font-bold text-slate-700">上游</h3>
                        </div>
                        <div class="relative flex h-full flex-col gap-4 rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>

                            <div class="chain-node disabled-node flex items-center gap-4 rounded-lg border border-slate-200 bg-slate-100 p-4 opacity-70 shadow-sm" aria-disabled="true">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 text-lg text-slate-400"><i class="fa-solid fa-fan"></i></span>
                                <span class="font-bold text-slate-500">渦輪發電機</span>
                            </div>

                            <div class="rounded-xl border-2 border-cyan-300 bg-cyan-50/50 p-4" aria-label="電力設施">
                                <div class="mb-3 flex items-center gap-2 font-bold text-cyan-800">
                                    <i class="fa-solid fa-bolt" aria-hidden="true"></i>
                                    <span>電力設施</span>
                                </div>
                                <div class="space-y-2">
                                    <button type="button" onclick="toggleCompanyList(companyChainNodes, '配電系統', 'cyan')" class="chain-node flex w-full items-center gap-3 rounded-lg border border-cyan-200 bg-white p-3 text-left text-sm font-medium text-slate-700 shadow-sm hover:border-cyan-400 hover:bg-cyan-50 hover:shadow-md"><i class="fa-solid fa-network-wired w-5 text-center text-cyan-600"></i>配電系統</button>
                                    <button type="button" onclick="toggleCompanyList(companyChainNodes, '變壓器', 'cyan')" class="chain-node flex w-full items-center gap-3 rounded-lg border border-cyan-200 bg-white p-3 text-left text-sm font-medium text-slate-700 shadow-sm hover:border-cyan-400 hover:bg-cyan-50 hover:shadow-md"><i class="fa-solid fa-plug-circle-bolt w-5 text-center text-cyan-600"></i>變壓器</button>
                                    <button type="button" onclick="toggleCompanyList(companyChainNodes, '電纜', 'cyan')" class="chain-node flex w-full items-center gap-3 rounded-lg border border-cyan-200 bg-white p-3 text-left text-sm font-medium text-slate-700 shadow-sm hover:border-cyan-400 hover:bg-cyan-50 hover:shadow-md"><i class="fa-solid fa-link w-5 text-center text-cyan-600"></i>電纜</button>
                                    <div class="disabled-node flex items-center gap-3 rounded-lg border border-cyan-100 bg-white p-3 text-sm font-medium text-slate-500" aria-disabled="true"><i class="fa-solid fa-screwdriver-wrench w-5 text-center text-slate-400"></i>其他配件</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="relative flex flex-col" aria-labelledby="midstream-title">
                        <div class="mb-6 flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-sky-100 font-bold text-sky-700 shadow-sm">2</span>
                            <h3 id="midstream-title" class="text-xl font-bold text-slate-700">中游</h3>
                        </div>
                        <div class="relative flex h-full flex-col justify-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>

                            <div class="chain-node disabled-node flex items-center gap-4 rounded-lg border border-slate-200 bg-slate-100 p-4 opacity-70 shadow-sm" aria-disabled="true">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 text-lg text-slate-400"><i class="fa-solid fa-compass-drafting"></i></span>
                                <span class="font-bold text-slate-500">規劃設計</span>
                            </div>
                            <button type="button" onclick="toggleCompanyList(companyChainNodes, '營造設置', 'sky')" class="chain-node flex items-center gap-4 rounded-lg border border-sky-200 bg-white p-4 text-left shadow-sm hover:border-sky-400 hover:bg-sky-50 hover:shadow-md">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sky-50 text-lg text-sky-600"><i class="fa-solid fa-helmet-safety"></i></span>
                                <span class="font-bold text-slate-700">營造設置</span>
                            </button>
                            <button type="button" onclick="toggleCompanyList(companyChainNodes, '系統整合', 'sky')" class="chain-node flex items-center gap-4 rounded-lg border border-sky-200 bg-white p-4 text-left shadow-sm hover:border-sky-400 hover:bg-sky-50 hover:shadow-md">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-sky-50 text-lg text-sky-600"><i class="fa-solid fa-diagram-project"></i></span>
                                <span class="font-bold text-slate-700">系統整合</span>
                            </button>
                        </div>
                    </section>

                    <section class="flex flex-col" aria-labelledby="downstream-title">
                        <div class="mb-6 flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700 shadow-sm">3</span>
                            <h3 id="downstream-title" class="text-xl font-bold text-slate-700">下游</h3>
                        </div>
                        <div class="flex h-full flex-col justify-center gap-4 rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <button type="button" onclick="toggleCompanyList(companyChainNodes, '設備維護', 'emerald')" class="chain-node flex items-center gap-4 rounded-lg border border-emerald-200 bg-white p-4 text-left shadow-sm hover:border-emerald-400 hover:bg-emerald-50 hover:shadow-md">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-lg text-emerald-600"><i class="fa-solid fa-screwdriver-wrench"></i></span>
                                <span class="font-bold text-slate-700">設備維護</span>
                            </button>
                            <button type="button" onclick="toggleCompanyList(companyChainNodes, '發電營運', 'emerald')" class="chain-node flex items-center gap-4 rounded-lg border border-emerald-200 bg-white p-4 text-left shadow-sm hover:border-emerald-400 hover:bg-emerald-50 hover:shadow-md">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-lg text-emerald-600"><i class="fa-solid fa-industry"></i></span>
                                <span class="font-bold text-slate-700">發電營運</span>
                            </button>
                        </div>
                    </section>
                </div>

                <div class="relative z-20 mt-16 border-t border-slate-100 pt-8">
                    <h2 class="mb-5 flex items-center gap-2 border-l-4 border-cyan-500 pl-4 text-xl font-bold text-slate-700">企業列表</h2>
                    <div id="company-list" aria-live="polite"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業鏈", "從設備供應、工程整合到電廠營運，探索汽電共生產業鏈中的企業");
    </script>
</body>
</html>
