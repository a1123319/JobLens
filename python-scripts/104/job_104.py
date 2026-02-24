import requests
import csv

def get_jobs_page_one(company_code):
    # 目標 API
    url = f"https://www.104.com.tw/api/companies/{company_code}/jobs"
    
    # 必要的偽裝
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
        "Referer": f"https://www.104.com.tw/company/{company_code}",
    }

    # 設定參數：只抓第 1 頁
    params = {
        "page": 1,
        "pageSize": 20,
        "order": 1, 
    }

    print("🚀 正在抓取第 1 頁資料...")
    
    try:
        resp = requests.get(url, headers=headers, params=params)
        
        if resp.status_code != 200:
            print(f"❌ 請求失敗: {resp.status_code}")
            return []
            
        data = resp.json()
        
        # 解析資料：合併置頂 (topJobs) 與一般 (normalJobs) 職缺
        list_obj = data.get('data', {}).get('list', {})
        top_jobs = list_obj.get('topJobs', [])
        normal_jobs = list_obj.get('normalJobs', [])
        
        all_jobs_raw = top_jobs + normal_jobs
        
        # 整理成我們需要的格式
        processed_jobs = []
        for job in all_jobs_raw:
            item = {
                "職缺名稱": job.get('jobName'),
                "連結": job.get('jobUrl'),  # ✅ 這裡就是您要的連結
                "薪資": job.get('jobSalaryDesc'),
                "地點": job.get('jobAddrNoDesc'),
                "學歷": job.get('edu'),
                "經歷": job.get('periodDesc')
            }
            processed_jobs.append(item)
            
        return processed_jobs

    except Exception as e:
        print(f"發生錯誤: {e}")
        return []

# --- 執行區 ---
company_code = "a5h92m0"
jobs = get_jobs_page_one(company_code)

print(f"\n🎉 成功抓取 {len(jobs)} 筆資料：")

# 印出前幾筆檢查連結
for job in jobs[:5]:
    print(f"{job['職缺名稱']} -> {job['連結']}")

# 存成 CSV
if jobs:
    with open('104_jobs_page1.csv', 'w', newline='', encoding='utf-8-sig') as f:
        writer = csv.DictWriter(f, fieldnames=jobs[0].keys())
        writer.writeheader()
        writer.writerows(jobs)
    print("\n✅ 檔案已儲存為 '104_jobs_page1.csv'")