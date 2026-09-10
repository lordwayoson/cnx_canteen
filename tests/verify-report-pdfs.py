from pypdf import PdfReader
import json, re
from collections import Counter
from pathlib import Path
base = Path('storage/reports-qa')
fixture = json.loads((base/'fixture.json').read_text())
for name in ['multi-page.pdf','browser-large.pdf','empty.pdf','http-export.pdf','browser-print.pdf']:
    reader = PdfReader(base/name)
    text = '\n'.join(page.extract_text() or '' for page in reader.pages)
    assert 'Concentrix Ghana' in text, name
    assert len(reader.pages[0].images) >= 1, (name, 'logo missing')
    assert 'Warning:' not in text and 'Notice:' not in text, name
    assert 'Internal Use Only' not in text, name
    assert 'Data Scope' not in text and 'Report Type' not in text, name
    for page in reader.pages:
        assert 'Concentrix Ghana - Canteen Management System' in (page.extract_text() or ''), (name, 'page footer missing')
    if name in ['multi-page.pdf','browser-large.pdf']:
        assert len(reader.pages) > 1
        assert Counter(re.findall(r'QA meal (\d{3})', text)) == Counter(f'{i:03d}' for i in range(1,181)), name
        for i in range(1,181):
            assert f'QA meal {i:03d}' in text, (name,i)
        assert fixture['reference'] in text
        assert fixture['generated_by'] in text
    print(name, len(reader.pages), 'pages, readable text and content checks passed')
texts = [PdfReader(base/name).pages[0].extract_text() for name in ['http-export.pdf','browser-print.pdf']]
refs = [re.search(r'CANT-[A-Z0-9-]+',t).group() for t in texts]
assert refs[0] == refs[1], refs
print('HTTP PDF and browser print use the same report reference and metadata.')
