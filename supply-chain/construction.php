<?php
require "util.php";

$id = $_GET["id"] ?? null;
$category = "建材營造";

function safeGetCompanies($cat) {
    try {
        $comps = getCompanies($cat);
        if (!empty($comps)) {
            return $comps;
        }
    } catch (Throwable $e) {
        // DB not yet imported or unavailable
    }
    // Fallback verified baseline data
    return json_decode(<<<'JSON'
[
  {
    "CompanyId": "1603",
    "CompanyName": "華電",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "1612",
    "CompanyName": "宏泰",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "1802",
    "CompanyName": "台玻",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "1806",
    "CompanyName": "冠軍",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "1809",
    "CompanyName": "中釉",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "1810",
    "CompanyName": "和成",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "1817",
    "CompanyName": "凱撒衛",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "2020",
    "CompanyName": "美亞",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "2504",
    "CompanyName": "國產",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "2597",
    "CompanyName": "潤弘",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "3149",
    "CompanyName": "正達",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "5515",
    "CompanyName": "建國",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "5534",
    "CompanyName": "長虹",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "8463",
    "CompanyName": "潤泰材",
    "Sector": "上游",
    "Subsector": "建材原料"
  },
  {
    "CompanyId": "2031",
    "CompanyName": "新光鋼",
    "Sector": "上游",
    "Subsector": "基礎工程"
  },
  {
    "CompanyId": "2597",
    "CompanyName": "潤弘",
    "Sector": "上游",
    "Subsector": "基礎工程"
  },
  {
    "CompanyId": "1472",
    "CompanyName": "三洋實業",
    "Sector": "上游",
    "Subsector": "結構工程"
  },
  {
    "CompanyId": "2010",
    "CompanyName": "春源",
    "Sector": "上游",
    "Subsector": "結構工程"
  },
  {
    "CompanyId": "2031",
    "CompanyName": "新光鋼",
    "Sector": "上游",
    "Subsector": "結構工程"
  },
  {
    "CompanyId": "2597",
    "CompanyName": "潤弘",
    "Sector": "上游",
    "Subsector": "結構工程"
  },
  {
    "CompanyId": "9945",
    "CompanyName": "潤泰新",
    "Sector": "上游",
    "Subsector": "結構工程"
  },
  {
    "CompanyId": "1472",
    "CompanyName": "三洋實業",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "1503",
    "CompanyName": "士電",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "1504",
    "CompanyName": "東元",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "2308",
    "CompanyName": "台達電",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "2371",
    "CompanyName": "大同",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "2597",
    "CompanyName": "潤弘",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "3018",
    "CompanyName": "隆銘綠能",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "9945",
    "CompanyName": "潤泰新",
    "Sector": "上游",
    "Subsector": "機電工程"
  },
  {
    "CompanyId": "2597",
    "CompanyName": "潤弘",
    "Sector": "上游",
    "Subsector": "工程設計"
  },
  {
    "CompanyId": "9945",
    "CompanyName": "潤泰新",
    "Sector": "上游",
    "Subsector": "工程設計"
  },
  {
    "CompanyId": "1472",
    "CompanyName": "三洋實業",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "2511",
    "CompanyName": "太子",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "2515",
    "CompanyName": "中工",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "2516",
    "CompanyName": "新建",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "2535",
    "CompanyName": "達欣工",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "2543",
    "CompanyName": "皇昌",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "2546",
    "CompanyName": "根基",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "2597",
    "CompanyName": "潤弘",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "3703",
    "CompanyName": "欣陸",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "5515",
    "CompanyName": "建國",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "5519",
    "CompanyName": "隆大",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "5521",
    "CompanyName": "工信",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "9945",
    "CompanyName": "潤泰新",
    "Sector": "中游",
    "Subsector": "營造業"
  },
  {
    "CompanyId": "1103",
    "CompanyName": "嘉泥",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1402",
    "CompanyName": "遠東新",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1416",
    "CompanyName": "廣豐",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1436",
    "CompanyName": "華友聯",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1438",
    "CompanyName": "三地開發",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1439",
    "CompanyName": "雋揚",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1442",
    "CompanyName": "名軒",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1453",
    "CompanyName": "大將",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1456",
    "CompanyName": "怡華",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1513",
    "CompanyName": "中興電",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1532",
    "CompanyName": "勤美",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1605",
    "CompanyName": "華新",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1805",
    "CompanyName": "寶徠",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1808",
    "CompanyName": "潤隆",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2020",
    "CompanyName": "美亞",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2424",
    "CompanyName": "隴華",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2442",
    "CompanyName": "新美齊",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2501",
    "CompanyName": "國建",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2505",
    "CompanyName": "國揚",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2506",
    "CompanyName": "太設",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2509",
    "CompanyName": "全坤建",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2511",
    "CompanyName": "太子",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2514",
    "CompanyName": "龍邦",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2515",
    "CompanyName": "中工",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2520",
    "CompanyName": "冠德",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2524",
    "CompanyName": "京城",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2527",
    "CompanyName": "宏璟",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2528",
    "CompanyName": "皇普",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2530",
    "CompanyName": "華建",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2534",
    "CompanyName": "宏盛",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2535",
    "CompanyName": "達欣工",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2536",
    "CompanyName": "宏普",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2537",
    "CompanyName": "聯上發",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2538",
    "CompanyName": "基泰",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2539",
    "CompanyName": "櫻花建",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2542",
    "CompanyName": "興富發",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2545",
    "CompanyName": "皇翔",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2547",
    "CompanyName": "日勝生",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2548",
    "CompanyName": "華固",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2910",
    "CompanyName": "統領",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "3052",
    "CompanyName": "夆典",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "3056",
    "CompanyName": "富華新",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "3266",
    "CompanyName": "昇陽",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "3703",
    "CompanyName": "欣陸",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "5519",
    "CompanyName": "隆大",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "5522",
    "CompanyName": "遠雄",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "5525",
    "CompanyName": "順天",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "5531",
    "CompanyName": "鄉林",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "5533",
    "CompanyName": "皇鼎",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "6177",
    "CompanyName": "達麗",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "9906",
    "CompanyName": "欣巴巴",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "9945",
    "CompanyName": "潤泰新",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "9946",
    "CompanyName": "三發地產",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "2923",
    "CompanyName": "鼎固-KY",
    "Sector": "中游",
    "Subsector": "建設業"
  },
  {
    "CompanyId": "1402",
    "CompanyName": "遠東新",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "1472",
    "CompanyName": "三洋實業",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "1603",
    "CompanyName": "華電",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "2511",
    "CompanyName": "太子",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "2527",
    "CompanyName": "宏璟",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "2535",
    "CompanyName": "達欣工",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "3052",
    "CompanyName": "夆典",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "3703",
    "CompanyName": "欣陸",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "9917",
    "CompanyName": "中保科",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "9933",
    "CompanyName": "中鼎",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "9958",
    "CompanyName": "世紀鋼",
    "Sector": "中游",
    "Subsector": "工程承攬"
  },
  {
    "CompanyId": "1809",
    "CompanyName": "中釉",
    "Sector": "下游",
    "Subsector": "個人、民間企業、政府機構"
  },
  {
    "CompanyId": "2597",
    "CompanyName": "潤弘",
    "Sector": "下游",
    "Subsector": "裝潢業"
  },
  {
    "CompanyId": "6754",
    "CompanyName": "匯僑設計",
    "Sector": "下游",
    "Subsector": "裝潢業"
  },
  {
    "CompanyId": "9945",
    "CompanyName": "潤泰新",
    "Sector": "下游",
    "Subsector": "裝潢業"
  },
  {
    "CompanyId": "1513",
    "CompanyName": "中興電",
    "Sector": "下游",
    "Subsector": "物業管理"
  },
  {
    "CompanyId": "2371",
    "CompanyName": "大同",
    "Sector": "下游",
    "Subsector": "物業管理"
  },
  {
    "CompanyId": "2511",
    "CompanyName": "太子",
    "Sector": "下游",
    "Subsector": "物業管理"
  },
  {
    "CompanyId": "9917",
    "CompanyName": "中保科",
    "Sector": "下游",
    "Subsector": "物業管理"
  },
  {
    "CompanyId": "9945",
    "CompanyName": "潤泰新",
    "Sector": "下游",
    "Subsector": "物業管理"
  },
  {
    "CompanyId": "2923",
    "CompanyName": "鼎固-KY",
    "Sector": "下游",
    "Subsector": "物業管理"
  }
]
JSON
, true);
}

$companyData = safeGetCompanies($category);
?>
<!DOCTYPE html>
<html lang="zh-TW" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobLens - <?= $category ?>產業鏈分析</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="script.js?v=<?= time() ?>"></script>
    <script>
        function buildCompanySectors(entities) {
            const sectors = new Map();
            if (!Array.isArray(entities)) return sectors;
            for (const entity of entities) {
                if (entity.Sector) {
                    if (!sectors.has(entity.Sector)) sectors.set(entity.Sector, []);
                    sectors.get(entity.Sector).push(entity);
                }
                const sub = entity.Subsector || entity.SubSector;
                if (sub && sub !== entity.Sector) {
                    if (!sectors.has(sub)) sectors.set(sub, []);
                    sectors.get(sub).push(entity);
                }
            }
            return sectors;
        }

        const companySectors = buildCompanySectors(<?= json_encode($companyData, JSON_UNESCAPED_UNICODE) ?>);

        const iconMap = new Map([
            ['建材原料', 'fa-solid fa-trowel-bricks text-amber-500'],
            ['基礎工程', 'fa-solid fa-dolly text-amber-600'],
            ['結構工程', 'fa-solid fa-cubes-stacked text-amber-600'],
            ['機電工程', 'fa-solid fa-bolt text-amber-500'],
            ['工程設計', 'fa-solid fa-compass-drafting text-amber-600'],
            ['營造業', 'fa-solid fa-helmet-safety text-orange-500'],
            ['建設業', 'fa-solid fa-building text-orange-600'],
            ['工程承攬', 'fa-solid fa-file-signature text-orange-500'],
            ['個人、民間企業、政府機構', 'fa-solid fa-landmark text-emerald-600'],
            ['裝潢業', 'fa-solid fa-paint-roller text-emerald-500'],
            ['物業管理', 'fa-solid fa-key text-emerald-600']
        ]);

        function showCompanyList(sector, color) {
            if (companySectors.has(sector)) {
                toggleCompanyList(companySectors, sector, color, iconMap);
                return;
            }

            const companyListDiv = document.getElementById('company-list');
            companyListDiv.className = 'bg-white p-6 rounded-xl border border-slate-200 shadow-sm ring-2 ring-' + color + '-200';
            companyListDiv.innerHTML = `<p class="font-bold text-slate-700 text-lg mb-2 flex items-center gap-2 border-b border-slate-100 pb-3"><span class="w-3 h-3 rounded-full bg-${color}-500"></span>${sector}</p><p class="text-sm text-slate-500">目前尚無可顯示的公司資料。</p>`;
            companyListDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap');
        body { font-family: 'Noto Sans TC', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        .chain-step:not(:last-child)::after {
            content: '';
            position: absolute;
            right: -1.25rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-left: 12px solid #cbd5e1;
            z-index: 10;
        }
        .chain-step:not(:last-child)::before {
            content: '';
            position: absolute;
            right: -1.4rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-top: 12px solid transparent;
            border-bottom: 12px solid transparent;
            border-left: 14px solid #94a3b8;
            z-index: 9;
        }
        @media (max-width: 1023px) {
            .chain-step:not(:last-child)::after,
            .chain-step:not(:last-child)::before { display: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <?php nav($id) ?>
    <header id="banner"></header>

    <main class="container mx-auto px-4 py-8 space-y-12">
        <section id="supply-chain-overview" class="scroll-mt-24">
            <h3 class="text-2xl font-bold text-slate-800 mb-8 flex items-center gap-2">
                <span class="bg-amber-600 w-2 h-8 rounded-full"></span> 供應鏈結構圖
            </h3>
            
            <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-6 md:p-8 pb-12 overflow-visible relative">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-amber-500 via-orange-500 to-emerald-600 rounded-t-2xl"></div>
                
                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-slate-700 tracking-wide">建材營造產業鏈簡介</h2>
                    <p class="text-sm text-slate-500 mt-1">涵蓋工程原料準備、營造與建設主體至終端物業與裝潢應用，點擊各節點查看對應上市企業。</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 relative mb-12">
                    
                    <!-- 上游 原料與工程前期 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center font-bold shadow-sm">1</div>
                                <h4 class="text-xl font-bold text-slate-700">上游 <span class="text-sm text-slate-500 font-normal">原料與工程前期</span></h4>
                            </div>
                            
                            <div class="flex flex-col gap-3">
                                <div onclick="showCompanyList('建材原料', 'amber')" class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-xl p-3.5 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-trowel-bricks"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-slate-700 text-sm">建材原料</h5>
                                        <p class="text-xs text-slate-400 truncate">鋼鐵、水泥製品、玻璃等</p>
                                    </div>
                                </div>

                                <div onclick="showCompanyList('基礎工程', 'amber')" class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-xl p-3.5 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-dolly"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-slate-700 text-sm">基礎工程</h5>
                                        <p class="text-xs text-slate-400 truncate">地基、鋼板樁、擋土工程</p>
                                    </div>
                                </div>

                                <div onclick="showCompanyList('結構工程', 'amber')" class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-xl p-3.5 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-cubes-stacked"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-slate-700 text-sm">結構工程</h5>
                                        <p class="text-xs text-slate-400 truncate">鋼構主體、預鑄梁柱</p>
                                    </div>
                                </div>

                                <div onclick="showCompanyList('機電工程', 'amber')" class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-xl p-3.5 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-bolt"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-slate-700 text-sm">機電工程</h5>
                                        <p class="text-xs text-slate-400 truncate">水電系統、配電空調設施</p>
                                    </div>
                                </div>

                                <div onclick="showCompanyList('工程設計', 'amber')" class="cursor-pointer bg-white border border-slate-200 hover:border-amber-400 hover:bg-amber-50 rounded-xl p-3.5 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm">
                                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-compass-drafting"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-slate-700 text-sm">工程設計</h5>
                                        <p class="text-xs text-slate-400 truncate">建築規劃與結構設計</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 中游 營造與建設主體 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center font-bold shadow-sm">2</div>
                                <h4 class="text-xl font-bold text-slate-700">中游 <span class="text-sm text-slate-500 font-normal">營造與建設</span></h4>
                            </div>
                            
                            <div class="flex flex-col gap-3">
                                <!-- 上半部：營造業 -> 建設業 -->
                                <div class="grid grid-cols-1 md:grid-cols-11 gap-2 items-center">
                                    <div class="md:col-span-5">
                                        <div onclick="showCompanyList('營造業', 'orange')" class="cursor-pointer bg-white border border-slate-200 hover:border-orange-400 hover:bg-orange-50 rounded-xl p-3.5 transition-all flex items-center gap-3 hover:-translate-y-0.5 shadow-sm h-full">
                                            <div class="w-9 h-9 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-base flex-shrink-0">
                                                <i class="fa-solid fa-helmet-safety"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="font-bold text-slate-700 text-sm">營造業</h5>
                                                <p class="text-[11px] text-slate-400 truncate">土木工程與建築營造</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 營造業 -> 建設業 箭頭 -->
                                    <div class="md:col-span-1 flex justify-center items-center py-1">
                                        <div class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xs font-bold shadow-xs">
                                            <i class="fa-solid fa-chevron-right hidden md:block"></i>
                                            <i class="fa-solid fa-chevron-down md:hidden"></i>
                                        </div>
                                    </div>

                                    <div class="md:col-span-5">
                                        <div onclick="showCompanyList('建設業', 'orange')" class="cursor-pointer bg-gradient-to-br from-orange-500 to-amber-600 text-white rounded-xl p-3.5 transition-all flex items-center gap-3 hover:-translate-y-0.5 shadow-lg shadow-orange-100 hover:shadow-xl h-full">
                                            <div class="w-9 h-9 rounded-lg bg-white/20 text-white flex items-center justify-center text-base flex-shrink-0">
                                                <i class="fa-solid fa-building"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="font-bold text-white text-sm">建設業</h5>
                                                <p class="text-[11px] text-orange-100 truncate">土地開發與商辦建案</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 下半部：工程承攬 (寬版) -->
                                <div onclick="showCompanyList('工程承攬', 'orange')" class="cursor-pointer bg-white border border-slate-200 hover:border-orange-400 hover:bg-orange-50 rounded-xl p-3.5 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-sm mt-1">
                                    <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-slate-700 text-sm">工程承攬</h5>
                                        <p class="text-xs text-slate-400 truncate">大型統包工程、公共及廠房承攬</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 下游 終端應用、裝潢與物業管理 -->
                    <div class="chain-step relative bg-slate-100/80 p-6 rounded-2xl border border-slate-200 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold shadow-sm">3</div>
                                <h4 class="text-xl font-bold text-slate-700">下游 <span class="text-sm text-slate-500 font-normal">終端應用與物業</span></h4>
                            </div>
                            
                            <div class="flex flex-col gap-3">
                                <!-- 上方：個人、民間企業、政府機構 -->
                                <div onclick="showCompanyList('個人、民間企業、政府機構', 'emerald')" class="cursor-pointer bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-xl p-4 transition-all flex items-center gap-4 hover:-translate-y-0.5 shadow-md shadow-emerald-100 hover:shadow-lg">
                                    <div class="w-10 h-10 rounded-lg bg-white/20 text-white flex items-center justify-center text-lg flex-shrink-0">
                                        <i class="fa-solid fa-landmark"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-bold text-white text-sm">個人、民間企業、政府機構</h5>
                                        <p class="text-xs text-emerald-100 truncate">終端買方、公共建設主體</p>
                                    </div>
                                </div>

                                <!-- 下方：裝潢業與物業管理，各帶往上的箭頭 -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-1">
                                    <!-- 裝潢業 (含往上箭頭) -->
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold shadow-xs">
                                            <i class="fa-solid fa-chevron-up"></i>
                                        </div>
                                        <div onclick="showCompanyList('裝潢業', 'emerald')" class="w-full cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-xl p-3 transition-all flex flex-col items-center text-center gap-2 hover:-translate-y-0.5 shadow-sm">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                                                <i class="fa-solid fa-paint-roller"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h5 class="font-bold text-slate-700 text-sm">裝潢業</h5>
                                                <p class="text-[11px] text-slate-400 truncate">室內裝修施工</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 物業管理 (含往上箭頭) -->
                                    <div class="flex flex-col items-center gap-1.5">
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs font-bold shadow-xs">
                                            <i class="fa-solid fa-chevron-up"></i>
                                        </div>
                                        <div onclick="showCompanyList('物業管理', 'emerald')" class="w-full cursor-pointer bg-white border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50 rounded-xl p-3 transition-all flex flex-col items-center text-center gap-2 hover:-translate-y-0.5 shadow-sm">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                                                <i class="fa-solid fa-key"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <h5 class="font-bold text-slate-700 text-sm">物業管理</h5>
                                                <p class="text-[11px] text-slate-400 truncate">大樓保全與維運</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-12 space-y-6 relative z-20">
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
        banner("<?= $category ?>產業供應鏈", "全面透視建材原料、營造工程與建設開發產業鏈上市公司");
        // Default show 上游 on load
        showCompanyList('建材原料', 'amber');
    </script>
</body>
</html>
