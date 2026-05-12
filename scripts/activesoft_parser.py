#!/usr/bin/env python3
"""
Parser específico para "Relação de Títulos de Cobrança" do Activesoft Gestão Acadêmica.

Saída JSON:
{
  "success": true,
  "responsaveis": [
    {
      "nome": "ALLAN TAKAAKI SUZUKI",
      "cpf": "087.657.294-80",
      "telefone": "84 99661-8245",
      "rua": "R. FREI MIGUELINO",
      "numero": "1340",
      "bairro": "NOVA BETANIA",
      "cidade": "MOSSORO",
      "estado": "RN",
      "cep": "59607250",
      "itens": [
        {
          "numero_titulo": "628808",
          "situacao": "Em aberto",
          "vencimento": "28/02/2025",
          "parcela": "02/12",
          "servico": "Mens. EF 1 ao 5 ano - Manha",
          "aluno": "VINICIUS NAOEI GUERRA SUZUKI",
          "valor_servico": "1.032,00",
          "multa_juros": "46,82",
          "valor_receber": "1.078,82"
        },
        ...
      ]
    }
  ],
  "total_itens": N
}

Uso:
    python activesoft_parser.py --pdf /caminho/do/arquivo.pdf
"""

import sys
import json
import re
import argparse


# ---------------------------------------------------------------------------
# Helpers
# ---------------------------------------------------------------------------

def clean(s):
    if s is None:
        return ""
    return str(s).strip()


def is_caps_word(token):
    """Verifica se o token é uma palavra composta apenas de letras maiúsculas (Unicode)."""
    return bool(re.match(r'^[A-ZÁÉÍÓÚÀÂÊÎÔÛÃÕÇ]+$', token, re.UNICODE))


# Algarismos romanos comuns em nomes de fundos/cursos — não fazem parte do nome do aluno
_ROMAN_NUMERALS = re.compile(
    r'^M{0,3}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$',
    re.IGNORECASE
)


def _is_name_token(token):
    """Token válido para compor nome de aluno: all-caps e não é algarismo romano."""
    return is_caps_word(token) and not _ROMAN_NUMERALS.match(token)


def split_service_aluno(text):
    """
    Divide 'SERVICO ALUNO_NOME' separando o serviço do nome do aluno.
    O nome do aluno é o último grupo contíguo de 2+ tokens ALL-CAPS
    que não sejam algarismos romanos (I, II, III, IV, V...).
    Ex: 'Mens. EF 1 ao 5 ano - Manha VINICIUS NAOEI GUERRA SUZUKI'
        → ('Mens. EF 1 ao 5 ano - Manha', 'VINICIUS NAOEI GUERRA SUZUKI')
    Ex: 'Anuidade - Formacao Crista - Fund I VINICIUS NAOEI GUERRA SUZUKI'
        → ('Anuidade - Formacao Crista - Fund I', 'VINICIUS NAOEI GUERRA SUZUKI')
    """
    tokens = text.split()
    n = len(tokens)
    best_start = None

    i = n - 1
    while i >= 0:
        if _is_name_token(tokens[i]):
            # Procura sequência contígua de tokens de nome a partir de i para esquerda
            j = i
            while j >= 0 and _is_name_token(tokens[j]):
                j -= 1
            run_length = i - j
            if run_length >= 2:
                best_start = j + 1
                break
            # Sequência de 1 → pula e continua procurando mais para a esquerda
            i = j - 1
        else:
            i -= 1

    if best_start is not None:
        service = ' '.join(tokens[:best_start]).strip()
        aluno = ' '.join(tokens[best_start:]).strip()
        return service, aluno

    return text.strip(), ''


def parse_valor(s):
    """Remove '-' solitário e '--', retorna string numérica ou '0,00'."""
    s = clean(s)
    if s in ('--', '-', ''):
        return '0,00'
    return s


# ---------------------------------------------------------------------------
# Extração de texto do PDF
# ---------------------------------------------------------------------------

def extract_text(pdf_path):
    import pdfplumber
    lines = []
    with pdfplumber.open(pdf_path) as pdf:
        for page in pdf.pages:
            text = page.extract_text(x_tolerance=2, y_tolerance=2)
            if text:
                for line in text.split('\n'):
                    stripped = line.strip()
                    if stripped:
                        lines.append(stripped)
    return lines


# ---------------------------------------------------------------------------
# Padrões de regex
# ---------------------------------------------------------------------------

# "Responsável: NOME - CPF/CNPJ: XXX - Telefone residencial: XXX ..."
RE_RESPONSAVEL = re.compile(
    r'^Respons[aá]vel:\s+(.+?)\s+-\s+CPF/CNPJ:\s+([\d.\-/]+)\s*-\s*Telefone\s+residencial:\s*([^\-]+)',
    re.IGNORECASE | re.UNICODE
)

# "Endereço: STREET, NUM / BAIRRO / CIDADE-UF / CEP: XXXXX"
RE_ENDERECO = re.compile(r'^Endere[cç]o:\s+(.+)$', re.IGNORECASE | re.UNICODE)

# Título principal: "628808 Em aberto -- 28/02/2025 ..."
RE_TITULO = re.compile(
    r'^(\d{5,})\s+(Em aberto|Pago|Cancelado|Negociado|Liquidado|Em atraso)\s+',
    re.IGNORECASE | re.UNICODE
)

# Data vencimento dentro de linha
RE_DATE = re.compile(r'(\d{2}/\d{2}/\d{4})')

# Parcela line: "NN/NNN SERVICE ALUNO NUM NUM NUM ..."
# Número formato: 1.032,00 ou 0,00 ou --
NUM = r'(?:\d{1,3}(?:\.\d{3})*,\d{2}|--)'
RE_PARCELA = re.compile(
    r'^(\d{2}/\d{2,3})\s+(.+?)\s+(' +
    NUM + r')\s+(' + NUM + r')\s+(' + NUM + r')\s+(' + NUM + r')\s+(' +
    NUM + r')\s+(' + NUM + r')\s+(' + NUM + r')$'
)

# Linha de total do responsável → marca fim do bloco
RE_TOTAL = re.compile(r'^Total de t[ií]tulos do respons[aá]vel:', re.IGNORECASE)

# Celular (em linha de responsável) — para em ' - ' (separador de campo), não no '-' do número
RE_CELULAR = re.compile(r'Celular:\s*([\d\s()\-\.]+?)(?=\s+-\s+|$)', re.IGNORECASE)


# ---------------------------------------------------------------------------
# Parser principal
# ---------------------------------------------------------------------------

def parse_responsavel_line(line):
    """Extrai nome, CPF e telefone da linha de responsável."""
    m = RE_RESPONSAVEL.match(line)
    if not m:
        return None

    nome = clean(m.group(1))
    cpf = clean(m.group(2))
    tel_res = clean(m.group(3))
    if '(N' in tel_res or 'nao' in tel_res.lower():
        tel_res = ''

    # Tenta pegar celular da mesma linha (preferência: celular > residencial)
    mc = RE_CELULAR.search(line)
    if mc and '(N' not in mc.group(1) and 'nao' not in mc.group(1).lower():
        telefone = clean(mc.group(1))
    else:
        # Telefone residencial: reextrair para não cortar no '-' do número
        m_tel = re.search(r'Telefone\s+residencial:\s*([\d\s()\-\.]+?)(?=\s+-\s+|$)', line, re.IGNORECASE)
        if m_tel and '(N' not in m_tel.group(1):
            telefone = clean(m_tel.group(1))
        else:
            telefone = tel_res

    return {'nome': nome, 'cpf': cpf, 'telefone': telefone}


def parse_endereco_line(line):
    """Extrai endereço completo."""
    m = RE_ENDERECO.match(line)
    if not m:
        return {}

    addr = clean(m.group(1))
    # Formato típico: "R. FREI MIGUELINO, 1340 / NOVA BETANIA / MOSSORÓ-RN / CEP: 59607250"
    parts = [p.strip() for p in addr.split('/')]

    rua = ''
    numero = ''
    bairro = ''
    cidade = ''
    estado = ''
    cep = ''

    if len(parts) >= 1:
        # Parte 0: "R. FREI MIGUELINO, 1340" ou "R. FREI MIGUELINO 1340" ou só "R. FREI MIGUELINO"
        rua_num = parts[0]
        # Tenta separar pela última vírgula seguida de número
        m_rn = re.match(r'^(.+?),\s*(\S+(?:\s+[A-Z0-9]+)?)\s*$', rua_num)
        if m_rn:
            rua_candidate = clean(m_rn.group(1))
            num_candidate = clean(m_rn.group(2))
            # O número deve ser predominantemente numérico ou alfanumérico simples
            if re.match(r'^[\w\-]+$', num_candidate):
                rua = rua_candidate
                numero = num_candidate
            else:
                rua = rua_num
        else:
            # Tenta separar pelo último espaço antes de número
            m_rn2 = re.search(r'^(.+)\s+(\d[\w\-]*)$', rua_num)
            if m_rn2:
                rua = clean(m_rn2.group(1))
                numero = clean(m_rn2.group(2))
            else:
                rua = rua_num

    if len(parts) >= 2:
        bairro = parts[1].strip()

    if len(parts) >= 3:
        cidade_uf = parts[2].strip()
        # "MOSSORÓ-RN" ou "MOSSORO-RN"
        m_cuf = re.match(r'^(.+)-([A-Z]{2})$', cidade_uf)
        if m_cuf:
            cidade = m_cuf.group(1).strip()
            estado = m_cuf.group(2).strip()
        else:
            cidade = cidade_uf

    if len(parts) >= 4:
        cep_part = parts[3]
        m_cep = re.search(r'CEP[:\s]*(\d{5}-?\d{3}|\d{8})', cep_part, re.IGNORECASE)
        if m_cep:
            cep = re.sub(r'\D', '', m_cep.group(1))

    return {
        'rua': rua,
        'numero': numero,
        'bairro': bairro,
        'cidade': cidade,
        'estado': estado,
        'cep': cep,
    }


def parse_titulo_line(line):
    """Extrai número do título, situação e data de vencimento."""
    m = RE_TITULO.match(line)
    if not m:
        return None

    numero = m.group(1)
    situacao = m.group(2)

    date_m = RE_DATE.search(line)
    vencimento = date_m.group(1) if date_m else ''

    return {'numero': numero, 'situacao': situacao, 'vencimento': vencimento}


# Padrão de número monetário brasileiro: 1.032,00 ou --
_NUM_RE = re.compile(r'(?:\d{1,3}(?:\.\d{3})*,\d{2}|--)')


def parse_parcela_line(line):
    """
    Extrai dados de uma linha de parcela/serviço.
    Estratégia robusta: encontra os últimos 7 números no final da linha.
    Colunas (último→primeiro): valor_receber | devolvido | recebido | multa_juros |
                               desc_concedido | desc_nc | valor_servico
    """
    # Linha deve começar com NN/NN ou NN/NNN (parcela)
    m_inicio = re.match(r'^(\d{2}/\d{2,3})\s+', line)
    if not m_inicio:
        return None

    parcela = m_inicio.group(1)
    rest = line[m_inicio.end():]

    # Encontra todas as ocorrências de NUM no restante da linha
    all_nums = list(_NUM_RE.finditer(rest))

    # Precisa de pelo menos 7 valores numéricos
    if len(all_nums) < 7:
        return None

    # Os últimos 7 são as colunas do relatório
    nums = all_nums[-7:]

    # O texto do meio é tudo antes do início do primeiro dos últimos 7 NUMs
    text_end_pos = nums[0].start()
    text_middle = rest[:text_end_pos].strip()

    if not text_middle:
        return None

    valor_servico = nums[0].group()   # coluna 1
    # nums[1] = desc_nc, nums[2] = desc_cond, nums[3] = desc_concedido
    multa_juros   = nums[4].group()   # coluna 5
    # nums[5] = recebido
    valor_receber = nums[6].group()   # coluna 7

    servico, aluno = split_service_aluno(text_middle)

    return {
        'parcela': parcela,
        'servico': servico,
        'aluno': aluno,
        'valor_servico': parse_valor(valor_servico),
        'multa_juros': parse_valor(multa_juros),
        'valor_receber': parse_valor(valor_receber),
    }


def parse_lines(lines):
    responsaveis = []
    current_resp = None
    current_titulo = None

    i = 0
    while i < len(lines):
        line = lines[i]

        # ---- Responsável ----
        resp_data = parse_responsavel_line(line)
        if resp_data:
            if current_resp is not None:
                # Mesmo CPF = cabeçalho repetido de página de continuação
                # Não empurra, não reseta current_titulo
                if current_resp.get('cpf') == resp_data['cpf']:
                    i += 1
                    continue
                # CPF diferente = responsável novo
                responsaveis.append(current_resp)
            current_resp = {**resp_data,
                            'rua': '', 'numero': '', 'bairro': '',
                            'cidade': '', 'estado': '', 'cep': '',
                            'itens': []}
            current_titulo = None
            i += 1
            continue

        # ---- Endereço ----
        if current_resp and line.lower().startswith('endere'):
            addr = parse_endereco_line(line)
            # Atualiza apenas se ainda não temos endereço (evita sobrescrever em pág. de continuação)
            if not current_resp.get('rua'):
                current_resp.update(addr)
            i += 1
            continue

        # ---- Total (fim do bloco do responsável) ----
        if RE_TOTAL.match(line):
            current_titulo = None
            i += 1
            continue

        # ---- Título (invoice group) ----
        titulo_data = parse_titulo_line(line)
        if titulo_data and current_resp is not None:
            current_titulo = titulo_data
            i += 1
            continue

        # ---- Parcela/serviço ----
        if current_titulo is not None and current_resp is not None:
            parcela_data = parse_parcela_line(line)
            if parcela_data:
                item = {
                    'numero_titulo': current_titulo['numero'],
                    'situacao': current_titulo['situacao'],
                    'vencimento': current_titulo['vencimento'],
                    **parcela_data
                }
                current_resp['itens'].append(item)
                i += 1
                continue

        i += 1

    if current_resp is not None:
        responsaveis.append(current_resp)

    return responsaveis


# ---------------------------------------------------------------------------
# Entry point
# ---------------------------------------------------------------------------

def main():
    parser = argparse.ArgumentParser(description='Parser Activesoft PDF')
    parser.add_argument('--pdf', required=True, help='Caminho do PDF')
    parser.add_argument('--out', default=None, help='Arquivo de saída JSON (opcional; usa stdout se omitido)')
    args = parser.parse_args()

    try:
        lines = extract_text(args.pdf)
        responsaveis = parse_lines(lines)

        total_itens = sum(len(r['itens']) for r in responsaveis)

        result = json.dumps({
            'success': True,
            'responsaveis': responsaveis,
            'total_itens': total_itens,
        }, ensure_ascii=False)

        if args.out:
            with open(args.out, 'w', encoding='utf-8') as f:
                f.write(result)
        else:
            print(result)

    except Exception as exc:
        error_result = json.dumps({'success': False, 'error': str(exc)})
        if args.out:
            try:
                with open(args.out, 'w', encoding='utf-8') as f:
                    f.write(error_result)
            except Exception:
                pass
        else:
            print(error_result)
        sys.exit(1)


if __name__ == '__main__':
    main()
