<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "鋼鐵";
$steelCompanies = getCompanies($category);
$availableSectors = [];

foreach ($steelCompanies as $company) {
    if (!empty($company["Sector"])) {
        $availableSectors[$company["Sector"]] = true;
    }
}

function steelLabel(string $label, string $sector, array $availableSectors): string
{
    return isset($availableSectors[$sector]) ? $label : $label . "（無上市公司）";
}
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
        const companySectors = fromCompanyDatabase(<?= json_encode($steelCompanies, JSON_UNESCAPED_UNICODE) ?>);
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .steel-sector { min-height: 76px; }
        .steel-sector-disabled { filter: grayscale(1); opacity: .58; cursor: not-allowed; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-sky-600 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>

            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-5 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-500 via-slate-500 to-sky-600 rounded-t-2xl"></div>
                <div class="mb-8 flex flex-wrap items-center justify-between gap-3 text-sm text-slate-500">
                    <p><i class="fa-solid fa-circle-info text-sky-600 mr-1"></i> 點擊有上市公司的節點查看公司清單</p>
                    <span class="rounded-full bg-slate-100 px-3 py-1">碳鋼 × 不鏽鋼 × 工業應用</span>
                </div>

                <div id="main_steel_panel" class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12 relative">
                    <!-- 上游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold shadow-sm">1</div>
                            <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-400 font-normal">原料與煉鋼</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full relative shadow-sm">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-4 relative z-10">
                                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-800">
                                    <i class="fa-solid fa-fire-flame-curved mr-1"></i> 高爐與電爐煉鋼，產出碳鋼及不鏽鋼胚
                                </div>
                                <button type="button" data-sector="煤、鐵礦砂、廢鋼" data-color="amber" class="steel-sector w-full bg-white border border-slate-200 rounded-lg p-4 shadow-sm transition-all text-left flex items-center gap-4">
                                    <span class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-mountain"></i></span>
                                    <span class="font-bold text-slate-700 text-sm"><?= steelLabel("煤、鐵礦砂、廢鋼", "煤、鐵礦砂、廢鋼", $availableSectors) ?></span>
                                </button>
                                <div class="flex justify-center -my-2"><i class="fa-solid fa-arrow-down text-slate-400"></i></div>
                                <button type="button" data-sector="鋼胚" data-color="amber" class="steel-sector w-full bg-white border border-slate-200 rounded-lg p-4 shadow-sm transition-all text-left flex items-center gap-4">
                                    <span class="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-cubes-stacked"></i></span>
                                    <span class="font-bold text-slate-700 text-sm"><?= steelLabel("鋼胚", "鋼胚", $availableSectors) ?></span>
                                </button>
                                <div class="border-t border-dashed border-slate-300 my-1"></div>
                                <button type="button" data-sector="煤、鐵礦砂、鎳鐵、鉻鐵、廢鋼" data-color="amber" class="steel-sector w-full bg-white border border-slate-200 rounded-lg p-4 shadow-sm transition-all text-left flex items-center gap-4">
                                    <span class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-recycle"></i></span>
                                    <span class="font-bold text-slate-700 text-sm"><?= steelLabel("煤、鐵礦砂、鎳鐵、鉻鐵、廢鋼", "煤、鐵礦砂、鎳鐵、鉻鐵、廢鋼", $availableSectors) ?></span>
                                </button>
                                <div class="flex justify-center -my-2"><i class="fa-solid fa-arrow-down text-slate-400"></i></div>
                                <button type="button" data-sector="不鏽鋼胚" data-color="amber" class="steel-sector w-full bg-gradient-to-br from-slate-600 to-slate-700 text-white rounded-lg p-5 shadow-lg shadow-slate-200 transition-all text-center ring-4 ring-white">
                                    <i class="fa-solid fa-cubes-stacked text-2xl mb-1"></i>
                                    <span class="block font-bold text-lg"><?= steelLabel("不鏽鋼胚", "不鏽鋼胚", $availableSectors) ?></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- 中游 -->
                    <div class="chain-col relative flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold shadow-sm">2</div>
                            <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-400 font-normal">軋延與加工</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full relative shadow-sm">
                            <div class="hidden lg:block absolute top-1/2 left-full w-12 h-1 bg-slate-300 z-0"></div>
                            <div class="hidden lg:block absolute top-1/2 -right-12 w-3 h-3 bg-slate-300 rounded-full transform translate-x-1/2 -translate-y-1/2 z-10 border-2 border-white"></div>
                            <div class="flex flex-col gap-3 h-full relative z-10 justify-center">
                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">碳鋼產品</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <?php foreach (["冷熱軋鋼板捲", "鋼筋", "線材盤元", "棒鋼盤元(捲狀條鋼)"] as $sector): ?>
                                            <button type="button" data-sector="<?= $sector ?>" data-color="slate" class="steel-sector w-full bg-slate-50 border border-slate-200 rounded-lg p-3 transition-all text-left flex items-center gap-3">
                                                <span class="w-8 h-8 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-bars-staggered text-sm"></i></span>
                                                <span class="font-bold text-slate-700 text-xs leading-5"><?= steelLabel($sector, $sector, $availableSectors) ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="flex justify-center"><i class="fa-solid fa-arrow-down text-slate-400"></i></div>
                                <div class="rounded-lg border border-slate-200 bg-white p-3">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">不鏽鋼產品</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <?php foreach (["冷熱軋不鏽鋼板捲", "不鏽鋼棒線", "不鏽鋼型鋼", "裁剪加工", "製管"] as $sector): ?>
                                            <button type="button" data-sector="<?= $sector ?>" data-color="slate" class="steel-sector w-full bg-slate-50 border border-slate-200 rounded-lg p-3 transition-all text-left flex items-center gap-3">
                                                <span class="w-8 h-8 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-gears text-sm"></i></span>
                                                <span class="font-bold text-slate-700 text-xs leading-5"><?= steelLabel($sector, $sector, $availableSectors) ?></span>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 下游 -->
                    <div class="chain-col flex flex-col h-full">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center font-bold shadow-sm">3</div>
                            <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-400 font-normal">終端製品與應用</span></h4>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 h-full shadow-sm">
                            <div class="flex flex-col gap-3 h-full relative z-10 justify-center">
                                <?php foreach ([
                                    "金屬製品" => "fa-solid fa-shapes",
                                    "機械設備(如產業機械、精密機械、工具機)" => "fa-solid fa-gears",
                                    "運輸工具" => "fa-solid fa-truck-front",
                                    "模具" => "fa-solid fa-shapes",
                                    "螺絲螺帽" => "fa-solid fa-screwdriver-wrench",
                                    "鋼線鋼纜" => "fa-solid fa-grip-lines",
                                    "工業設施" => "fa-solid fa-industry",
                                    "建築工程" => "fa-solid fa-building"
                                ] as $sector => $icon): ?>
                                    <button type="button" data-sector="<?= $sector ?>" data-color="sky" class="steel-sector w-full bg-white border border-slate-200 rounded-lg p-3 transition-all text-left flex items-center gap-3">
                                        <span class="w-9 h-9 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="<?= $icon ?> text-sm"></i></span>
                                        <span class="font-bold text-slate-700 text-xs leading-5"><?= steelLabel($sector, $sector, $availableSectors) ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-16 space-y-6 relative z-20">
                    <h4 class="text-xl font-bold text-slate-700 pl-4 border-l-4 border-sky-600 flex items-center gap-2">
                        點擊上方圖表查看公司列表
                    </h4>
                    <div id="company-list"></div>
                </div>
            </div>
        </section>
    </main>

    <footer id="footer"></footer>
    <script>
        document.querySelectorAll('.steel-sector').forEach((button) => {
            const sector = button.dataset.sector;
            const color = button.dataset.color;
            const hasCompanies = companySectors.has(sector) && companySectors.get(sector).length > 0;

            if (!hasCompanies) {
                button.disabled = true;
                button.setAttribute('aria-disabled', 'true');
                button.classList.add('steel-sector-disabled');
                return;
            }

            button.classList.add('cursor-pointer', 'hover:-translate-y-1', 'hover:shadow-md');
            button.addEventListener('click', () => toggleCompanyList(companySectors, sector, color));
        });

        banner("<?= $category ?>產業供應鏈", "從原料、煉鋼到終端應用，掌握碳鋼與不鏽鋼的產業鏈脈絡");
    </script>
</body>
</html>
