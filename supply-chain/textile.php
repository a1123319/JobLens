<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "紡織";
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
            ['石化原料', 'fa-solid fa-flask text-blue-500'],
            ['人造纖維產品', 'fa-solid fa-industry text-blue-500'],
            ['天然纖維產品', 'fa-solid fa-seedling text-blue-500'],
            ['化學助劑', 'fa-solid fa-vial text-blue-500'],
            ['紡紗', 'fa-solid fa-scroll text-blue-500'],
            ['織布', 'fa-solid fa-layer-group text-blue-500'],
            ['染整', 'fa-solid fa-fill-drip text-purple-500'],
            ['成衣及其它家居紡織類品', 'fa-solid fa-shirt text-purple-500']
        ]);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* TPEx 與 JobLens 融合的精緻藍色高亮容器 */
        .joblens-frame {
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        /* 跨區塊的實體水平連接橋 */
        .joblens-connector-bar::before {
            content: "";
            display: inline-block;
            position: absolute;
            background-color: inherit;
            right: 24px;
            width: 24px;
            height: inherit;
        }

        .joblens-connector-bar::after {
            content: "";
            display: inline-block;
            position: absolute;
            background-color: inherit;
            left: 24px;
            width: 24px;
            height: inherit;
        }

        .joblens-connector-bar {
            display: inline-block;
            height: 38px;
            width: 24px;
            position: relative;
            bottom: 70px;
            align-self: center;
            flex-shrink: 0;
        }
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

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-x-auto">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-cyan-400 via-blue-500 to-purple-500 rounded-t-2xl"></div>

                <!-- 嚴格依循圖中排版結構（上中下游、石化原料與人造纖維藍色容器及連接橋樑）並套用 JobLens 現代化 UI 風格 -->
                <div id="main_textile_panel" class="flex items-stretch justify-center gap-0 mb-12">
                    
                    <!-- 1. 【上游】 -->
                    <div class="chain-col flex flex-col w-[200px] flex-shrink-0">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-lg font-bold text-slate-700">上游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            
                            <!-- 藍色外框容器 (石化原料) -->
                            <div class="joblens-frame bg-blue-300 h-full flex items-center justify-center">
                                <div onclick="toggleCompanyList(companySectors, '石化原料', 'blue')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex flex-col items-center justify-center gap-1.5 hover:-translate-y-1 shadow-sm text-center h-full w-full group">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-flask"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-800">石化原料</h6>
                                    <p class="text-[11px] text-slate-400 font-normal leading-snug">
                                        (例如純對苯二甲酸、乙二醇、己內醯胺、丙烯腈等)
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 上游石化原料與中游人造纖維之間的實體連接橋 -->
                    <div class="joblens-connector-bar bg-blue-300"></div>

                    <!-- 2. 【中游】 -->
                    <div class="chain-col flex flex-col flex-1 min-w-[500px] max-w-[560px] flex-shrink-0">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-lg font-bold text-slate-700">中游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            
                            <!-- 中游內部: 第一排為橫向 3 個卡片，接著向下延伸至紡紗與織布 -->
                            <div class="flex flex-col gap-3">
                                
                                <!-- 頂部第 1 列：並排 3 個按鈕 (人造纖維產品、天然纖維產品、化學助劑) -->
                                <div class="grid grid-cols-3 gap-2 items-stretch">
                                    
                                    <!-- 人造纖維產品 (藍色外框容器，與上游連接橋無縫接軌) -->
                                    <div class="joblens-frame bg-blue-300 h-[140px] flex items-center justify-center">
                                        <div onclick="toggleCompanyList(companySectors, '人造纖維產品', 'blue')"
                                            class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-2 transition-all flex flex-col items-center justify-center gap-1 hover:-translate-y-1 shadow-sm text-center h-full w-full group">
                                            <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs flex-shrink-0 group-hover:scale-110 transition-transform">
                                                <i class="fa-solid fa-industry"></i>
                                            </div>
                                            <h6 class="font-bold text-slate-800">人造纖維產品</h6>
                                            <p class="text-xs text-slate-400 font-normal leading-tight">
                                                (例如尼龍纖維、聚酯纖維、嫘縈纖維等)
                                            </p>
                                        </div>
                                    </div>

                                    <!-- 天然纖維產品 -->
                                    <div onclick="toggleCompanyList(companySectors, '天然纖維產品', 'blue')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-2 transition-all flex flex-col items-center justify-center gap-1 hover:-translate-y-1 shadow-sm text-center h-[140px] group">
                                        <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-seedling"></i>
                                        </div>
                                        <h6 class="font-bold text-slate-700 text">天然纖維產品</h6>
                                        <p class="text-xs text-slate-400 font-normal leading-tight">
                                            (例如純棉、純羊毛等)
                                        </p>
                                    </div>

                                    <!-- 化學助劑 -->
                                    <div onclick="toggleCompanyList(companySectors, '化學助劑', 'blue')"
                                        class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-2 transition-all flex flex-col items-center justify-center gap-1.5 hover:-translate-y-1 shadow-sm text-center h-[140px] group">
                                        <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-xs flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <i class="fa-solid fa-vial"></i>
                                        </div>
                                        <h6 class="font-bold text-slate-700">化學助劑</h6>
                                    </div>

                                </div>

                                <!-- 向下箭頭 1 -->
                                <div class="flex items-center justify-center text-slate-300 text-xl font-bold py-0.5 select-none">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>

                                <!-- 紡紗 -->
                                <div onclick="toggleCompanyList(companySectors, '紡紗', 'blue')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm w-full text-center group">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-scroll"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base tracking-wider">紡紗</h6>
                                </div>

                                <!-- 向下箭頭 2 -->
                                <div class="flex items-center justify-center text-slate-300 text-xl font-bold py-0.5 select-none">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>

                                <!-- 織布 -->
                                <div onclick="toggleCompanyList(companySectors, '織布', 'blue')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg p-3 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm w-full text-center group">
                                    <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center text-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base tracking-wider">織布</h6>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- 中游至下游的間隔 -->
                    <div class="w-6 flex-shrink-0"></div>

                    <!-- 3. 【下游】 -->
                    <div class="chain-col flex flex-col flex-shrink-0">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-lg font-bold text-slate-700">下游</h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            
                            <!-- 下游內部: 由上至下 (染整 -> 成衣及其它家居紡織類品) -->
                            <div class="flex flex-col gap-3">
                                
                                <!-- 染整 -->
                                <div onclick="toggleCompanyList(companySectors, '染整', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3.5 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm w-full text-center group">
                                    <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-fill-drip"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base tracking-wider">染整</h6>
                                </div>

                                <!-- 向下箭頭 3 -->
                                <div class="flex items-center justify-center text-slate-300 text-xl font-bold py-0.5 select-none">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>

                                <!-- 成衣及其它家居紡織類品 -->
                                <div onclick="toggleCompanyList(companySectors, '成衣及其它家居紡織類品', 'purple')"
                                    class="cursor-pointer bg-white border border-slate-200 hover:border-purple-400 hover:bg-purple-50 rounded-lg p-3.5 transition-all flex items-center justify-center gap-3 hover:-translate-y-1 shadow-sm w-full text-center group">
                                    <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center text-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                        <i class="fa-solid fa-shirt"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-base tracking-wider">
                                        成衣及其它家居紡織類品
                                    </h6>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                <!-- 點擊公司列表展開區 -->
                <div class="mt-16 space-y-6 relative z-20">
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
        banner("紡織產業供應鏈分析", "從石化原料、人造與天然纖維、紡紗織布到染整成衣，探索台灣紡織產業生態系");
    </script>
</body>
</html>
