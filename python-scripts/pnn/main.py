from concurrent.futures import ThreadPoolExecutor
from dataclasses import dataclass
import os
import feedparser
import requests
import urllib3
from bs4 import BeautifulSoup
import sys
import json
from requests import HTTPError
import random
import time
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.common.proxy import Proxy
from selenium.webdriver.common.proxy import ProxyType
from requests import Session
from requests.adapters import HTTPAdapter, Retry
import ssl
import pymysql
from playsound3 import playsound
from datetime import datetime
import traceback
from concurrent.futures import ThreadPoolExecutor, as_completed

# Suppress the InsecureRequestWarning from urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

# Define problematic Unicode strings as variables to avoid file write/read corruption
PTS_IMAGE_CAPTION_TEXT = "圖 /"
USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'

def scrape_article(session: Session, url, proxy=None):
    """
    Scrapes a news article from the given URL and returns a dictionary
    with 'author', 'text', 'published-date', and 'title'.
    Handles multiple article formats.
    """

    if isinstance(proxy, str):
        proxy = {'http': proxy, 'https': proxy}

    response = session.get(url, timeout=10, verify=False, proxies=proxy)

    response.raise_for_status()
    
    # Use apparent_encoding for BeautifulSoup, which requests derives from headers or content
    soup = BeautifulSoup(response.content, 'html.parser', from_encoding=response.apparent_encoding)

    # Extract Title
    title_tag = soup.body.find('h1', class_='article-title')
    if not title_tag:
        title_tag = soup.body.h1
    if not title_tag:
        title_tag = soup.title
    title = title_tag.string.strip() if title_tag else None

    published_date_tag = soup.head.find('meta', attrs={'property': 'article:published_time'})
    if published_date_tag is not None and published_date_tag.attrs['content']:
        published_date = published_date_tag['content']
    else:
        published_date = None

    modified_date_tag = soup.head.find('meta', attrs={'property': 'article:modified_time'})

    if modified_date_tag is not None and modified_date_tag.attrs['content']:
        modified_date = modified_date_tag['content']
    else:
        modified_date = None

    thumbnail_tag = soup.head.find('meta', attrs={'property': 'og:image'})

    if thumbnail_tag is not None and thumbnail_tag.attrs['content']:
        thumbnail = thumbnail_tag['content']
    else:
        thumbnail = None

    # Extract Text (handles both formats)
    article_content_div = soup.find('div', class_='post-article') # New format
    if not article_content_div:
        article_content_div = soup.find('div', class_='article-content') # Old format
    
    text_paragraphs = []
    if article_content_div:
        all_p_tags = article_content_div.find_all('p')
        if all_p_tags:
            for p_tag in all_p_tags:
                paragraph_text = p_tag.get_text(strip=True)
                if paragraph_text and not paragraph_text.startswith(PTS_IMAGE_CAPTION_TEXT):
                    text_paragraphs.append(paragraph_text)
        else:
            direct_text = article_content_div.get_text(separator="\n", strip=True)
            if direct_text:
                for line in direct_text.split('\n'):
                    line_strip = line.strip()
                    if line_strip and not line_strip.startswith(PTS_IMAGE_CAPTION_TEXT):
                        text_paragraphs.append(line_strip)
    
    text = "\n\n".join(text_paragraphs) if text_paragraphs else None

    return {
        "text": text,
        "published-date": published_date,
        "modified-date": modified_date,
        "title": title,
        "thumbnail": thumbnail,
    }

def fetch_rss(url):
    """Fetches and parses the RSS feed from a given URL."""
    try:
        response = requests.get(url, timeout=10, verify=False)
        response.raise_for_status()
        response.encoding = 'utf-8'
        feed = feedparser.parse(response.content)
        return feed.entries
    except requests.exceptions.RequestException as e:
        print(f"Error fetching RSS feed: {e}")
        return None

def search_rss(rss_feeds, keyword):
    """Searches for a keyword in the news entries."""
    if not rss_feeds:
        return []

    keyword = keyword.lower()
    found_articles = []
    for entry in rss_feeds:
        title = entry.get("title", "").lower()
        summary = entry.get("summary", "").lower()
        if keyword in title or keyword in summary:
            found_articles.append(entry)
    return found_articles

def scrape(start: int, end: int):
    now = datetime.now().strftime("%Y-%m-%d_%H_%M_%S")
    output = []
    failed = []
    session = Session()

    if start > end:
        steps = -1
        end -= 1
    else:
        steps = 1
        end += 1
    try:
        with open("pnn/user-agents.json", encoding='utf-8', mode='r') as f:
            user_agents = [f['ua'] for f in json.load(f)]
    except: 
        user_agents = [USER_AGENT]

    retries = Retry(
        total=5,                  # Total retries before giving up
        backoff_factor=1,         # Waits 1s, 2s, 4s, 8s... between retries
        status_forcelist=[429], # Trigger on these codes
        raise_on_status=False
    )
    session.mount('https://', HTTPAdapter(max_retries=retries))

    try:
        SAVE_INTERVAL = 200
        next_save = SAVE_INTERVAL
        for id in range(start, end, steps):
            url = f"https://news.pts.org.tw/article/{id}"
            print(f"{url}: ", end='')
            try:
                session.headers['User-Agent'] = random.choice(user_agents)
                scraped_data = scrape_article(session, url)
                scraped_data["href"] = url
                output.append(scraped_data)
                print(f"succeeded")
            except HTTPError as e:
                data = {
                    "id": id,
                    "href": f"https://news.pts.org.tw/article/{id}",
                    "reason": str(e),
                }
                
                failed.append(data)

                try:
                    if e.status_code != 404:
                        playsound("pnn/error.mp3")
                    if e.status_code == 429:
                        time.sleep(6000)
                except:
                    pass
                print(f"failed: {e}")
            
            next_save -= 1
            if next_save <= 0:
                write_output_files(now, output, failed)
                next_save = SAVE_INTERVAL
            time.sleep(random.random())
    finally:
        write_output_files(now, output, failed)

# ── Proxy session pool ────────────────────────────────────────────────────────
class ProxySession:
    def __init__(self, proxy_url: str):
        self.proxy_url = proxy_url
        self.session = Session()
        self.session.proxies = {"http": proxy_url, "https": proxy_url}
        self.session.headers["User-Agent"] = USER_AGENT
 
    def close(self):
        self.session.close()
 
 
@dataclass
class ArticleResult:
    id: int
    url: str
    success: bool
    data: dict | None = None
    reason: str | None = None
 
def _fetch_one(proxy_session: ProxySession, article_id: int) -> ArticleResult:
    """Worker: fetch a single article through the given proxy session."""
    url = f"https://news.pts.org.tw/article/{article_id}"
    try:
        scraped_data = scrape_article(proxy_session.session, url)
        scraped_data["href"] = url
        return ArticleResult(id=article_id, url=url, success=True, data=scraped_data)
 
    except HTTPError as e:
        reason = str(e)
        # Surface non-404 errors loudly (mirrors original playsound behaviour)
        status = getattr(e.response, "status_code", None)
        if status and status != 404:
            print(f"  ⚠ Non-404 HTTP error for {url}: {reason}")
        return ArticleResult(id=article_id, url=url, success=False, reason=reason)
 
    except Exception as e:
        return ArticleResult(id=article_id, url=url, success=False, reason=str(e))
 
 
def scrape_with_proxy(start: int, end: int, proxies: list[str], max_workers=1) -> None:
    now = datetime.now().strftime("%Y-%m-%d_%H_%M_%S")
    print(proxies)
    # Build ID list (handles both ascending and descending ranges)
    if start > end:
        ids = list(range(start, end - 1, -1))
    else:
        ids = list(range(start, end + 1))
 
    # Create one session per proxy; shuffle so load distributes randomly
    proxy_sessions = [ProxySession(p) for p in proxies]
    random.shuffle(proxy_sessions)
 
    output: list[dict] = []
    failed: list[dict] = []
 
    print(f"Fetching {len(ids)} articles across {len(proxy_sessions)} proxies "
          f"({max_workers} workers)…\n")
 
    try:
        with ThreadPoolExecutor(max_workers=max_workers) as executor:
            # Assign each article ID to a random proxy session (round-robin)
            futures = {
                executor.submit(
                    _fetch_one,
                    proxy_sessions[i % len(proxy_sessions)],
                    article_id,
                ): article_id
                for i, article_id in enumerate(ids)
            }
 
            for future in as_completed(futures):
                result: ArticleResult = future.result()
 
                if result.success:
                    output.append(result.data)
                    print(f"  ✓ {result.url}")
                else:
                    failed.append({
                        "id":     result.id,
                        "href":   result.url,
                        "reason": result.reason,
                    })
                    print(f"  ✗ {result.url}  ({result.reason})")
 
    finally:
        write_output_files(now, output, failed)
 
        for ps in proxy_sessions:
            ps.close()
 
        print(f"\n── Done ──────────────────────────────────────────")
        print(f"  Succeeded : {len(output)}")
        print(f"  Failed    : {len(failed)}")

def in_database(db, url):
    id = int(url.split('/')[-1])
    with db.cursor() as cursor:
        cursor.execute("SELECT Id from pnn WHERE Id = %s", (id,))
        cursor.fetchall()

        return cursor.rowcount > 0
    
def clean_datetime(datetime_str: str):
    dt = datetime.fromisoformat(datetime_str)
    return dt.strftime("%Y-%m-%d %H:%M:%S")

def update_pnn_with_rss(db, pnn):
    id = int(pnn['url'].split('/')[-1])
    with db.cursor() as cursor:
        if in_database(db, pnn['url']):
            print(f'{pnn['url']} is already recorded')
            return

        try:
            cursor.execute(
                r"""INSERT INTO pnn (Id, Title, Text, PublishedTime, UpdatedTime, Url) VALUES (%s, %s, %s, %s, %s, %s) 
                ON DUPLICATE KEY UPDATE
                    Title         = VALUES(Title),
                    Text          = VALUES(Text),
                    PublishedTime = VALUES(PublishedTime),
                    UpdatedTime   = VALUES(UpdatedTime)""",
                (id,
                pnn['title'],
                pnn['text'],
                clean_datetime(pnn['published-date']),
                clean_datetime(pnn['modified-date']) if pnn['modified-date'] != "" else None,
                pnn['url'],)
            )
        finally:
            db.commit()

def write_output_files(time: str, success: dict | list[dict], failed: dict | list[dict]):
    with open(f"news-{time}.json", encoding="utf-8", mode="w") as f:
        json.dump(success, f, indent=2, ensure_ascii=False)
    with open(f"failed-{time}.json", encoding="utf-8", mode="w") as f:
        json.dump(failed, f, indent=2, ensure_ascii=False)

def main():
    start = None
    end = None
    use_proxy = False
    if len(sys.argv) >= 3:
        start = int(sys.argv[1])
        end = int(sys.argv[2])

    if len(sys.argv) == 4 and sys.argv[3].strip().lower() in ("proxy", "--proxy"):
        use_proxy = True

    user = os.environ['MYSQL_USER']
    password = os.environ['MYSQL_PASS']
    ip = os.environ.get('MYSQL_IP', "localhost")
    rss_url = "https://news.pts.org.tw/xml/newsfeed.xml"
    session = Session()
    session.headers['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'

    try:
        db_connection = pymysql.connect(
            host=ip,
            user=user,
            password=password,
            database="joblens",
        )
        
        print(f"Fetching news from '{rss_url}'...")
        news_entries = fetch_rss(rss_url)

        print(f"Successfully fetched {len(news_entries)} news articles.")
        scraped_res = []

        for article in news_entries:
            try:
                if in_database(db_connection, article.id):
                    print(f'Skipped {article.id}: Found in DB.')
                    continue

                print(f'Scraping "{article.id}"')
                scraped_res.append(scrape_article(session, article.id) | {'url': article.id})
            except Exception as e:
                print(f"Failed: {e}")
                print('Wait 60s until the next try')
                time.sleep(60)

            update_pnn_with_rss(db_connection, scraped_res[-1])
        
        if start is not None and end is not None:
            if use_proxy:
                with open('pnn/proxies.txt', encoding='utf-8') as f:
                    proxies = [i.strip() for i in f.readlines()]
                    TW_proxies = [
                        "http://118.163.99.115:443",
                        "http://193.42.43.36:443",
                        "http://203.69.23.34:443",
                        "socks4://125.228.94.232:4145",
                        "socks4://125.228.143.207:4145",
                        "socks4://125.228.94.153:4145",
                    ]

                    for proxy in TW_proxies:
                        if proxy in proxies:
                            proxies.remove(proxy)
                    scrape_with_proxy(start, end, proxies, 20)
            else:
                scrape(start, end)
    except:
        traceback.print_exc()
    finally:
        db_connection.close()
            
if __name__ == "__main__":
    main()
