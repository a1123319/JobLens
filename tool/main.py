from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import pymysql
# 匯入你的爬蟲函式
from dcard.dcard_crawler import crawl_dcard_passive_content
from job104.job_104 import get_jobs_page_one

app = FastAPI(title="JobLens Internal API")

# 允許 Vercel 前端跨域請求
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], 
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "joblens",
    "cursorclass": pymysql.cursors.DictCursor
}

# --- 1. 取得公司清單 API ---
@app.get("/api/companies")
def get_companies():
    connection = pymysql.connect(**DB_CONFIG)
    with connection.cursor() as cursor:
        cursor.execute("SELECT Id, Name FROM company")
        companies = cursor.fetchall()
    connection.close()
    return companies
@app.get("/api/crawl/104")
def crawl_104(company_code: str):
    data = get_jobs_page_one(company_code)
    return {"status": "success", "data": data}

class ImportRequest(BaseModel):
    company_id: int
    data: list

@app.post("/api/import/recruitment")
def import_recruitment(req: ImportRequest):
    connection = pymysql.connect(**DB_CONFIG)
    try:
        with connection.cursor() as cursor:
            for item in req.data:
                # ⚠️ 注意：這裡的 JobTitle, Link 等欄位名稱，請依照你們 recruitment 資料表的實際英文欄位名稱進行修改
                sql = """
                    INSERT INTO recruitment (CompanyId, Url, Name, Salary) 
                    VALUES (%s, %s, %s, %s)
                """
                cursor.execute(sql, (
                    req.company_id, 
                    item['連結'], 
                    item['職缺名稱'], 
                    item['薪資'],
                ))
        connection.commit()
    except Exception as e:
        connection.rollback()
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        connection.close()
    return {"status": "success", "message": "成功匯入 104 職缺資料庫"}
# --- 2. 執行爬蟲 API (僅回傳預覽資料，不寫入) ---
@app.get("/api/crawl/dcard")
def crawl_dcard(company_name: str, forum: str = "tech_job"):
    data = crawl_dcard_passive_content(company_name, forum=forum)
    return {"status": "success", "data": data}

@app.get("/api/crawl/104")
def crawl_104(company_code: str):
    data = get_jobs_page_one(company_code)
    return {"status": "success", "data": data}

# --- 3. 確認後匯入資料庫 API ---
class ImportRequest(BaseModel):
    company_id: int
    data: list

@app.post("/api/import/comment")
def import_comments(req: ImportRequest):
    connection = pymysql.connect(**DB_CONFIG)
    try:
        with connection.cursor() as cursor:
            for item in req.data:
                sql = "INSERT INTO comment (CompanyId, Content, Url, Source) VALUES (%s, %s, %s, %s)"
                cursor.execute(sql, (req.company_id, item['內容'], item['連結'],item['評論來源']))
        connection.commit()
    except Exception as e:
        connection.rollback()
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        connection.close()
    return {"status": "success", "message": "成功匯入評論資料庫"}