<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "水泥";
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
                <span class="bg-amber-500 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-500 via-orange-400 to-emerald-500 rounded-t-2xl"></div>
                
                <div id="main_cement_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative mb-12">
                    
                    <!-- 上游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">主要原料</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow flex flex-col justify-center">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 relative z-10">
                                <div onclick="toggleCompanyList(companySectors, '石灰石', 'amber')" class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 rounded-lg p-4 shadow-sm hover:shadow-md hover:bg-amber-50 transition-all text-center hover:-translate-y-1 flex items-center gap-4">
                                    <div class="text-slate-500 w-8 text-center"><i class="fa-solid fa-mountain text-xl"></i></div>
                                    <h5 class="font-bold text-slate-700">石灰石</h5>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="text-slate-400 w-8 text-center"><i class="fa-solid fa-earth-americas text-xl"></i></div>
                                    <h5 class="font-bold text-slate-400">黏土 (無上市公司)</h5>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="text-slate-400 w-8 text-center"><i class="fa-solid fa-trowel-bricks text-xl"></i></div>
                                    <h5 class="font-bold text-slate-400">矽砂 (無上市公司)</h5>
                                </div>
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="text-slate-400 w-8 text-center"><i class="fa-solid fa-dumpster text-xl"></i></div>
                                    <h5 class="font-bold text-slate-400">鐵渣 (無上市公司)</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <!-- 中游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">研磨與煅燒</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full relative shadow-sm hover:shadow-md transition-shadow">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
								<div class="grid grid-cols-2 gap-4 items-center">

									<div class="flex flex-col gap-3">
										<div onclick="toggleCompanyList(companySectors, '水泥生料', 'orange')" class="cursor-pointer bg-white border border-slate-200 hover:border-orange-400 hover:bg-orange-50 rounded-lg p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
											<div class="w-10 h-10 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center text-lg flex-shrink-0">
												<i class="fa-solid fa-blender"></i>
											</div>
											<h6 class="font-bold text-slate-700 text-sm">水泥生料</h6>
										</div>
										
										<div class="flex justify-center -my-1">
											<i class="fa-solid fa-arrow-down text-slate-400"></i>
										</div>
										
										<div onclick="toggleCompanyList(companySectors, '水泥熟料', 'orange')" class="cursor-pointer bg-white border border-slate-200 hover:border-orange-400 rounded-lg hover:bg-amber-50 p-3 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
											<div class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 flex items-center justify-center text-lg flex-shrink-0">
												<i class="fa-solid fa-fire"></i>
											</div>
											<h6 class="font-bold text-slate-700 text-sm">水泥熟料</h6>
										</div>
									</div>

									<div class="h-full flex flex-col justify-center">
										<div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-3 h-full flex items-center gap-4 shadow-sm justify-center lg:justify-start cursor-not-allowed">
											<div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
												<i class="fa-solid fa-cubes"></i>
											</div>
											<h6 class="font-bold text-slate-400 text-sm">石膏 (無上市公司)</h6>
										</div>
									</div>

								</div>

								<div class="grid grid-cols-2 gap-4 -my-1">
									<div class="flex justify-center">
										<i class="fa-solid fa-arrow-down text-slate-400"></i>
									</div>
									<div class="flex justify-center">
										<i class="fa-solid fa-arrow-down text-slate-400"></i>
									</div>
								</div>

								<div onclick="toggleCompanyList(companySectors, '水泥成品', 'orange')" class="cursor-pointer bg-gradient-to-br from-orange-500 to-amber-600 text-white rounded-lg p-5 shadow-lg shadow-orange-100 hover:shadow-xl hover:scale-105 transition-all text-center ring-4 ring-white">
									<div class="mb-1"><i class="fa-solid fa-industry text-2xl"></i></div>
									<h5 class="font-bold text-lg">水泥成品</h5>
								</div>
							</div>
                        </div>
                    </div>
                
                    <!-- 下游 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">終端應用與市場</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 h-full shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex flex-col gap-4 h-full relative z-10 justify-center">
                
                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-trowel"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-400 text-sm">營建業 (無上市公司)</h6>
                                </div>

                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-bridge"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-400 text-sm">公共工程 (無上市公司)</h6>
                                </div>

                                <div class="bg-slate-100 opacity-60 border border-slate-200 rounded-lg p-4 flex items-center gap-4 shadow-sm cursor-not-allowed">
                                    <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-oil-well"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-400 text-sm">水泥製品 (無上市公司) <span class="text-xs text-slate-400 block font-normal">(如水泥磚、水泥瓦等)</span></h6>
                                </div>

                                <div onclick="toggleCompanyList(companySectors, '預拌混凝土', 'emerald')" class="cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-lg p-4 transition-all flex items-center gap-4 hover:-translate-y-1 shadow-sm">
                                    <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-truck-monster"></i>
                                    </div>
                                    <h6 class="font-bold text-slate-700 text-sm">預拌混凝土</h6>
                                </div>
                
                            </div>
                        </div>
                    </div>
                </div>
                
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
        banner("<?= $category ?>產業供應鏈", "精準解析傳統基建核心，掌握水泥產業鏈脈絡");
    </script>
</body>
</html>