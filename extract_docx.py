import zipfile
import xml.etree.ElementTree as ET
import sys

def extract_text_from_docx(docx_path):
    try:
        with zipfile.ZipFile(docx_path, 'r') as docx:
            xml_content = docx.read('word/document.xml')
            tree = ET.fromstring(xml_content)
            
            # The namespace for w:t (text) nodes
            ns = {'w': 'http://schemas.openxmlformats.org/wordprocessingml/2006/main'}
            
            # Extract text from all <w:t> tags
            text = [node.text for node in tree.findall('.//w:t', ns) if node.text]
            return '\n'.join(text)
    except Exception as e:
        return str(e)

file_path = sys.argv[1]
text = extract_text_from_docx(file_path)
with open("C:\\Users\\Benaya\\Downloads\\makalah_py2.txt", "w", encoding="utf-8") as f:
    f.write(text)
