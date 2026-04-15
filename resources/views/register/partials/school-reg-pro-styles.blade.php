<style>
    .school-reg-pro {
        --sr-accent: #16a34a;
        --sr-accent-soft: #22c55e;
        --sr-accent-h: #15803d;
        --sr-accent-l: rgba(34, 197, 94, 0.18);
        --sr-shadow: rgba(22, 163, 74, 0.32);
        --sr-shadow-hover: rgba(22, 163, 74, 0.38);
        position: relative;
        min-height: calc(100vh - 48px);
        padding: 0;
        overflow: hidden;
    }
    .school-reg-pro__ambient {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 90% 55% at 15% -5%, rgba(34, 197, 94, 0.12), transparent 52%),
            radial-gradient(ellipse 70% 50% at 92% 5%, rgba(86, 182, 31, 0.07), transparent 48%),
            linear-gradient(180deg, rgba(34, 197, 94, 0.06) 0%, rgba(34, 197, 94, 0) 55%, var(--bg) 100%);
        pointer-events: none;
    }
    .school-reg-pro__ambient::before {
        content: '';
        position: absolute;
        left: -200px;
        bottom: -320px;
        width: 560px;
        height: 560px;
        background: rgba(34, 197, 94, 0.14);
        border-radius: 55% 45% 40% 60% / 45% 55% 45% 55%;
        pointer-events: none;
    }
    .school-reg-pro__ambient::after {
        content: '';
        position: absolute;
        right: -120px;
        top: -160px;
        width: 420px;
        height: 400px;
        background: linear-gradient(135deg, rgba(108, 200, 64, 0.35) 0%, rgba(86, 182, 31, 0.22) 100%);
        border-radius: 58% 42% 43% 57% / 56% 43% 57% 44%;
        transform: rotate(6deg);
        pointer-events: none;
    }
    .school-reg-pro__wrap {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: clamp(24px, 4vw, 48px) clamp(16px, 3vw, 32px) 48px;
        display: grid;
        grid-template-columns: minmax(280px, 1fr) minmax(0, 520px);
        gap: clamp(24px, 4vw, 56px);
        align-items: start;
    }
    @media (max-width: 960px) {
        .school-reg-pro__wrap {
            grid-template-columns: 1fr;
            max-width: 560px;
        }
    }

    /* Hero column */
    .school-reg-pro__hero {
        padding-top: 8px;
    }
    .school-reg-pro__hero-panel {
        max-width: 100%;
        padding: clamp(18px, 2.5vw, 26px);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        background: linear-gradient(
            165deg,
            rgba(255, 255, 255, 0.98) 0%,
            rgba(240, 253, 244, 0.45) 55%,
            rgba(255, 255, 255, 0.96) 100%
        );
    }
    .school-reg-pro__brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--ink);
        margin-bottom: 22px;
    }
    .school-reg-pro__brand-mark {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--surface);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        flex-shrink: 0;
    }
    .school-reg-pro__brand-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    .school-reg-pro__brand-text {
        font-weight: 800;
        font-size: 2.52rem;
        letter-spacing: -0.02em;
        line-height: 1.05;
    }
    .school-reg-pro__eyebrow {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--sr-accent-soft);
        margin-bottom: 10px;
    }
    .school-reg-pro__title {
        font-size: clamp(1.75rem, 4vw, 2.35rem);
        font-weight: 800;
        letter-spacing: -0.035em;
        line-height: 1.12;
        color: var(--ink);
        margin: 0 0 14px;
    }
    .school-reg-pro__lead {
        font-size: 0.95rem;
        line-height: 1.55;
        color: var(--ink-2);
        margin: 0 0 20px;
        max-width: 38ch;
    }
    .school-reg-pro__benefits {
        list-style: none;
        margin: 0 0 22px;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .school-reg-pro__benefits li {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 0.92rem;
        line-height: 1.5;
        color: var(--ink-2);
    }
    .school-reg-pro__benefits strong { color: var(--ink); font-weight: 700; }
    .school-reg-pro__benefit-icon {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--sr-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
    }
    .school-reg-pro__benefit-icon svg { width: 16px; height: 16px; }

    .school-reg-pro__timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
        padding-left: 4px;
        border-left: 2px solid var(--border);
    }
    .school-reg-pro__timeline-item {
        position: relative;
        padding: 0 0 12px 18px;
        margin-left: -7px;
    }
    .school-reg-pro__timeline-item:last-child { padding-bottom: 0; }
    .school-reg-pro__timeline-dot {
        position: absolute;
        left: -7px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--surface);
        border: 2px solid var(--border-2);
    }
    .school-reg-pro__timeline-item.is-active .school-reg-pro__timeline-dot {
        background: var(--sr-accent);
        border-color: var(--sr-accent);
        box-shadow: 0 0 0 4px var(--sr-accent-l);
    }
    .school-reg-pro__timeline-item.is-done .school-reg-pro__timeline-dot {
        background: var(--sr-accent);
        border-color: var(--sr-accent);
        opacity: 0.55;
    }
    .school-reg-pro__timeline-item.is-done .school-reg-pro__timeline-label {
        color: var(--ink-2);
        font-weight: 600;
    }
    .school-reg-pro__timeline-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--muted);
    }
    .school-reg-pro__timeline-item.is-active .school-reg-pro__timeline-label {
        color: var(--ink);
    }

    /* Panel / form â€” single compact sheet */
    .school-reg-pro__panel {
        background: transparent;
        border-radius: 0;
        border: none;
        box-shadow: none;
        padding: 0;
    }
    .school-reg-pro__sheet {
        background: var(--surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-lg);
        padding: 20px 20px 18px;
    }
    .school-reg-pro__sheet-head {
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }
    .school-reg-pro__sheet .school-reg-pro__alert + .school-reg-pro__sheet-head {
        margin-top: 0;
    }
    .school-reg-pro__sheet-title {
        margin: 0 0 4px;
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--ink);
    }
    .school-reg-pro__sheet-lead {
        margin: 0;
        font-size: 0.84rem;
        color: var(--muted);
        line-height: 1.45;
    }
    .school-reg-pro__step-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }
    .school-reg-pro__step-pill {
        display: inline-flex;
        align-items: center;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 6px 11px;
        border-radius: 999px;
        border: 1px solid var(--border);
        color: var(--muted);
        background: var(--surface-2);
        transition: border-color 0.2s, color 0.2s, background 0.2s;
    }
    .school-reg-pro__step-pill.is-active {
        border-color: var(--sr-accent);
        color: var(--sr-accent-h);
        background: var(--sr-accent-l);
    }
    .school-reg-pro__steps-viewport {
        position: relative;
        overflow: hidden;
    }
    .school-reg-pro__step.is-hidden {
        display: none !important;
    }
    .school-reg-pro__step.school-reg-pro__step--out {
        animation: sr-step-out 0.32s cubic-bezier(0.33, 1, 0.68, 1) forwards;
        pointer-events: none;
    }
    .school-reg-pro__step.school-reg-pro__step--in {
        animation: sr-step-in 0.38s cubic-bezier(0.33, 1, 0.68, 1) forwards;
    }
    .school-reg-pro__step.school-reg-pro__step--out-rev {
        animation: sr-step-out-rev 0.32s cubic-bezier(0.33, 1, 0.68, 1) forwards;
        pointer-events: none;
    }
    .school-reg-pro__step.school-reg-pro__step--in-rev {
        animation: sr-step-in-rev 0.38s cubic-bezier(0.33, 1, 0.68, 1) forwards;
    }
    @keyframes sr-step-out {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-6px);
        }
    }
    @keyframes sr-step-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes sr-step-out-rev {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(8px);
        }
    }
    @keyframes sr-step-in-rev {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .school-reg-pro__step.school-reg-pro__step--out,
        .school-reg-pro__step.school-reg-pro__step--in,
        .school-reg-pro__step.school-reg-pro__step--out-rev,
        .school-reg-pro__step.school-reg-pro__step--in-rev {
            animation: none;
        }
    }
    .school-reg-pro__subhead {
        margin: 14px 0 8px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .school-reg-pro__subhead:first-of-type {
        margin-top: 0;
    }
    .school-reg-pro__sep {
        border: none;
        height: 1px;
        margin: 14px 0 12px;
        background: linear-gradient(90deg, transparent, var(--border), transparent);
    }
    .school-reg-pro__alert {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 10px 12px;
        border-radius: var(--radius);
        background: var(--red-l);
        border: 1px solid var(--red-l2);
        color: #991b1b;
        font-size: 0.86rem;
        margin: 0 0 14px;
        line-height: 1.45;
    }
    .school-reg-pro__alert svg { flex-shrink: 0; width: 18px; height: 18px; margin-top: 1px; }

    .school-reg-pro__form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .school-reg-pro__fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 14px;
    }
    .school-reg-pro__fields--tight {
        gap: 10px 12px;
    }
    .school-reg-pro__field--full { grid-column: 1 / -1; }
    .school-reg-pro__field label {
        display: block;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--ink-2);
        margin-bottom: 5px;
    }
    .school-reg-pro__opt {
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
        color: var(--muted);
        font-size: 0.75rem;
    }
    .school-reg-pro__field input,
    .school-reg-pro__field textarea {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--surface-2);
        color: var(--ink);
        font-size: 0.92rem;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .school-reg-pro__field textarea {
        min-height: 68px;
        resize: vertical;
        line-height: 1.45;
    }
    .school-reg-pro__field input::placeholder,
    .school-reg-pro__field textarea::placeholder {
        color: #94a3b8;
    }
    .school-reg-pro__field input:hover,
    .school-reg-pro__field textarea:hover {
        border-color: var(--border-2);
        background: var(--surface);
    }
    .school-reg-pro__field input:focus,
    .school-reg-pro__field textarea:focus {
        outline: none;
        border-color: var(--sr-accent);
        background: var(--surface);
        box-shadow: 0 0 0 3px var(--sr-accent-l);
    }
    .school-reg-pro__err {
        display: block;
        margin-top: 5px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--red);
    }
    .school-reg-pro__err--block { margin-top: 10px; }

    .school-reg-pro__empty {
        margin: 0;
        padding: 14px 16px;
        border-radius: var(--radius);
        background: var(--amber-l);
        border: 1px solid var(--amber-l2);
        color: #92400e;
        font-size: 0.9rem;
    }

    .school-reg-pro__plans {
        border: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .school-reg-pro__sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }
    .school-reg-pro__plan {
        position: relative;
        display: block;
        cursor: pointer;
        border-radius: var(--radius);
        border: 2px solid var(--border);
        background: var(--surface-2);
        padding: 10px 12px 10px 42px;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    .school-reg-pro__plan:hover {
        border-color: var(--border-2);
        background: var(--surface);
    }
    .school-reg-pro__plan.is-selected {
        border-color: var(--sr-accent);
        background: var(--sr-accent-l);
        box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.12);
    }
    .school-reg-pro__plan-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .school-reg-pro__plan-body {
        display: block;
    }
    .school-reg-pro__plan-top {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 4px;
    }
    .school-reg-pro__plan-name {
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--ink);
    }
    .school-reg-pro__plan-price {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--sr-accent);
        white-space: nowrap;
    }
    .school-reg-pro__plan-meta {
        font-size: 0.75rem;
        color: var(--muted);
        font-weight: 500;
    }
    .school-reg-pro__plan-check {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border-2);
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        color: transparent;
        transition: 0.2s;
    }
    .school-reg-pro__plan-check svg { width: 12px; height: 12px; }
    .school-reg-pro__plan.is-selected .school-reg-pro__plan-check {
        border-color: var(--sr-accent);
        background: var(--sr-accent);
        color: #fff;
    }

    .school-reg-pro__actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 14px;
        margin-top: 12px;
        border-top: 1px solid var(--border);
    }
    .school-reg-pro__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 18px;
        border-radius: var(--radius);
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        transition: transform 0.15s, box-shadow 0.2s, background 0.2s, border-color 0.2s;
    }
    .school-reg-pro__btn svg { width: 18px; height: 18px; }
    .school-reg-pro__btn--ghost {
        background: transparent;
        border-color: var(--border);
        color: var(--ink-2);
    }
    .school-reg-pro__btn--ghost:hover {
        background: var(--surface-2);
        border-color: var(--border-2);
    }
    .school-reg-pro__btn--primary {
        background: linear-gradient(135deg, var(--sr-accent) 0%, var(--sr-accent-h) 100%);
        color: #fff;
        border-color: var(--sr-accent-h);
        box-shadow: 0 8px 24px var(--sr-shadow);
    }
    .school-reg-pro__btn--primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px var(--sr-shadow-hover);
    }
    .school-reg-pro__btn--primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    .school-reg-pro__signin {
        text-align: center;
        margin-top: 14px;
        font-size: 0.88rem;
        color: var(--muted);
    }
    .school-reg-pro__signin a {
        color: var(--sr-accent);
        font-weight: 700;
        text-decoration: none;
    }
    .school-reg-pro__signin a:hover { text-decoration: underline; }

    @media (max-width: 640px) {
        .school-reg-pro__fields {
            grid-template-columns: 1fr;
        }
        .school-reg-pro__actions {
            flex-direction: column-reverse;
        }
        .school-reg-pro__btn {
            width: 100%;
        }
        .school-reg-pro__timeline { display: none; }
    }
</style>
