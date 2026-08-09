<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "人工智慧";
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
        const iconMap = new Map([
            [ "應用與服務", new Map([
                [ "系統整合", "fa-solid fa-cubes text-sky-500" ],
                [ "顧問諮詢", "fa-solid fa-user-tie text-sky-500" ],
                [ "領域解決方案", "fa-solid fa-lightbulb text-sky-500" ],
                [ "智慧設備", "fa-solid fa-mobile-screen-button text-sky-500" ],
            ])],
            [ "核心技術", new Map([
                [ "機器學習", "fa-solid fa-robot text-indigo-500" ],
                [ "電腦視覺", "fa-solid fa-eye text-indigo-500" ],
                [ "自然語言處理", "fa-solid fa-comments text-indigo-500" ],
                [ "移動控制", "fa-solid fa-sliders text-indigo-500" ],
            ])],
            [ "運算資源", new Map([
                [ "運算設備", "fa-solid fa-server text-emerald-500" ],
                [ "雲端平台", "fa-solid fa-cloud text-emerald-500" ],
                [ "資料處理", "fa-solid fa-database text-emerald-500" ],
            ])],
        ]);
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
                <span class="bg-indigo-500 w-2 h-8 rounded-full"></span> 人工智慧產業鏈簡介
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-sky-500 via-indigo-500 to-emerald-500 rounded-t-2xl"></div>
                
                <div id="main_ai_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-8 relative mb-12">
                    
                    <!-- 區塊一：應用與服務 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="bg-slate-100/80 rounded-t-xl p-4 border-t border-x border-slate-200 text-center">
                            <h4 class="text-lg font-bold text-slate-700">應用與服務</h4>
                        </div>
                        <div class="bg-slate-50 rounded-b-xl p-6 border border-slate-200 h-full shadow-sm">
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
                                
                                <div onclick="toggleCompanyList(companySectors, '系統整合', 'sky')" class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-cubes"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">系統整合</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '顧問諮詢', 'sky')" class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-user-tie"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">顧問諮詢</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '領域解決方案', 'sky')" class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-lightbulb"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">領域解決方案</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '智慧設備', 'sky')" class="cursor-pointer bg-white border border-slate-200 hover:border-sky-400 hover:bg-sky-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-sky-50 text-sky-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-mobile-screen-button"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">智慧設備</h6>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 區塊二：核心技術 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="bg-slate-100/80 rounded-t-xl p-4 border-t border-x border-slate-200 text-center">
                            <h4 class="text-lg font-bold text-slate-700">核心技術</h4>
                        </div>
                        <div class="bg-slate-50 rounded-b-xl p-6 border border-slate-200 h-full shadow-sm">
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
                                
                                <div onclick="toggleCompanyList(companySectors, '機器學習', 'indigo')" class="cursor-pointer bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-robot"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">機器學習</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '電腦視覺', 'indigo')" class="cursor-pointer bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-eye"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">電腦視覺</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '自然語言處理', 'indigo')" class="cursor-pointer bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-comments"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">自然語言處理</h6>
                                </div>

                                <!-- 屏蔽：大型語言模型 -->
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-brain"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-400 text-base">大型語言模型 (無上市公司)</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '移動控制', 'indigo')" class="cursor-pointer bg-white border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-sliders"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">移動控制</h6>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- 區塊三：運算資源 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="bg-slate-100/80 rounded-t-xl p-4 border-t border-x border-slate-200 text-center">
                            <h4 class="text-lg font-bold text-slate-700">運算資源</h4>
                        </div>
                        <div class="bg-slate-50 rounded-b-xl p-6 border border-slate-200 h-full shadow-sm">
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
                                
                                <div onclick="toggleCompanyList(companySectors, '運算設備', 'emerald')" class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-server"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">運算設備</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '雲端平台', 'emerald')" class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-cloud"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">雲端平台</h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '資料處理', 'emerald')" class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-database"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base">資料處理</h6>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
                
                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-indigo-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從應用與服務、核心技術到運算資源，掌握人工智慧產業鏈脈絡");
    </script>
</body>
</html>