<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "食品";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業供應鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js?v=20260807"></script>
    <script>
        const foodCompanies = <?= json_encode(getCompanies($category), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
        const companySectors = new Map();

        for (const company of foodCompanies) {
            for (const node of [company.Sector, company.Subsector]) {
                if (!node) {
                    continue;
                }

                if (!companySectors.has(node)) {
                    companySectors.set(node, []);
                }

                companySectors.get(node).push(company);
            }
        }

        function foodToggleCompanyList(sector, color) {
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
        .food-node { transition: transform 180ms ease, box-shadow 180ms ease, border-color 180ms ease; }
        .food-node:hover { transform: translateY(-4px); }
        .food-node:focus-visible { outline: 3px solid #f59e0b; outline-offset: 3px; }
        .food-flow-card { min-height: 300px; }
        #company-list > .grid > .company-details:only-child { grid-column: 1 / -1; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto space-y-12 px-4 py-8">
        <section id="supply-chain-overview" class="scroll-mt-24" aria-labelledby="supply-chain-title">
            <h3 id="supply-chain-title" class="mb-8 flex items-center gap-2 text-2xl font-bold text-slate-800">
                <span class="h-8 w-2 rounded-full bg-amber-500"></span> 供應鏈結構圖
            </h3>

            <div class="relative overflow-visible rounded-2xl border border-slate-100 bg-white p-5 pb-12 shadow-xl sm:p-8">
                <div class="absolute left-0 top-0 h-2 w-full rounded-t-2xl bg-gradient-to-r from-emerald-500 via-amber-400 to-orange-500"></div>

                <div id="main_food_panel" class="grid grid-cols-1 gap-10 lg:grid-cols-3 lg:gap-12" aria-label="食品產業供應鏈：上游、中游、下游">
                    <!-- 上游：原物料 -->
                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700 shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm font-normal text-slate-400">原料供應</span></h4>
                        </div>
                        <div class="food-flow-card relative flex flex-1 items-center rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <button type="button" onclick="foodToggleCompanyList('原物料', 'emerald')" class="food-node relative z-10 flex w-full flex-col items-center justify-center gap-4 rounded-lg border border-emerald-200 bg-white p-10 text-center shadow-sm hover:border-emerald-400 hover:bg-emerald-50 hover:shadow-md">
                                <i class="fa-solid fa-wheat-awn text-4xl text-emerald-600" aria-hidden="true"></i>
                                <span class="text-2xl font-bold text-slate-700">原物料</span>
                                <span class="text-sm text-slate-400">穀物、油脂、糖、肉品與水產等</span>
                            </button>
                        </div>
                    </article>

                    <!-- 中游：加工食品 -->
                    <article class="relative flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700 shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm font-normal text-slate-400">加工製造</span></h4>
                        </div>
                        <div class="food-flow-card relative flex flex-1 items-center rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <div class="pointer-events-none absolute left-full top-1/2 z-0 hidden h-1 w-12 -translate-y-1/2 bg-slate-300 lg:block"></div>
                            <div class="pointer-events-none absolute -right-12 top-1/2 z-10 hidden h-3 w-3 -translate-y-1/2 translate-x-1/2 rounded-full border-2 border-white bg-slate-300 lg:block"></div>
                            <button type="button" onclick="foodToggleCompanyList('加工食品', 'amber')" class="food-node relative z-10 flex w-full flex-col items-center justify-center gap-4 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 p-10 text-center text-white shadow-lg shadow-amber-100 ring-4 ring-white hover:shadow-xl">
                                <i class="fa-solid fa-industry text-4xl" aria-hidden="true"></i>
                                <span class="text-2xl font-bold">加工食品</span>
                                <span class="text-sm text-amber-100">穀類、食用油脂、調理食品與飲品</span>
                            </button>
                        </div>
                    </article>

                    <!-- 下游：四項終端食品與餐飲通路 -->
                    <article class="flex flex-col">
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 font-bold text-orange-700 shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm font-normal text-slate-400">終端食品與通路</span></h4>
                        </div>
                        <div class="food-flow-card flex flex-1 flex-col justify-center gap-3 rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                            <button type="button" onclick="foodToggleCompanyList('冷凍、罐頭、脫水、醃漬食品', 'orange')" class="food-node flex w-full items-center gap-4 rounded-lg border border-orange-200 bg-white p-4 text-left shadow-sm hover:border-orange-400 hover:bg-orange-50 hover:shadow-md">
                                <i class="fa-solid fa-box-open w-8 text-center text-xl text-orange-600" aria-hidden="true"></i>
                                <span class="font-bold text-slate-700">冷凍、罐頭、脫水、醃漬食品</span>
                            </button>
                            <button type="button" onclick="foodToggleCompanyList('乳製品', 'orange')" class="food-node flex w-full items-center gap-4 rounded-lg border border-orange-200 bg-white p-4 text-left shadow-sm hover:border-orange-400 hover:bg-orange-50 hover:shadow-md">
                                <i class="fa-solid fa-glass-water w-8 text-center text-xl text-orange-600" aria-hidden="true"></i>
                                <span class="font-bold text-slate-700">乳製品</span>
                            </button>
                            <button type="button" onclick="foodToggleCompanyList('營養食品', 'orange')" class="food-node flex w-full items-center gap-4 rounded-lg border border-orange-200 bg-white p-4 text-left shadow-sm hover:border-orange-400 hover:bg-orange-50 hover:shadow-md">
                                <i class="fa-solid fa-heart-pulse w-8 text-center text-xl text-orange-600" aria-hidden="true"></i>
                                <span class="font-bold text-slate-700">營養食品</span>
                            </button>
                            <button type="button" onclick="foodToggleCompanyList('餐飲連鎖', 'orange')" class="food-node flex w-full items-center gap-4 rounded-lg border border-orange-200 bg-white p-4 text-left shadow-sm hover:border-orange-400 hover:bg-orange-50 hover:shadow-md">
                                <i class="fa-solid fa-utensils w-8 text-center text-xl text-orange-600" aria-hidden="true"></i>
                                <span class="font-bold text-slate-700">餐飲連鎖</span>
                            </button>
                        </div>
                    </article>
                </div>

                <div class="relative z-20 mt-16 space-y-6">
                    <h4 class="flex items-center gap-2 border-l-4 border-amber-500 pl-4 text-xl font-bold text-slate-700">點擊上方圖表查看公司列表</h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從原物料、加工食品到終端食品與餐飲通路的完整產業鏈");
    </script>
</body>
</html>
