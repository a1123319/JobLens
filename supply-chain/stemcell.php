<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "再生醫療";
$companies = getCompanies($category);
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js?v=<?= filemtime(__DIR__ . '/script.js') ?>"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode($companies, JSON_UNESCAPED_UNICODE) ?>);
        const companyChainNodes = new Map();
        const chainLabels = ['幹細胞收集儲存', '幹細胞開發', '臨床實驗、移植技術、疾病治療'];
        const categoryCompanies = Array.from(companySectors.values()).flat();

        for (const label of chainLabels) {
            const sectorMatches = categoryCompanies.filter((company) => company.Sector === label);
            const subsectorMatches = categoryCompanies.filter((company) => company.Subsector === label);
            companyChainNodes.set(label, sectorMatches.length > 0 ? sectorMatches : subsectorMatches);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .chart-container { position: relative; height: 300px; width: 100%; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-cyan-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl"></div>

                <div id="main_ic_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    <!-- 上游 -->
                    <div class="chain-col relative group flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">
                                上游 <span class="text-sm text-slate-400 font-normal">幹細胞收集儲存</span>
                            </h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-8 relative z-10">
                                <div onclick="toggleCompanyList(companyChainNodes, '幹細胞收集儲存', 'cyan')"
                                    class="cursor-pointer bg-gradient-to-br from-cyan-500 to-cyan-600 text-white rounded-lg p-6 shadow-lg shadow-cyan-200 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white">
                                    <div class="mb-2"><i class="fa-solid fa-box-archive text-3xl"></i></div>
                                    <h5 class="font-bold text-xl">幹細胞收集儲存</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 中游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">
                                中游 <span class="text-sm text-slate-400 font-normal">幹細胞開發</span>
                            </h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-6 h-full relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companyChainNodes, '幹細胞開發', 'blue')"
                                    class="cursor-pointer bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg p-6 shadow-lg shadow-blue-200 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white mt-2">
                                    <div class="mb-2"><i class="fa-solid fa-dna text-3xl"></i></div>
                                    <h5 class="font-bold text-xl">幹細胞開發</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 下游 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">
                                下游 <span class="text-sm text-slate-400 font-normal">臨床實驗、移植技術、疾病治療</span>
                            </h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
                                <div onclick="toggleCompanyList(companyChainNodes, '臨床實驗、移植技術、疾病治療', 'purple')"
                                    class="cursor-pointer bg-gradient-to-br from-purple-500 to-purple-600 text-white rounded-lg p-6 shadow-lg shadow-purple-200 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white">
                                    <div class="mb-2"><i class="fa-solid fa-hand-holding-medical text-3xl"></i></div>
                                    <h5 class="font-bold text-xl leading-relaxed">臨床實驗、移植技術、疾病治療</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-cyan-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>

    <script>
        banner("<?= $category ?>產業鏈", "從上游到下游，全面透視產業鏈夥伴");
    </script>
</body>
</html>
