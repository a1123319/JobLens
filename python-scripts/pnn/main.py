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
from requests.adapters import HTTPAdapter
import ssl
import mysql.connector
from mysql.connector import Error
from datetime import datetime

# Suppress the InsecureRequestWarning from urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

# Define problematic Unicode strings as variables to avoid file write/read corruption
PTS_IMAGE_CAPTION_TEXT = "圖 /"

# Source - https://stackoverflow.com/a/78341139
# Posted by Joe Savage
# Retrieved 2026-02-26, License - CC BY-SA 4.0

class CustomHTTPAdapter(HTTPAdapter):

    def init_poolmanager(self, *args, **kwargs):
        # this creates a default context with secure default settings,
        # which enables server certficiate verification using the
        # system's default CA certificates
        context = ssl.create_default_context()

        # alternatively, you could create your own context manually
        # but this does NOT enable server certificate verification
        # context = ssl.SSLContext(ssl.PROTOCOL_TLSv1)

        super().init_poolmanager(*args, **kwargs, ssl_context=context)


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
        "title": title
    }

def fetch_rss(url):
    """Fetches and parses the RSS feed from a given URL."""
    try:
        response = requests.get(url, timeout=10, verify=False)
        response.raise_for_status()  # Raise an exception for bad status codes
        response.encoding = 'utf-8' # Set encoding to utf-8 (assuming RSS feeds are consistently UTF-8)
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

def search_news(driver, query):    
    driver.get(f"https://news.pts.org.tw/search/{query}")
    driver.implicitly_wait(2)
    news_titles = driver.find_elements(by=By.CSS_SELECTOR, value="h2 > a")
    try:
        return [e.get_attribute('href') for e in news_titles]
    except:
        return []

def scrape():
    output = []
    failed = []
    proxies_count = 20
    delay = 10
    session = Session()
    session.headers['User-Agent'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'
    session.mount("https://", CustomHTTPAdapter())

    if len(sys.argv) != 4:
        print('Pass two numbers for the range of news (e.g., 800000 1)\nand a suffix of file')
        exit(1)
    try:
        a = int(sys.argv[1])
        b = int(sys.argv[2])
    except:
        print('Invalid number')
        exit(1)

    suffix = sys.argv[3]

    if a > b:
        steps = -1
        b -= 1
    else:
        steps = 1
        b += 1
    # options = Options()
    # options.page_load_strategy = 'eager'
    # options.add_argument('--headless')

    # driver = webdriver.Chrome(options=options)
    # print(search_news(driver, "台積電"))
    # driver.quit()

    with open('proxies.txt', encoding='utf-8') as f:
        proxy_list = [i.strip() for i in f.readlines()]

    proxies = proxy_list.copy()
    random.shuffle(proxies)

    try:
        for id in range(a, b, steps):
            url = f"https://news.pts.org.tw/article/{id}"
            print(f"{url}: ", end='')
            
            if len(proxies) == 0:
                proxies = proxy_list.copy()
                random.shuffle(proxies)

            proxy = proxies.pop()

            try:
                scraped_data = scrape_article(session, url, proxy)
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
                print(f"failed: {e}")
            
            time.sleep(random.random())
    finally:
        with open(f"news{suffix}.json", encoding="utf-8", mode="w") as f:
            json.dump(output, f, indent=2, ensure_ascii=False)
        with open(f"failed{suffix}.json", encoding="utf-8", mode="w") as f:
            json.dump(failed, f, indent=2 ,ensure_ascii=False)

def in_database(db, url):
    id = int(url.split('/')[-1])
    with db.cursor() as cursor:
        cursor.execute("SELECT Id from pnn WHERE Id = %s", (id,))
        cursor.fetchall()

        return cursor.rowcount > 0

def update_pnn_with_rss(db, pnn):
    id = int(url.split('/')[-1])
    with db.cursor() as cursor:
        if in_database(db, url):
            print(f'{url} is already recorded')
            return

        cursor.execute(
            r"INSERT INTO pnn (Id, Title, Text, PublishedTime, UpdatedTime, Url) VALUES (%s, %s, %s, %s, %s, %s)",
            (id,
            pnn['title'],
            pnn['text'],
            pnn['published-date'],
            pnn['modified-date'] if pnn['modified-date'] != "" else None,
            pnn['url'],)
        )

        db.commit()

def main():
    user = os.environ['MYSQL_USERNAME']
    password = os.environ['MYSQL_PASSWORD']
    ip = os.environ.get('MYSQL_IP', "localhost")
    rss_url = "https://news.pts.org.tw/xml/newsfeed.xml"
    session = Session()
    session.headers['User-Agent'] = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36'
    now = datetime.now()

    db_connection = mysql.connector.connect(
        host=ip,
        user=user,
        passwd=password,
        database="joblens",
    )
    
    if not db_connection.is_connected():
        print('Failed to connect MySQL. Exiting.')
    
    print(f"Fetching news from {rss_url}...")
    news_entries = fetch_rss(rss_url)

    print(f"Successfully fetched {len(news_entries)} news articles.")
    print(f"\n--- RSS {now.strftime('%Y-%m-%dT%H:%M:%S')} ---")
    scraped_res = []

    for article in news_entries:
        print(f"{article.title}\n\n{article.summary}")
        print("-" * 40) # Separator for readability

    for article in news_entries:
        try:
            if in_database(db_connection, article.id):
                print(f'Skipped {article.id}: Found in DB.')
                continue

            print(f'Scraping "{article.id}"')
            scraped_res.append(scrape_article(session, article.id) | {'url': article.id})
            update_pnn_with_rss(db_connection, scraped_res[-1])
        except Exception as e:
            print(f"Failed: {e}")
            print('Wait 60s until the next try')
            time.sleep(60)

    with open(f"rss_{now.strftime('%Y-%m-%dT%H_%M_%S')}.json", 'w', encoding='utf-8') as f:
        json.dump(scraped_res, f, ensure_ascii=False, indent=2)

    db_connection.close()
            
if __name__ == "__main__":
    main()
