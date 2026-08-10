<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "連接器";
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
                <span class="bg-amber-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-400 via-amber-500 to-amber-600 rounded-t-2xl"></div>
                
                <div id="main_ic_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12 mt-4">
                    
                    <!-- 上游 -->
                    <div class="chain-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold shadow-sm">
                                1
                            </div>
                            <h4 class="text-xl font-bold text-slate-700">上游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10">
                                <!-- 可點擊項目 1 -->
                                <div onclick="toggleCompanyList(companySectors, '金屬材料', 'amber')"
                                     class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-lg p-4 transition-all flex items-center justify-center hover:-translate-y-1 shadow-sm">
                                    <h5 class="font-bold text-slate-700 text-base">金屬材料</h5>
                                </div>

                                <!-- 可點擊項目 2 -->
                                <div onclick="toggleCompanyList(companySectors, '電鍍材料', 'amber')"
                                     class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-lg p-4 transition-all flex items-center justify-center hover:-translate-y-1 shadow-sm">
                                    <h5 class="font-bold text-slate-700 text-base">電鍍材料</h5>
                                </div>

                                <!-- 可點擊項目 3 -->
                                <div onclick="toggleCompanyList(companySectors, '塑膠材料', 'amber')"
                                     class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-lg p-4 transition-all flex items-center justify-center hover:-translate-y-1 shadow-sm">
                                    <h5 class="font-bold text-slate-700 text-base">塑膠材料</h5>
                                </div>

                                <!-- 屏蔽項目（不可點擊） -->
                                <div class="bg-slate-100 border border-slate-200 rounded-lg p-4 text-center cursor-not-allowed opacity-75">
                                    <h5 class="font-bold text-slate-600 text-base">其他材料</h5>
                                    <span class="text-xs text-slate-400 block mt-0.5">(如鋅合金、陶瓷、玻璃)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 中游 -->
                    <div class="chain-col relative">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold shadow-sm">
                                2
                            </div>
                            <h4 class="text-xl font-bold text-slate-700">中游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10 h-full justify-center">
                                <!-- 可點擊項目（大卡片） -->
                                <div onclick="toggleCompanyList(companySectors, '連接器設計、組裝及製造', 'amber')"
                                     class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-lg p-6 transition-all flex items-center justify-center text-center hover:-translate-y-1 shadow-sm h-full min-h-[220px]">
                                    <h5 class="font-bold text-slate-800 text-lg leading-relaxed">連接器設計、<br>組裝及製造</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 下游 -->
                    <div class="chain-col">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center font-bold shadow-sm">
                                3
                            </div>
                            <h4 class="text-xl font-bold text-slate-700">下游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="flex flex-col gap-4 relative z-10 h-full justify-center">
                                <!-- 屏蔽項目（不可點擊） -->
                                <div class="bg-slate-100 border border-slate-200 rounded-lg p-6 flex items-center justify-center text-center cursor-not-allowed opacity-75 h-full min-h-[220px]">
                                    <h5 class="font-bold text-slate-600 text-lg">各類電子產品</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                
                <!-- 公司列表顯示區 -->
                <div class="mt-20 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-amber-500 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        banner("<?= $category ?>產業供應鏈", "從原材料、設計製造到終端電子產品應用，全面透視產業鏈夥伴");
    </script>
</body>
</html>