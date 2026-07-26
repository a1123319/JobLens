<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "大數據";
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

                <div id="main_big_data_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-6 relative mb-12">

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">
                                1</div>
                            <h4 class="text-lg font-bold text-slate-700">應用與服務</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div onclick="toggleCompanyList(companySectors, '系統整合', 'cyan')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-diagram-project"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">系統整合</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '顧問諮詢', 'cyan')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-comments"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">顧問諮詢</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '領域解決方案', 'cyan')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-cyan-400 hover:bg-cyan-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-cyan-50 text-cyan-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-lightbulb"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">領域解決方案</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">
                                2</div>
                            <h4 class="text-lg font-bold text-slate-700">軟體暨工具</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div onclick="toggleCompanyList(companySectors, '資料庫', 'blue')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-database"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">資料庫</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '分析工具', 'blue')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-chart-line"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">分析工具</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '應用軟體', 'blue')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-window-maximize"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">應用軟體</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div
                                class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">
                                3</div>
                            <h4 class="text-lg font-bold text-slate-700">基礎資源</h4>
                        </div>
                        <div
                            class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-3">
                                <div onclick="toggleCompanyList(companySectors, '運算元件與設備', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-microchip"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">運算元件與設備</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '雲端平台', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-cloud"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">雲端平台</h6>
                                    </div>
                                </div>
                                <div onclick="toggleCompanyList(companySectors, '儲存處理', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3 transition-all flex items-center gap-3 hover:-translate-y-1 shadow-sm">
                                    <div
                                        class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-base flex-shrink-0">
                                        <i class="fa-solid fa-hard-drive"></i>
                                    </div>
                                    <div class="text-left">
                                        <h6 class="font-bold text-slate-700 text-sm">儲存處理</h6>
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
        banner("<?= $category ?>產業供應鏈", "從應用服務、軟體工具到基礎資源，全面透視大數據產業鏈夥伴");
    </script>
</body>
</html>
