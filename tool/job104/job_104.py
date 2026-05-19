import requests

def get_jobs_page_one(company_code):
    url = f"https://www.104.com.tw/api/companies/{company_code}/jobs"
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36",
        "Referer": f"https://www.104.com.tw/company/{company_code}",
    }
    params = {"page": 1, "pageSize": 20, "order": 1}
    
    try:
        resp = requests.get(url, headers=headers, params=params)
        if resp.status_code != 200:
            return []
            
        data = resp.json()
        list_obj = data.get('data', {}).get('list', {})
        all_jobs_raw = list_obj.get('topJobs', []) + list_obj.get('normalJobs', [])
        
        processed_jobs = []
        for job in all_jobs_raw:
            processed_jobs.append({ 
                "連結": job.get('jobUrl'),
                "職缺名稱": job.get('jobName'),
                "薪資": job.get('jobSalaryDesc'),
            })
        return processed_jobs
    except Exception as e:
        print(f"發生錯誤: {e}")
        return []