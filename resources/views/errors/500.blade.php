<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Something went wrong — Stoic Recovery</title>
        <style>
            :root {
                --site-bg: #f0eff6;
                --site-ink: #1e1f2e;
                --site-ink-soft: #4e5069;
                --site-ink-faint: #8a8ca6;
                --site-accent: #4c57c4;
                --site-accent-ink: #ffffff;
                --site-line: #d9d6e8;
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --site-bg: #0b0c14;
                    --site-ink: #e7e8f3;
                    --site-ink-soft: #a7a9c4;
                    --site-ink-faint: #676a8a;
                    --site-accent: #8b97f5;
                    --site-accent-ink: #10111c;
                    --site-line: #23243a;
                }
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 22px;
                padding: 40px 24px;
                background: var(--site-bg);
                color: var(--site-ink);
                font-family: Calibri, 'Segoe UI', Arial, sans-serif;
                text-align: center;
            }

            .eyebrow {
                font-size: 15px;
                color: var(--site-ink-faint);
            }

            h1 {
                margin: 0;
                font-family: Georgia, 'Iowan Old Style', serif;
                font-size: 32px;
                font-weight: 500;
                max-width: 20ch;
            }

            p {
                margin: 0;
                max-width: 42ch;
                color: var(--site-ink-soft);
                line-height: 1.6;
            }

            a.home {
                margin-top: 8px;
                display: inline-block;
                border-radius: 6px;
                background: var(--site-accent);
                color: var(--site-accent-ink);
                padding: 13px 26px;
                font-size: 15.5px;
                font-weight: 600;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <p class="eyebrow">Stoic Recovery</p>
        <h1>Something went wrong on our end.</h1>
        <p>It's not you — a fix is likely already on the way. Try again in a moment.</p>
        <a class="home" href="/">Back to home</a>
    </body>
</html>
