-- ========================================================
-- JobLens 產業資料匯入 SQL
-- 產業名稱: 電子商務 (Category: 電子商務)
-- 來源網址: https://ic.tpex.org.tw/introduce.php?ic=R300
-- 產生時間: 2026-07-27
-- ========================================================

-- 1. 匯入 companycategory (公司產業分類)
INSERT INTO `companycategory` (`CompanyID`, `Category`, `Sector`, `SubSector`) VALUES
('8454', '電子商務', '支援服務業', '物流倉儲服務'),
('2352', '電子商務', '支援服務業', '資訊系統建置服務'),
('6183', '電子商務', '支援服務業', '資訊系統建置服務'),
('7721', '電子商務', '支援服務業', '資訊系統建置服務'),
('7765', '電子商務', '支援服務業', '資訊系統建置服務'),
('6183', '電子商務', '支援服務業', '金流串接處理服務'),
('7721', '電子商務', '支援服務業', '金流串接處理服務'),
('7722', '電子商務', '支援服務業', '金流串接處理服務'),
('2352', '電子商務', '支援服務業', '資料分析處理服務'),
('6183', '電子商務', '支援服務業', '資料分析處理服務'),
('6614', '電子商務', '支援服務業', '資料分析處理服務'),
('7721', '電子商務', '支援服務業', '資料分析處理服務'),
('7765', '電子商務', '支援服務業', '資料分析處理服務'),
('3130', '電子商務', '支援服務業', '行銷廣告服務'),
('7722', '電子商務', '支援服務業', '行銷廣告服務'),
('8454', '電子商務', '平台服務業', '店點開設管理'),
('6614', '電子商務', '平台服務業', '交易撮(媒)合'),
('2352', '電子商務', '平台服務業', '資訊聚合'),
('3130', '電子商務', '平台服務業', '資訊聚合'),
('6614', '電子商務', '平台服務業', '資訊聚合'),
('7721', '電子商務', '平台服務業', '資訊聚合'),
('6277', '電子商務', '銷售服務業', '自有產品(服務)銷售'),
('6614', '電子商務', '銷售服務業', '自有產品(服務)銷售'),
('2352', '電子商務', '銷售服務業', '一般零售'),
('2903', '電子商務', '銷售服務業', '一般零售'),
('2915', '電子商務', '銷售服務業', '一般零售'),
('5904', '電子商務', '銷售服務業', '一般零售'),
('8454', '電子商務', '銷售服務業', '一般零售'),
('2731', '電子商務', '銷售服務業', '票券銷售');

-- 2. 匯入 recruitmentsource (公司徵才來源，跳過已有記錄)
INSERT IGNORE INTO `recruitmentsource` (`CompanyID`, `OneHundredAndFour`, `Official`) VALUES
('8454', 'https://www.104.com.tw/company/a70w22w', 'https://www.momoshop.com.tw'),
('2352', 'https://www.104.com.tw/company/2a5ju20', 'https://www.qisda.com'),
('6183', 'https://www.104.com.tw/company/a30edkg', 'https://www.tradevan.com.tw'),
('7721', 'https://www.104.com.tw/company/1a2x6bm0g4', 'https://www.microprogram.com.tw'),
('7765', 'https://www.104.com.tw/company/1a2x6bm16g', 'https://www.chtsecurity.com'),
('7722', 'https://www.104.com.tw/company/1a2x6bm0i8', 'https://linepay.line.me'),
('6614', 'https://www.104.com.tw/company/1a2x6bjwc0', 'https://www.iisicom.com'),
('3130', 'https://www.104.com.tw/company/a4c1t80', 'https://corp.104.com.tw'),
('6277', 'https://www.104.com.tw/company/c3g6518', 'https://www.aten.com'),
('2903', 'https://www.104.com.tw/company/a0g0i9k', 'https://www.feds.com.tw'),
('2915', 'https://www.104.com.tw/company/a1451f8', 'https://www.ruentex.com.tw'),
('5904', 'https://www.104.com.tw/company/a4i54o4', 'https://www.poya.com.tw'),
('2731', 'https://www.104.com.tw/company/a70ed60', 'https://www.liontravel.com');
