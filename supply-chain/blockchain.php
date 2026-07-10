<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "區塊鏈";
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>供應鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js"></script>
    <script>
        const companySectors = fromCompanyDatabase(<?= json_encode(getCompanies($category)) ?>);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .chart-container { position: relative; height: 300px; width: 100%; }
        /* 隱藏捲軸但保持功能 */
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
                <div
                    class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl">
                </div>

                <div id="main_chain_panel" class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-6 relative mb-12">

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">
                                1</div>
                            <h4 class="text-lg font-bold text-slate-700">應用服務</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div onclick="toggleCompanyList(companySectors, '交易平台', 'cyan')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-right-left"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">交易平台</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '錢包/資產管理', 'cyan')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-wallet"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">錢包/資產管理</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '商業應用', 'cyan')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-briefcase"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">商業應用</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center font-bold shadow-sm">
                                2</div>
                            <h4 class="text-lg font-bold text-slate-700">解決方案提供</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div onclick="toggleCompanyList(companySectors, 'BaaS', 'sky')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-cloud"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">BaaS</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '系統整合', 'sky')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-diagram-project"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">系統整合</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '領域方案', 'sky')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-lightbulb"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">領域方案</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">
                                3</div>
                            <h4 class="text-lg font-bold text-slate-700">基礎技術開發</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div onclick="toggleCompanyList(companySectors, '底層平台', 'blue')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-cubes"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">底層平台</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '開發工具', 'sky')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-code"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">開發工具</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '監控管理', 'sky')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-gauge-high"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">監控管理</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold shadow-sm">
                                4</div>
                            <h4 class="text-lg font-bold text-slate-700">運算資源</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-3 flex items-center gap-3 shadow-sm cursor-not-allowed">
                                    <div
                                        class="w-9 h-9 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-microchip"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-400 text-sm">運算晶片 (無上市公司)</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '元件裝置', 'indigo')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-server"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">元件裝置</h6>
                                    </div>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-3 flex items-center gap-3 shadow-sm cursor-not-allowed">
                                    <div
                                        class="w-9 h-9 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-network-wired"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-400 text-sm">節點服務 (無上市公司)</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">
                                5</div>
                            <h4 class="text-lg font-bold text-slate-700">支援服務</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div onclick="toggleCompanyList(companySectors, '創投', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-seedling"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">創投</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '顧問諮詢', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-comments"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">顧問諮詢</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '媒體推廣', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-bullhorn"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">媒體推廣</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '資安審計', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">資安審計</h6>
                                    </div>
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
        banner("<?= $category ?>產業供應鏈", "從應用服務到支援服務，全面透視產業鏈夥伴");
    </script>
</body>
</html>