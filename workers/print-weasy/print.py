#!/usr/bin/env python

from weasyprint import HTML

HTML('http://print:3000/').write_pdf('./data/page.pdf')

print("PDF printed successfuly")