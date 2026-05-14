import os
from pypdf import PdfReader

pdf_path = "تحليل متطلبات موقع خدمة عملاء - ابداع ميديا.PDF"

try:
    reader = PdfReader(pdf_path)
    text = ""
    for page in reader.pages:
        text += page.extract_text() + "\n"
        
    with open("pdf_text.txt", "w", encoding="utf-8") as f:
        f.write(text)
    print("Extracted successfully.")
except Exception as e:
    print(f"Error: {e}")
