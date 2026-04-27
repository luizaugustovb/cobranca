#!/usr/bin/env python3
"""
PDF table extractor para CobrancaPRO
Engines suportadas: pdfplumber (padrão), tabula

Uso:
    python pdf_extractor.py --pdf /path/to/file.pdf --engine pdfplumber
    python pdf_extractor.py --pdf /path/to/file.pdf --engine tabula

Saída (stdout): JSON
    {"success": true, "tables": [[[col1, col2], [val1, val2]], ...], "pages": N}
    {"success": false, "error": "mensagem"}
"""

import sys
import json
import argparse


def clean_cell(cell):
    """Normaliza célula para string não nula."""
    if cell is None:
        return ""
    return str(cell).strip()


def extract_pdfplumber(pdf_path: str) -> list:
    import pdfplumber

    all_tables = []
    with pdfplumber.open(pdf_path) as pdf:
        for page in pdf.pages:
            tables = page.extract_tables()
            for table in (tables or []):
                if not table:
                    continue
                cleaned = [[clean_cell(cell) for cell in row] for row in table]
                # Ignora tabelas vazias
                if any(any(c for c in row) for row in cleaned):
                    all_tables.append(cleaned)
    return all_tables


def extract_tabula(pdf_path: str) -> list:
    import tabula

    dfs = tabula.read_pdf(pdf_path, pages="all", multiple_tables=True, silent=True)
    all_tables = []
    for df in dfs:
        if df.empty:
            continue
        df = df.fillna("")
        # Primeira linha = cabeçalhos do DataFrame
        headers = [clean_cell(c) for c in df.columns.tolist()]
        rows = [[clean_cell(v) for v in row] for row in df.values.tolist()]
        all_tables.append([headers] + rows)
    return all_tables


def main():
    parser = argparse.ArgumentParser(description="Extrai tabelas de PDF")
    parser.add_argument("--pdf", required=True, help="Caminho do arquivo PDF")
    parser.add_argument(
        "--engine",
        default="pdfplumber",
        choices=["pdfplumber", "tabula"],
        help="Engine de extração",
    )
    args = parser.parse_args()

    try:
        if args.engine == "tabula":
            tables = extract_tabula(args.pdf)
        else:
            tables = extract_pdfplumber(args.pdf)

        print(json.dumps({"success": True, "tables": tables, "count": len(tables)}))

    except Exception as exc:
        print(json.dumps({"success": False, "error": str(exc)}))
        sys.exit(1)


if __name__ == "__main__":
    main()
