"""Convert the KDP manuscript PDF into resources/book/chapters/*.md.

Typography in the PDF:
  18pt bold   -> "Table of Contents" (skipped) / "Sources and Notes"
  17pt bold   -> chapter / front-matter title
  12pt bold   -> chapter name inside Sources and Notes
  11pt bold   -> section heading (##)
  11pt italic -> dedication
  10.5 bold-italic whole line -> sub-heading (###)
  10.5 bold "N. " lead        -> numbered practice step
  10.5 regular -> body; paragraphs begin with a first-line indent (x ~ 57.7)
  9pt          -> source notes (hanging indent)

Usage: python3 convert.py <manuscript.pdf> <out-dir>
"""

import os
import re
import sys

import fitz

SRC, OUT = sys.argv[1], sys.argv[2]
# Offsets from each page's left margin (odd/even pages have different gutters).
LEFT, INDENT = 0.0, 12.9

# Output filenames match config/book.php.
CHAPTER_FILES = {
    1: "01-not-fighting-anything.md",
    2: "02-self-will-run-riot.md",
    3: "03-illusion-of-control.md",
    4: "04-dichotomy-of-control.md",
    5: "05-putting-down-the-gavel.md",
    6: "06-debt-you-keep-paying.md",
    7: "07-honest-inventory.md",
    8: "08-carried-by-something-you-didnt-create.md",
    9: "09-where-two-or-three-are-gathered.md",
    10: "10-build-it-before-you-need-it.md",
    11: "11-when-it-doesnt-hold.md",
    12: "12-life-on-lifes-terms.md",
}


def near(x, target):
    return abs(x - target) < 3


def span_text(s):
    t = s["text"]
    if "Italic" in s["font"] and "Bold" not in s["font"] and t.strip():
        lead = t[: len(t) - len(t.lstrip())]
        trail = t[len(t.rstrip()) :]
        return f"{lead}*{t.strip()}*{trail}"
    return t


def join(a, b):
    if not a:
        return b
    if a.endswith((" ", " ")):
        return a + b
    if a.endswith("-") and not a.endswith(" -"):
        return a + b
    return a + " " + b


def clean(t):
    t = re.sub(r"[  ]+", " ", t).strip()
    t = re.sub(r"\*(\s+)\*", r"\1", t)  # "*a* *b*" -> "*a b*"
    t = re.sub(r"(\d)– (\d)", r"\1–\2", t)  # number ranges broken across lines
    return t


# ---- flatten to lines ------------------------------------------------------
lines = []
for page in fitz.open(SRC):
    blocks = page.get_text("dict")["blocks"]
    xs = [l["bbox"][0] for b in blocks for l in b.get("lines", []) if l["spans"] and l["spans"][0]["size"] < 12]
    margin = min(xs) if xs else 0
    for b in blocks:
        for l in b.get("lines", []):
            spans = [s for s in l["spans"] if s["text"].strip()]
            if not spans:
                continue
            raw = "".join(s["text"] for s in l["spans"])
            if l["bbox"][1] > 600 and re.fullmatch(r"\s*[0-9ivxlc]+\s*", raw):
                continue  # page number
            lines.append(
                {
                    "page": page.number,
                    "block": b["number"],
                    "x": l["bbox"][0] - margin,
                    "size": round(spans[0]["size"], 1),
                    "font": spans[0]["font"],
                    "raw": raw,
                    "text": "".join(span_text(s) for s in l["spans"]),
                    "bold_num": "Bold" in spans[0]["font"]
                    and re.match(r"\d+\.(\s|$)", spans[0]["text"]) is not None,
                }
            )

# ---- state machine ---------------------------------------------------------
files = {}
order = []
current = None
started = in_toc = in_notes = in_bibliography = False
prev_line = None


def use(name):
    global current
    if name not in files:
        files[name] = []
        order.append(name)
    current = files[name]


def last():
    return current[-1] if current else None


def add(t, text):
    current.append({"t": t, "text": text})


for i, ln in enumerate(lines):
    nxt = lines[i + 1] if i + 1 < len(lines) else None
    size, font, x = ln["size"], ln["font"], ln["x"]
    same_block = prev_line and prev_line["page"] == ln["page"] and prev_line["block"] == ln["block"]
    p = prev_line
    prev_line = ln

    if size == 18.0:
        in_toc = ln["raw"].strip().startswith("Table of Contents")
        if ln["raw"].strip().startswith("Sources and Notes"):
            in_notes = True
            use("13-sources-and-notes.md")
        continue

    if size == 17.0:
        in_toc = False
        started = True
        if p and p["size"] == 17.0 and last() and last()["t"] == "title":
            last()["text"] = join(last()["text"], ln["raw"])
        else:
            m = re.match(r"Chapter (\d+):", ln["raw"])
            use(CHAPTER_FILES[int(m.group(1))] if m else "00-front-matter.md")
            add("title", ln["raw"])
        continue

    if in_toc or not started:
        continue

    if size == 11.0 and "Italic" in font:
        add("h2", "Dedication")
        add("p", ln["raw"])
        continue

    if in_notes:
        if size == 12.0:
            in_bibliography = ln["raw"].strip() == "Bibliography"
            add("h2", ln["raw"])
        elif size == 9.0 and near(x, LEFT) and (
            re.match(r"\d+\.\s", ln["raw"]) or in_bibliography
        ):
            add("note", ln["text"])
        elif last() and last()["t"] in ("note", "p") and (size == 9.0 or near(x, LEFT)):
            last()["text"] = join(last()["text"], ln["text"])
        else:
            add("p", ln["text"])
        continue

    if size == 11.0 and "Bold" in font:
        if p and p["size"] == 11.0 and same_block and last()["t"] == "h2":
            last()["text"] = join(last()["text"], ln["raw"])
        else:
            add("h2", ln["raw"])
        continue

    if "BoldItalic" in font:
        add("h3", ln["raw"])
        continue

    prev = last()
    hanging_num = (
        near(x, LEFT)
        and re.match(r"\d+\.\s", ln["raw"])
        and ((nxt is not None and near(nxt["x"], INDENT)) or (prev and prev["t"] == "li"))
    )
    if ln["bold_num"] or hanging_num:
        add("li", ln["text"])
        continue

    if near(x, INDENT):
        # A hanging continuation of a numbered step, unless the next line
        # drops back to the margin (then this is a new indented paragraph).
        next_is_margin = (
            nxt is not None
            and nxt["size"] == 10.5
            and near(nxt["x"], LEFT)
            and not nxt["bold_num"]
            and not re.match(r"\d+\.\s", nxt["raw"])
        )
        if prev and prev["t"] == "li" and not next_is_margin:
            prev["text"] = join(prev["text"], ln["text"])
        else:
            add("p", ln["text"])
    elif prev and prev["t"] in ("p", "li"):
        prev["text"] = join(prev["text"], ln["text"])
    else:
        add("p", ln["text"])

# ---- write -----------------------------------------------------------------
os.makedirs(OUT, exist_ok=True)
for name in order:
    out = []
    for e in files[name]:
        t = clean(e["text"])
        if e["t"] == "title":
            # Chapter titles come from config/book.php; front-matter titles
            # (About the Author, Introduction) are kept as section headings.
            if name == "00-front-matter.md":
                out.append("## " + t)
            else:
                print("  title:", t)
        elif e["t"] == "h2":
            out.append("## " + t)
        elif e["t"] == "h3":
            out.append("### " + t)
        else:
            out.append(t)
    with open(os.path.join(OUT, name), "w") as f:
        f.write("\n\n".join(out) + "\n")
    print(name, len(files[name]))
