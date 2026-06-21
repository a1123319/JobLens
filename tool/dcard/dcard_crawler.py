from DrissionPage import ChromiumPage, ChromiumOptions
import time
import os
import random
import json
import urllib.parse
import re

def normalize_text(text):
    if not text:
        return ""
    return re.sub(r'[\s\-_/\\\'"─—－]', '', text).lower()


def clean_comment_text(container_text):
    lines = [line.strip() for line in container_text.split('\n') if line.strip()]
    
    clean_lines = []
    for line in lines:
        if line in ["回覆", "引用", "分享"]:
            continue
        if re.match(r'^B\d+', line):
            continue
        clean_lines.append(line)
        
    content_start_idx = 0
    if len(clean_lines) > 1:
        # 情況 A: 單一頭像字母 + 名字 + 讚數
        if len(clean_lines) > 2 and len(clean_lines[0]) == 1 and clean_lines[2].isdigit():
            content_start_idx = 3
        # 情況 B: 學校/人設 + 讚數
        elif clean_lines[0].isdigit() or (len(clean_lines[0]) <= 15 and clean_lines[1].isdigit()):
            content_start_idx = 2
        # 情況 C: 只有學校/人設
        elif len(clean_lines[0]) <= 15:
            content_start_idx = 1
            
    if content_start_idx < len(clean_lines):
        return " ".join(clean_lines[content_start_idx:])
    else:
        return " ".join(clean_lines)


def crawl_dcard_passive_content(company_name, forum="tech_job"):
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
    
    target_url = f"https://www.dcard.tw/search?query={urllib.parse.quote(company_name)}&forum={forum}"
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
                    post_url = f"https://www.dcard.tw/f/{forum}/p/{post_id}"

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
    # Phase 2: 進入文章頁面監聽留言 (核心修正)
    # ==========================================
    print(f"🚀 [Phase 2] 開始進入文章頁面監聽留言...")
    
    all_comments_data = []
    success_count = 0
    
    # 確保頁面不要在這邊自動關閉，我們要複用同一個 tab
    for post in all_posts:
        try:
            print(f"   📖 正在讀取留言: {post['標題'][:15]}...", end="\r")
            
            page.get(post['連結'])
            # 滾動到頁面底部以確保觸發留言載入
            page.scroll.to_bottom()
            time.sleep(random.uniform(2.5, 4)) # 給予穩定等待時間
            
            # 定位所有的樓層超連結
            floor_links = page.eles('xpath://a[contains(@href, "/b/")]')
            
            comments_list = []
            normalized_company = normalize_text(company_name)
            is_title_relevant = normalized_company in normalize_text(post['標題'])
            
            for link in floor_links:
                try:
                    # 向上找 4 層大容器 (對應留言區塊 div)
                    container = link.parent(4)
                    raw_text = container.text
                    if raw_text:
                        cleaned = clean_comment_text(raw_text)
                        # 避免抓到重複 or 空白的留言
                        if cleaned and cleaned not in comments_list:
                            # 智慧篩選：如果標題包含公司名稱，抓取所有留言；
                            # 如果標題不包含公司名稱，則僅抓取「內容包含公司名稱」的留言！
                            if is_title_relevant or (normalized_company in normalize_text(cleaned)):
                                comments_list.append(cleaned)
                except Exception as container_err:
                    continue
            
            if comments_list:
                success_count += 1
                for c_content in comments_list:
                    all_comments_data.append({
                        "ID": post['ID'],
                        "標題": post['標題'],
                        "連結": post['連結'],
                        "發文時間": post['發文時間'],
                        "內容": c_content,
                        "評論來源": "Dcard"
                    })
                
        except Exception as e:
            print(f"\n   ❌ 讀取留言失敗: {e}")
            continue

    print(f"\n\n🎉 全部完成！成功讀取 {success_count}/{len(all_posts)} 篇留言。共取得 {len(all_comments_data)} 條留言。")
    page.quit() # 最終關閉瀏覽器
    return all_comments_data


def crawl_dcard_single_post(post_url):
    # 提取 post_id
    match = re.search(r'/p/(\d+)', post_url)
    post_id = match.group(1) if match else "unknown"

    co = ChromiumOptions()
    possible_paths = [
        r"C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe",
        r"C:\Program Files\Microsoft\Edge\Application\msedge.exe"
    ]
    edge_path = next((p for p in possible_paths if os.path.exists(p)), None)
    if edge_path:
        co.set_browser_path(edge_path)

    print(f"🚀 啟動瀏覽器讀取單一文章: {post_url} ...")
    try:
        page = ChromiumPage(co)
    except Exception as e:
        print(f"❌ 啟動失敗: {e}")
        return []

    all_comments_data = []
    try:
        page.get(post_url)
        page.scroll.to_bottom()
        time.sleep(random.uniform(2.5, 4))

        # 抓取標題
        title = ""
        try:
            h1_el = page.ele('xpath://h1')
            if h1_el:
                title = h1_el.text
        except Exception:
            pass
        if not title:
            title = page.title or "未命名文章"

        # 抓取發文時間
        created_at = ""
        try:
            time_el = page.ele('tag:time')
            if time_el:
                created_at = time_el.attr('datetime')
        except Exception:
            pass
        if not created_at:
            created_at = time.strftime("%Y-%m-%dT%H:%M:%SZ", time.gmtime())

        # 抓取留言
        floor_links = page.eles('xpath://a[contains(@href, "/b/")]')
        comments_list = []

        for link in floor_links:
            try:
                container = link.parent(4)
                raw_text = container.text
                if raw_text:
                    cleaned = clean_comment_text(raw_text)
                    if cleaned and cleaned not in comments_list:
                        comments_list.append(cleaned)
            except Exception:
                continue

        for c_content in comments_list:
            all_comments_data.append({
                "ID": post_id,
                "標題": title,
                "連結": post_url,
                "發文時間": created_at,
                "內容": c_content,
                "評論來源": "Dcard"
            })
            
    except Exception as e:
        print(f"❌ 讀取留言失敗: {e}")
    finally:
        page.quit()

    print(f"🎉 單一文章完成！共取得 {len(all_comments_data)} 條留言。")
    return all_comments_data