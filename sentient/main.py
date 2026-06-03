# CkipPos tags: https://ckip-transformers.readthedocs.io/en/latest/main/tag.html

import collections
from transformers import pipeline
from ckip_transformers.nlp import CkipWordSegmenter, CkipPosTagger
import pymysql

# --- Configuration ---
DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "ggyy1155",
    "database": "joblens",
    "charset": "utf8mb4",
    "autocommit": True
}

model_id = "IDEA-CCNL/Erlangshen-Roberta-110M-Sentiment"

print("Initializing NLP models on GPU...")

# Change device=0 to select other GPUs.
ws_driver = CkipWordSegmenter(model="bert-base", device=0)
pos_driver = CkipPosTagger(model="bert-base", device=0)
sentiment_classifier = pipeline("sentiment-analysis", model=model_id, device=0)

def get_db_connection():
    return pymysql.connect(**DB_CONFIG)

def process_and_save():
    conn = get_db_connection()
    try:
        with conn.cursor(pymysql.cursors.DictCursor) as cursor:
            print("Fetching individual comments from database...")
            cursor.execute("""
                SELECT Id, CompanyId, Content 
                FROM comment 
                WHERE Content IS NOT NULL AND Content != ''
            """)
            rows = cursor.fetchall()
            
            if not rows:
                print("No comments found to process.")
                return
            
            comment_ids = [row['Id'] for row in rows]
            contents = [row['Content'] for row in rows]

            print(f"Total comments fetched: {len(contents)}")

            # 1. Batch execution for Sentiment Analysis
            print("Running sentiment analysis on comments...")
            sentiment_results = sentiment_classifier(contents)

            # 2. Batch execution for Word Segmentation and POS Tagging
            print("Segmenting comment text into words and tagging part of speech...")
            segmented_comments = ws_driver(contents, use_delim=True)
            pos_results = pos_driver(segmented_comments, use_delim=True)
            zipped_nlp_results = list(zip(segmented_comments, pos_results))

            # Containers for bulk operations
            comment_update_records = []
            wordcloud_insert_records = []
            
            # 3. Process results
            for c_id, nlp_res, sentiment in zip(comment_ids, zipped_nlp_results, sentiment_results):
                # Evaluate sentiment properties
                is_positive = 1 if 'positive' in sentiment['label'].lower() or '1' in sentiment['label'] else 0
                score = float(sentiment['score']) # components might expect standard python float
                
                # Record for comment table update
                comment_update_records.append((is_positive, score, c_id))
                
                words, pos_tags = nlp_res
                for word, part_of_speech in zip(words, pos_tags):
                    # Filter out unwanted punctuation/structural tags
                    if part_of_speech in [
                        "V_2", 
                        "DE", 
                        "SHI", 
                        "FW", 
                        "COLONCATEGORY", 
                        "COMMACATEGORY",
                        "DASHCATEGORY", 
                        "DOTCATEGORY", 
                        "ETCCATEGORY", 
                        "EXCLAMATIONCATEGORY",
                        "PARENTHESISCATEGORY", 
                        "PAUSECATEGORY", 
                        "PERIODCATEGORY",
                        "QUESTIONCATEGORY", 
                        "SEMICOLONCATEGORY", 
                        "SPCHANGECATEGORY", 
                        "WHITESPACE"
                    ]: 
                        continue
                        
                    # Record for wordcloud table insert: (CommentSource, Content, Pos)
                    wordcloud_insert_records.append((
                        c_id,          # CommentSource (Id of the comment)
                        word,          # Content (The actual word)
                        part_of_speech # Pos (Part of speech tag)
                    ))

            # 4. Bulk Update 'comment' table
            if comment_update_records:
                print(f"Updating {len(comment_update_records)} rows in 'comment' table...")
                update_comment_query = """
                    UPDATE comment 
                    SET Emotion = %s, Confidence = %s 
                    WHERE Id = %s
                """
                cursor.executemany(update_comment_query, comment_update_records)

            # 5. Bulk Insert into 'wordcloud' table
            if wordcloud_insert_records:
                print(f"Writing {len(wordcloud_insert_records)} words to 'wordcloud' table...")
                insert_wordcloud_query = """
                    INSERT INTO wordcloud (CommentSource, Content, Pos)
                    VALUES (%s, %s, %s)
                """
                cursor.executemany(insert_wordcloud_query, wordcloud_insert_records)
                
            print("Database processing completed successfully!")
            
    except Exception as e:
        print(f"An error occurred: {e}")
    finally:
        conn.close()

if __name__ == "__main__":
    process_and_save()