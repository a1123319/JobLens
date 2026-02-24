from DrissionPage import ChromiumPage, ChromiumOptions
import csv
import time
import os
import random
import json

def crawl_dcard_passive_content():
    # --- 1. 設定瀏覽器 ---
    co = ChromiumOptions()
    possible_paths = [
        r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
        r"C:\Program Files\Microsoft\Edge\Application\msedge.exe"
    ]
    edge_path = next((p for p in possible_paths if os.path.exists(p)), None)
    if edge_path: co.set_browser_path(edge_path)
    try: co.use_system_user_path()
    except: pass
    co.set_local_port(9527)

    print("🚀 啟動瀏覽器 (全被動監聽模式)...")
    try:
        page = ChromiumPage(co)
    except Exception as e:
        print(f"❌ 啟動失敗: {e}")
        return []

    # ==========================================
    # Phase 1: 搜尋列表 (已驗證成功)
    # ==========================================
    print("running Phase 1...")
    page.listen.start('search/all')
    
    target_url = "https://www.dcard.tw/search?query=台積電&forum=tech_job"
    if target_url not in page.url:
        page.get(target_url)

    # 捲動幾次抓列表
    scroll_times = 3
    for i in range(scroll_times):
        page.scroll.to_bottom()
        time.sleep(2)

    all_posts = []
    seen_ids = set()

    # 解析列表
    for packet in page.listen.steps(timeout=5):
        try:
            response = packet.response.body
            items = []
            if isinstance(response, dict):
                items = response.get('items', []) or response.get('data', [])
            elif isinstance(response, list):
                items = response
            
            for item in items:
                try:
                    search_post = item.get('searchPost', {})
                    real_post = search_post.get('post', {})
                    if not real_post: continue

                    post_id = str(real_post.get('id'))
                    title = real_post.get('title', '').replace('<em>', '').replace('</em>', '')
                    created_at = real_post.get('createdAt')
                    post_url = f"https://www.dcard.tw/f/tech_job/p/{post_id}"

                    if post_id in seen_ids: continue
                    seen_ids.add(post_id)
                    
                    all_posts.append({
                        "ID": post_id,
                        "標題": title,
                        "連結": post_url,
                        "發文時間": created_at,
                        "全文": "" # 待填入
                    })
                except: continue
        except: continue

    print(f"✅ Phase 1 完成！共取得 {len(all_posts)} 篇 ID。")
    
    # 停止之前的監聽
    page.listen.stop()

    # ==========================================
    # Phase 2: 進入文章頁面監聽內文 (核心修正)
    # ==========================================
    print(f"🚀 [Phase 2] 開始進入文章頁面監聽全文...")
    
    success_count = 0
    
    for index, post in enumerate(all_posts):
        post_id = post['ID']
        post_url = post['連結']
        
        # 1. 設定監聽器：鎖定 "posts/{id}" 這個 API
        # 當我們打開網頁時，瀏覽器會自動打這個 API，我們只要攔截它
        listen_target = f'posts/{post_id}'
        page.listen.start(listen_target)
        
        print(f"   📖 ({index+1}/{len(all_posts)}) 正在閱讀: {post['標題'][:15]}...", end="\r")
        
        try:
            # 2. 【關鍵】打開「網頁」而不是 API
            page.get(post_url)
            
            # 3. 等待攔截封包
            found_content = False
            
            # 給它 5 秒鐘載入並觸發 API
            for packet in page.listen.steps(timeout=5):
                try:
                    res = packet.response.body
                    # 確認是不是這篇文章的資料
                    if isinstance(res, dict) and str(res.get('id')) == post_id:
                        content = res.get('content', '')
                        if content:
                            # 簡單清理換行
                            post['全文'] = content.replace('\n', ' ')
                            found_content = True
                            success_count += 1
                            break
                except: continue
            
            if not found_content:
                # 如果監聽失敗，嘗試用備用方案：直接抓網頁文字 (DOM)
                # print("      (監聽逾時，改用網頁文字抓取)")
                article = page.ele('tag:article')
                if article:
                    post['全文'] = article.text.replace('\n', ' ')
                    success_count += 1
                else:
                    post['全文'] = "(無法讀取內容)"

            # 4. 休息一下
            page.listen.stop()
            time.sleep(random.uniform(1.5, 2.5))

        except Exception as e:
            post['全文'] = "讀取錯誤"
            page.listen.stop()
            continue

    print(f"\n\n🎉 全部完成！成功下載 {success_count}/{len(all_posts)} 篇全文。")
    return all_posts

# --- 執行 ---
data = crawl_dcard_passive_content()

if data:
    filename = 'dcard_tsmc_passive_full.csv'
    with open(filename, 'w', newline='', encoding='utf-8-sig') as f:
        writer = csv.DictWriter(f, fieldnames=data[0].keys())
        writer.writeheader()
        writer.writerows(data)
    print(f"📂 檔案已儲存: {filename}")
else:
    print("😭 沒抓到資料")