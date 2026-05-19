from DrissionPage import ChromiumPage, ChromiumOptions
import time
import os
import random
import json

def crawl_dcard_passive_content(company_name):
    # --- 1. 設定瀏覽器 ---
    co = ChromiumOptions()
    possible_paths = [
        r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
        r"C:\Program Files\Microsoft\Edge\Application\msedge.exe"
    ]
    edge_path = next((p for p in possible_paths if os.path.exists(p)), None)
    if edge_path: co.set_browser_path(edge_path)

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
    
    target_url = f"https://www.dcard.tw/search?query={company_name}&forum=tech_job"
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
                    if not real_post:
                        continue

                    post_id = str(real_post.get('id'))
                    title = real_post.get('title', '').replace('<em>', '').replace('</em>', '')
                    created_at = real_post.get('createdAt')
                    post_url = f"https://www.dcard.tw/f/tech_job/p/{post_id}"

                    if post_id in seen_ids:
                        continue

                    seen_ids.add(post_id)

                    all_posts.append({
                        "ID": post_id,
                        "標題": title,
                        "連結": post_url,
                        "發文時間": created_at,
                        "內容": ""
                    })

                    if len(all_posts) >= 10:
                        print("達到目標數量 10 篇，停止爬取。")
                        break

                except Exception as e:
                    print(e)
                    continue

            if len(all_posts) >= 10:
                break

        except Exception as e:
            print(e)
            continue

    page.listen.stop()

    # ==========================================
    # Phase 2: 進入文章頁面監聽內文 (核心修正)
    # ==========================================
    print(f"🚀 [Phase 2] 開始進入文章頁面監聽全文...")
    
    success_count = 0
    # 確保頁面不要在這邊自動關閉，我們要複用同一個 tab
    for post in all_posts:
        try:
            print(f"   📖 正在讀取: {post['標題'][:15]}...", end="\r")
            page.get(post['連結'])
            time.sleep(random.uniform(2.5, 4)) # 給予穩定等待時間
            
            # 使用更穩定的選擇器抓取內容，不使用 API 監聽
            # Dcard 文章內容容器 class 通常是 sc-3405c87b-1
            article = page.ele('css:.d_1f_1') or page.ele('tag:article')
            
            if article:
                post['內容'] = article.text.replace('\n', ' ')
                success_count += 1
            else:
                post['內容'] = page.ele('tag:body').text[:200]
                
        except Exception as e:
            print(f"\n   ❌ 讀取失敗: {e}")
            post['內容'] = "讀取錯誤"
            continue

    print(f"\n\n🎉 全部完成！成功讀取 {success_count}/{len(all_posts)} 篇全文。")
    page.quit() # 最終關閉瀏覽器
    return all_posts