
    @import url('https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,500;6..72,600;6..72,700&family=Playfair+Display:wght@600;700;800&family=Source+Sans+Pro:wght@700&display=swap');

    .preview-scale-wrap {
        width: 100%;
        overflow: hidden;
        background: #f3f4f6;
        border-radius: 0.75rem;
        padding: 1rem;
        display: flex;
        justify-content: center;
    }
    .preview-stage {
        transform-origin: top left;
    }
    .preview-viewport {
        overflow: hidden;
        margin: 0 auto;
    }
    .carrossel-slide-live {
        --slide-bg: #f8fafc;
        --slide-bg-2: #e2e8f0;
        --slide-fg: #0f172a;
        --slide-muted: rgba(15, 23, 42, 0.62);
        --slide-accent: #2563eb;
        --slide-accent-soft: rgba(37, 99, 235, 0.14);
        --slide-ring: rgba(15, 23, 42, 0.08);
        --slide-glow: rgba(37, 99, 235, 0.18);

        width: 1080px;
        height: 1350px;
        box-sizing: border-box;
        padding: 200px 120px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        font-family: 'Newsreader', 'Georgia', 'Times New Roman', serif;
        flex-shrink: 0;
        color: var(--slide-fg);
        background:
            radial-gradient(1200px 700px at 0% 0%, var(--slide-glow), transparent 55%),
            radial-gradient(900px 600px at 100% 100%, var(--slide-accent-soft), transparent 50%),
            linear-gradient(165deg, var(--slide-bg) 0%, var(--slide-bg-2) 100%);
    }
    .carrossel-slide-live .slide-glow {
        position: absolute;
        border-radius: 50%;
        pointer-events: none;
    }
    .carrossel-slide-live .slide-glow-a {
        width: 420px;
        height: 420px;
        top: -120px;
        right: -80px;
        background: var(--slide-accent-soft);
    }
    .carrossel-slide-live .slide-glow-b {
        width: 360px;
        height: 360px;
        bottom: -100px;
        left: -90px;
        background: var(--slide-glow);
        opacity: 0.7;
    }
    .carrossel-slide-live .slide-ring {
        position: absolute;
        inset: 48px;
        border: 2px solid var(--slide-ring);
        border-radius: 36px;
        pointer-events: none;
    }
    .carrossel-slide-live .slide-top,
    .carrossel-slide-live .slide-body,
    .carrossel-slide-live .footer {
        position: relative;
        z-index: 1;
    }
    .carrossel-slide-live .slide-top {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 28px;
        min-height: 96px;
    }
    .carrossel-slide-live .badge {
        display: inline-flex;
        align-items: center;
        padding: 14px 28px;
        border-radius: 14px;
        font-family: 'Newsreader', 'Georgia', serif;
        font-size: 24px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        width: fit-content;
        background: var(--slide-accent);
        color: #fff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
    }
    .carrossel-slide-live .accent-bar {
        width: 96px;
        height: 6px;
        border-radius: 999px;
        background: var(--slide-accent);
    }
    .carrossel-slide-live .slide-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 36px;
        padding: 24px 0;
    }
    .carrossel-slide-live .titulo {
        font-family: 'Playfair Display', 'Georgia', 'Times New Roman', serif;
        font-size: 72px;
        line-height: 1.1;
        font-weight: 700;
        letter-spacing: -0.02em;
        margin: 0;
        max-width: 820px;
        word-break: break-word;
    }
    .carrossel-slide-live .texto {
        font-family: 'Newsreader', 'Georgia', 'Times New Roman', serif;
        font-size: 36px;
        line-height: 1.45;
        margin: 0;
        max-width: 780px;
        font-weight: 500;
        color: var(--slide-muted);
        white-space: pre-wrap;
        word-break: break-word;
    }
    .carrossel-slide-live .footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 28px;
        border-top: 2px solid var(--slide-ring);
    }
    .carrossel-slide-live .brand-wrap {
        display: inline-flex;
        align-items: center;
        gap: 14px;
    }
    .carrossel-slide-live .brand-dot {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--slide-accent);
        box-shadow: 0 0 0 6px var(--slide-accent-soft);
    }
    .carrossel-slide-live .brand {
        font-family: 'Source Sans Pro', 'Segoe UI', sans-serif;
        font-size: 30px;
        font-weight: 700;
        letter-spacing: -0.015em;
        line-height: 1;
    }
    .carrossel-slide-live .page {
        font-family: 'Newsreader', 'Georgia', serif;
        font-size: 26px;
        font-weight: 600;
        color: var(--slide-muted);
        letter-spacing: 0.04em;
    }
    .carrossel-slide-live .page-sep {
        opacity: 0.5;
        margin: 0 4px;
    }

    .modelo-organizacao {
        --slide-bg: #f8fbff; --slide-bg-2: #dbeafe; --slide-fg: #1e3a8a;
        --slide-muted: rgba(30, 58, 138, 0.72); --slide-accent: #2563eb;
        --slide-accent-soft: rgba(37, 99, 235, 0.16); --slide-ring: rgba(37, 99, 235, 0.14);
        --slide-glow: rgba(59, 130, 246, 0.22);
    }
    .modelo-marketing {
        --slide-bg: #fff7fb; --slide-bg-2: #fce7f3; --slide-fg: #9d174d;
        --slide-muted: rgba(157, 23, 77, 0.72); --slide-accent: #db2777;
        --slide-accent-soft: rgba(219, 39, 119, 0.14); --slide-ring: rgba(219, 39, 119, 0.14);
        --slide-glow: rgba(236, 72, 153, 0.18);
    }
    .modelo-dinheiro {
        --slide-bg: #f3fdf8; --slide-bg-2: #d1fae5; --slide-fg: #065f46;
        --slide-muted: rgba(6, 95, 70, 0.72); --slide-accent: #059669;
        --slide-accent-soft: rgba(5, 150, 105, 0.14); --slide-ring: rgba(5, 150, 105, 0.14);
        --slide-glow: rgba(16, 185, 129, 0.18);
    }
    .modelo-cliente {
        --slide-bg: #f7f8ff; --slide-bg-2: #e0e7ff; --slide-fg: #312e81;
        --slide-muted: rgba(49, 46, 129, 0.7); --slide-accent: #4f46e5;
        --slide-accent-soft: rgba(79, 70, 229, 0.14); --slide-ring: rgba(79, 70, 229, 0.14);
        --slide-glow: rgba(99, 102, 241, 0.18);
    }
    .modelo-erros_comuns {
        --slide-bg: #fff8f9; --slide-bg-2: #ffe4e6; --slide-fg: #9f1239;
        --slide-muted: rgba(159, 18, 57, 0.72); --slide-accent: #e11d48;
        --slide-accent-soft: rgba(225, 29, 72, 0.14); --slide-ring: rgba(225, 29, 72, 0.14);
        --slide-glow: rgba(244, 63, 94, 0.16);
    }
    .modelo-historia {
        --slide-bg: #fffaf5; --slide-bg-2: #ffedd5; --slide-fg: #9a3412;
        --slide-muted: rgba(154, 52, 18, 0.72); --slide-accent: #ea580c;
        --slide-accent-soft: rgba(234, 88, 12, 0.14); --slide-ring: rgba(234, 88, 12, 0.14);
        --slide-glow: rgba(249, 115, 22, 0.16);
    }
    .modelo-bastidor {
        --slide-bg: #111827; --slide-bg-2: #1f2937; --slide-fg: #f8fafc;
        --slide-muted: rgba(248, 250, 252, 0.72); --slide-accent: #38bdf8;
        --slide-accent-soft: rgba(56, 189, 248, 0.16); --slide-ring: rgba(248, 250, 252, 0.12);
        --slide-glow: rgba(56, 189, 248, 0.14);
    }
    .modelo-bastidor .badge { color: #0c4a6e; }
    .modelo-reflexao {
        --slide-bg: #faf8ff; --slide-bg-2: #ede9fe; --slide-fg: #5b21b6;
        --slide-muted: rgba(91, 33, 182, 0.72); --slide-accent: #7c3aed;
        --slide-accent-soft: rgba(124, 58, 237, 0.14); --slide-ring: rgba(124, 58, 237, 0.14);
        --slide-glow: rgba(139, 92, 246, 0.16);
    }
    .modelo-curiosidade {
        --slide-bg: #fffef5; --slide-bg-2: #fef08a; --slide-fg: #854d0e;
        --slide-muted: rgba(133, 77, 14, 0.72); --slide-accent: #ca8a04;
        --slide-accent-soft: rgba(202, 138, 4, 0.16); --slide-ring: rgba(202, 138, 4, 0.16);
        --slide-glow: rgba(234, 179, 8, 0.18);
    }

