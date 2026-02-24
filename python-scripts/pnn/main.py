import feedparser
import requests
import urllib3
from bs4 import BeautifulSoup
import sys
import json
from requests import HTTPError
import random
import time

# Suppress the InsecureRequestWarning from urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

# Define problematic Unicode strings as variables to avoid file write/read corruption
PTS_IMAGE_CAPTION_TEXT = "圖 /"

def scrape_article(url):
    """
    Scrapes a news article from the given URL and returns a dictionary
    with 'author', 'text', 'published-date', and 'title'.
    Handles multiple article formats.
    """
    response = requests.get(url, timeout=10, verify=False)
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

def search_news(entries, keyword):
    """Searches for a keyword in the news entries."""
    if not entries:
        return []

    keyword = keyword.lower()
    found_articles = []
    for entry in entries:
        title = entry.get("title", "").lower()
        summary = entry.get("summary", "").lower()
        if keyword in title or keyword in summary:
            found_articles.append(entry)
    return found_articles

def main():
    """Main function to run the news searcher."""
    rss_url = "https://news.pts.org.tw/xml/newsfeed.xml"
    
    print(f"Fetching news from {rss_url}...")
    news_entries = fetch_rss(rss_url)
    
    if not news_entries:
        print("Could not retrieve news. Exiting.")
        return

    print(f"Successfully fetched {len(news_entries)} news articles.")

    print("\n--- All News Articles ---")
    for article in news_entries:
        print(f"{article.title}\n\n{article.summary}")
        print("-" * 40) # Separator for readability
    print("-------------------------\\n")
    
    while True:
        try:
            keyword = input("Enter keyword to search (or Ctrl+C to exit): ").strip()
            if not keyword:
                continue
                
            results = search_news(news_entries, keyword)
            
            if results:
                print(f"\nFound {len(results)} articles matching '{keyword}':")
                for i, article in enumerate(results, 1):
                    print(f"  {i}. {article.title}")
                    print(f"     Link: {article.link}")
                    # print(f"     Summary: {article.summary}") # Summary can be long
                    print("-" * 20)
            else:
                print(f"No articles found matching '{keyword}'.")

        except KeyboardInterrupt:
            print("\nExiting program.")
            break
        except Exception as e:
            print(f"An unexpected error occurred: {e}")

if __name__ == "__main__":
    output = []
    failed = []

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

    try:
        for id in range(a, b, steps):
            url = f"https://news.pts.org.tw/article/{id}"
            print(f"{url}: ", end='')
            
            try:
                scraped_data = scrape_article(url)
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